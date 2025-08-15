<?php

namespace App\Livewire\Pages;

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
        $user = Auth::user();
        $professionIds = $user->favorite_professions ?? [];
        $this->favoriteProfessions = Profession::with('talents')->whereIn('id', $professionIds)->get();
    }

    public function removeProfession($professionId)
    {
        $user = Auth::user();
        $favoriteProfessions = $user->favorite_professions ?? [];

        $favoriteProfessions = array_filter($favoriteProfessions, function($id) use ($professionId) {
            return $id != $professionId;
        });

        $user->update(['favorite_professions' => array_values($favoriteProfessions)]);
        $this->loadFavoriteProfessions();

        session()->flash('message', 'Профессия удалена из избранного!');
    }

    public function render()
    {
        return view('livewire.pages.my-professions');
    }
}
