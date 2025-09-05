<?php

namespace App\Livewire\Pages;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $school = '';
    public $class = '';

    protected $rules = [
        'name' => 'required|min:2',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
        'school' => 'nullable',
        'class' => 'nullable',
    ];

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'school' => $this->school,
            'class' => $this->class,
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.pages.register');
    }
}
