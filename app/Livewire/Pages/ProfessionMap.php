<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Sphere;

class ProfessionMap extends Component
{
    public $search = '';
    public $sortBy = 'sort_order';
    public $sortDirection = 'asc';
    public $showInactive = false;
    public $expandedSpheres = []; // Массив ID раскрытых сфер
    public $expandedProfessions = []; // Массив ID раскрытых профессий для описаний

    public function updatedSearch()
    {
        // This will trigger a re-render when search is updated
    }

    public function toggleSphere($sphereId)
    {
        if (in_array($sphereId, $this->expandedSpheres)) {
            // Закрываем сферу
            $this->expandedSpheres = array_filter($this->expandedSpheres, function($id) use ($sphereId) {
                return $id !== $sphereId;
            });
        } else {
            // Открываем сферу
            $this->expandedSpheres[] = $sphereId;
        }
    }

    public function toggleProfessionDescription($professionId)
    {
        if (in_array($professionId, $this->expandedProfessions)) {
            // Закрываем описание профессии
            $this->expandedProfessions = array_filter($this->expandedProfessions, function($id) use ($professionId) {
                return $id !== $professionId;
            });
        } else {
            // Открываем описание профессии
            $this->expandedProfessions[] = $professionId;
        }
    }

    public function likeSphere($sphereId)
    {
        // TODO: Implement logic to add sphere to user favorites
        $this->dispatch('sphere-liked', ['sphereId' => $sphereId]);
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = Sphere::query()
            ->withCount(['professions' => function($q) {
                if (!$this->showInactive) {
                    $q->where('is_active', true);
                }
            }]);

        // Apply search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('name_kz', 'like', '%' . $this->search . '%')
                  ->orWhere('name_en', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if (!$this->showInactive) {
            $query->where('is_active', true);
        }

        // Apply sorting
        if ($this->sortBy === 'professions_count') {
            $query->orderBy('professions_count', $this->sortDirection);
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        // Add default ordering
        if ($this->sortBy !== 'sort_order') {
            $query->orderBy('sort_order');
        }
        if ($this->sortBy !== 'name') {
            $query->orderBy('name');
        }

        $spheres = $query->get();

        // Загружаем профессии только для раскрытых сфер
        if (!empty($this->expandedSpheres)) {
            $spheres = $spheres->map(function($sphere) {
                if (in_array($sphere->id, $this->expandedSpheres)) {
                    $professionsQuery = $sphere->professions();
                    if (!$this->showInactive) {
                        $professionsQuery->where('is_active', true);
                    }
                    $sphere->loadedProfessions = $professionsQuery->orderBy('name')->get();
                } else {
                    $sphere->loadedProfessions = collect();
                }
                return $sphere;
            });
        } else {
            $spheres = $spheres->map(function($sphere) {
                $sphere->loadedProfessions = collect();
                return $sphere;
            });
        }
            
        return view('livewire.pages.profession-map', [
            'spheres' => $spheres
        ]);
    }
}
