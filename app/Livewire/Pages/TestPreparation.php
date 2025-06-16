<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TestPreparation extends Component
{
    public function startTest()
    {
        // Переходим к тесту
        return redirect()->route('test');
    }

    public function render()
    {
        return view('livewire.pages.test-preparation');
    }
}
