<?php

namespace App\Livewire\Pages;

use App\Models\User;
use Livewire\Component;
use App\Models\Sphere;
use Illuminate\Support\Facades\Auth;

class MySpheres extends Component
{
    public $favoriteSpheres = [];
    public $expandedSpheres = [];

    public function mount()
    {
        $this->loadFavoriteSpheres();
    }

    public function toggleExpanded($sphereId)
    {
        if (in_array($sphereId, $this->expandedSpheres)) {
            $this->expandedSpheres = array_filter($this->expandedSpheres, function($id) use ($sphereId) {
                return $id !== $sphereId;
            });
        } else {
            $this->expandedSpheres[] = $sphereId;
        }
    }

    public function loadFavoriteSpheres()
    {
        /** @var User $user */
        $user = Auth::user();

        $this->favoriteSpheres = $user->favouriteSpheres->load(['talents', 'professions']);
    }

    public function removeSphere($sphereId)
    {
        /** @var User $user */
        $user = Auth::user();
        $user->favouriteSpheres()->detach($sphereId);
        $this->loadFavoriteSpheres();
    }

    public function render()
    {
        return view('livewire.pages.my-spheres');
    }
}
