<?php

namespace App\Livewire\Parts;

use Livewire\Component;
use App\Models\Profession;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserDropdown extends Component
{
    public $showProfessionMap = false;
    public $userProfessions = [];
    public $availableProfessions = [];

    public function mount()
    {
        if (Auth::check()) {
            $this->loadUserProfessions();
            $this->loadAvailableProfessions();
        }
    }

    public function loadUserProfessions()
    {
        $user = Auth::user();
        $this->userProfessions = $user->favorite_professions ?? [];
    }

    public function loadAvailableProfessions()
    {
        $this->availableProfessions = Profession::orderBy('name')->get();
    }

    public function toggleProfessionMap()
    {
        $this->showProfessionMap = !$this->showProfessionMap;
    }

    public function addProfessionToFavorites($professionId)
    {
        $user = Auth::user();
        $favoriteProfessions = $user->favorite_professions ?? [];
        
        if (!in_array($professionId, $favoriteProfessions)) {
            $favoriteProfessions[] = $professionId;
            $user->update(['favorite_professions' => $favoriteProfessions]);
            $this->loadUserProfessions();
            
            session()->flash('message', 'Профессия добавлена в избранное!');
        }
    }

    public function removeProfessionFromFavorites($professionId)
    {
        $user = Auth::user();
        $favoriteProfessions = $user->favorite_professions ?? [];
        
        $favoriteProfessions = array_filter($favoriteProfessions, function($id) use ($professionId) {
            return $id != $professionId;
        });
        
        $user->update(['favorite_professions' => array_values($favoriteProfessions)]);
        $this->loadUserProfessions();
        
        session()->flash('message', 'Профессия удалена из избранного!');
    }

    public function getUserFavoriteProfessions()
    {
        if (empty($this->userProfessions)) {
            return collect();
        }
        
        return Profession::whereIn('id', $this->userProfessions)->get();
    }

    public function render()
    {
        return view('livewire.parts.user-dropdown', [
            'favoriteProfessions' => $this->getUserFavoriteProfessions(),
        ]);
    }
}
