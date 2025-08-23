<?php

namespace App\Livewire\Pages;

use App\Models\Profession;
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
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $favoriteSpheres = $user->favorite_spheres ?? [];

        if (!in_array($sphereId, $favoriteSpheres)) {
            $favoriteSpheres[] = $sphereId;
            $user->update(['favorite_spheres' => $favoriteSpheres]);

            session()->flash('sphere-added', 'Сфера добавлена в избранное!');
        } else {
            // Удаляем из избранного
            $favoriteSpheres = array_filter($favoriteSpheres, function($id) use ($sphereId) {
                return $id != $sphereId;
            });
            $user->update(['favorite_spheres' => array_values($favoriteSpheres)]);

            session()->flash('sphere-added', 'Сфера удалена из избранного!');
        }
    }

    public function likeProfession($professionId): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $favoriteProfessions = $user->favorite_professions ?? [];

        if (!in_array($professionId, $favoriteProfessions)) {
            $favoriteProfessions[] = $professionId;
            $user->update(['favorite_professions' => $favoriteProfessions]);

            session()->flash('profession-added', __('messages.session.add.profession'));
        } else {
            // Удаляем из избранного
            $favoriteSpheres = array_filter($favoriteProfessions, function($id) use ($professionId) {
                return $id != $professionId;
            });
            $user->update(['favorite_professions' => array_values($favoriteSpheres)]);

            session()->flash('profession-added', __('messages.session.remove.profession'));
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
        $userFavoriteSpheres = Auth::check() ? (Auth::user()->favorite_spheres ?? []) : [];
        $userFavoriteProfessions = Auth::check() ? (Auth::user()->favorite_professions ?? []) : [];

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
