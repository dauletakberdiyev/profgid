<?php

namespace App\Livewire\Pages;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Profile extends Component
{
    public $name = '';
    public $email = '';
    public $current_password = '';
    public $password = '';
    public $password_confirmation = '';

    protected $rules = [
        'name' => 'required|min:2',
        'email' => 'required|email',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function updateProfile()
    {
        $this->validate();

        $user = Auth::user();

        // Check if email is changed and if it's unique
        if ($user->email !== $this->email) {
            $this->validate([
                'email' => 'unique:users,email'
            ]);
        }

        $user->name = $this->name;
        $user->email = $this->email;
        $user->save();

        session()->flash('message', 'Профиль успешно обновлен');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Текущий пароль неверный');
            return;
        }

        $user->password = Hash::make($this->password);
        $user->save();

        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';

        session()->flash('password_message', 'Пароль успешно обновлен');
    }

    public function render()
    {
        return view('livewire.pages.profile');
    }
}
