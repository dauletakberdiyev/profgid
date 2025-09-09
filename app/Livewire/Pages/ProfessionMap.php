<?php

namespace App\Livewire\Pages;

use App\Models\Profession;
use App\Models\User;
use Livewire\Component;
use App\Models\Sphere;
use Illuminate\Support\Facades\Auth;

class ProfessionMap extends Component
{
    public $search = '';
    public $sortBy = 'sort_order';
    public $sortDirection = 'asc';
    public $showInactive = false;
    public $showModal = false;
    public $selectedSphere = null;
    public $expandedProfessions = []; // Массив ID раскрытых профессий в модалке
    public $expandedSpheres = []; // Массив ID раскрытых сфер в аккордеоне
    public $showProfessionModal = false;
    public $selectedProfession = null;

    public function updatedSearch()
    {
        // This will trigger a re-render when search is updated
    }

    public function showSphereInfo($sphereId)
    {
        $sphere = Sphere::with(['professions' => function($query) {
            if (!$this->showInactive) {
                $query->where('is_active', true);
            }
            $query->orderBy('name');
        }])->find($sphereId);

        if ($sphere) {
            $sphere->loadedProfessions = $sphere->professions;
            $this->selectedSphere = $sphere;
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedSphere = null;
        $this->expandedProfessions = []; // Сбрасываем раскрытые профессии при закрытии модалки
    }

    public function showProfessionInfo($professionId)
    {
        $profession = \App\Models\Profession::find($professionId);

        if ($profession) {
            $this->selectedProfession = $profession;
            $this->showProfessionModal = true;
        }
    }

    public function closeProfessionModal()
    {
        $this->showProfessionModal = false;
        $this->selectedProfession = null;
    }

    public function toggleSphere($sphereId)
    {
        if (in_array($sphereId, $this->expandedSpheres)) {
            // Закрываем аккордеон сферы
            $this->expandedSpheres = array_filter($this->expandedSpheres, function($id) use ($sphereId) {
                return $id !== $sphereId;
            });
        } else {
            // Открываем аккордеон сферы
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
        /** @var User $user */
        $user = Auth::user();
        if (!$user) return;

        if ($user->favouriteSpheres()->where('sphere_id', $sphereId)->exists()) {
            $user->favouriteSpheres()->detach($sphereId); // remove
        } else {
            $user->favouriteSpheres()->attach($sphereId); // add
        }
    }

    public function likeProfession($professionId): void
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) return;

        if ($user->favouriteProfessions()->where('profession_id', $professionId)->exists()) {
            $user->favouriteProfessions()->detach($professionId); // remove
        } else {
            $user->favouriteProfessions()->attach($professionId); // add
        }
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
            ->with(['professions' => function($q) {
                if (!$this->showInactive) {
                    $q->where('is_active', true);
                }
                $q->orderBy('name');
            }])
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

        $query->orderBy('name');

        /** @var Sphere $spheres */
        $spheres = $query->get();

        // Получаем избранные сферы пользователя
        $userFavoriteSpheres = Auth::check() ? (Auth::user()->favouriteSpheres()->pluck('spheres.id')->toArray() ?? []) : [];
        $userFavoriteProfessions = Auth::check() ? (Auth::user()->favouriteProfessions()->pluck('professions.id')->toArray() ?? []) : [];

        // Добавляем информацию об избранности
        $spheres = $spheres->map(function(Sphere $sphere) use ($userFavoriteSpheres, $userFavoriteProfessions) {
            $sphere->is_favorite = in_array($sphere->id, $userFavoriteSpheres);
            $sphere->professions->map(function(Profession $profession) use ($userFavoriteProfessions) {
                $profession->is_favourite = in_array($profession->id, $userFavoriteProfessions);
            });
            return $sphere;
        });

        return view('livewire.pages.profession-map', [
            'spheres' => $spheres
        ]);
    }
}
