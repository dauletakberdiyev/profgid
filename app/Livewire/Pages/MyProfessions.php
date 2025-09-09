<?php

namespace App\Livewire\Pages;

use App\Models\User;
use Livewire\Component;
use App\Models\Profession;
use Illuminate\Support\Facades\Auth;

class MyProfessions extends Component
{
    public $favoriteProfessions = [];
    public $expandedProfessions = [];

    public function mount()
    {
        $this->loadFavoriteProfessions();
    }

    public function toggleExpanded($professionId)
    {
        if (in_array($professionId, $this->expandedProfessions)) {
            $this->expandedProfessions = array_filter($this->expandedProfessions, function($id) use ($professionId) {
                return $id !== $professionId;
            });
        } else {
            $this->expandedProfessions[] = $professionId;
        }
    }

    public function loadFavoriteProfessions()
    {
        /** @var User $user */
        $user = Auth::user();

        $this->favoriteProfessions = $user->favouriteProfessions->load('talents');
    }

    public function removeProfession($professionId)
    {
        /** @var User $user */
        $user = Auth::user();
        $user->favouriteProfessions()->detach($professionId);
        $this->loadFavoriteProfessions();
    }

    public function render()
    {
        return view('livewire.pages.my-professions');
    }
}
