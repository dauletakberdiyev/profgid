<?php

namespace App\Livewire\Parts;

use Livewire\Component;
use Illuminate\Support\Facades\App;

class Header extends Component
{
    public $locale;

    public function setLocale($locale)
    {
        APP::setLocale($locale);
        session(['locale' => $locale]);
        $this->locale = $locale;
    }

    public function render()
    {
        return view('livewire.parts.header');
    }
}
