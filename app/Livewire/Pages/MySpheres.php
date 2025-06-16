<?php

namespace App\Livewire\Pages;

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
        $user = Auth::user();
        $sphereIds = $user->favorite_spheres ?? [];
        $this->favoriteSpheres = Sphere::whereIn('id', $sphereIds)->get();
    }

    public function removeSphere($sphereId)
    {
        $user = Auth::user();
        $favoriteSpheres = $user->favorite_spheres ?? [];
        
        $favoriteSpheres = array_filter($favoriteSpheres, function($id) use ($sphereId) {
            return $id != $sphereId;
        });
        
        $user->update(['favorite_spheres' => array_values($favoriteSpheres)]);
        $this->loadFavoriteSpheres();
        
        session()->flash('message', 'Сфера удалена из избранного!');
    }

    public function render()
    {
        return view('livewire.pages.my-spheres');
    }
}
