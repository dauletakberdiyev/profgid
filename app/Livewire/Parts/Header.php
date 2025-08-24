<?php

namespace App\Livewire\Parts;

use App\Models\Profession;
use App\Models\Sphere;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\App;

class Header extends Component
{
    public $locale;
    public $userProfessions = [];
    public $userSpheres = [];

    public function mount()
    {
        if (Auth::check()) {
            $this->loadUserProfessions();
            $this->loadUserSpheres();
        }
    }

    public function loadUserProfessions()
    {
        $user = Auth::user();
        $this->userProfessions = $user->favorite_professions ?? [];
    }

    public function loadUserSpheres()
    {
        $user = Auth::user();
        $this->userSpheres = $user->favorite_spheres ?? [];
    }

    public function getUserFavoriteProfessions()
    {
        if (empty($this->userProfessions)) {
            return collect();
        }

        return Profession::whereIn('id', $this->userProfessions)->get();
    }

    public function getUserFavoriteSpheres()
    {
        if (empty($this->userSpheres)) {
            return collect();
        }

        return Sphere::whereIn('id', $this->userSpheres)->get();
    }

    public function setLocale($locale)
    {
        APP::setLocale($locale);
        session(['locale' => $locale]);
        $this->locale = $locale;
    }

    public function render()
    {
        return view('livewire.parts.header', [
            'favoriteProfessions' => $this->getUserFavoriteProfessions(),
            'favoriteSpheres' => $this->getUserFavoriteSpheres(),
        ]);
    }
}
