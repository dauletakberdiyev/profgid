<div class="min-h-screen bg-white py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-12 text-center">
            <h1 class="text-3xl font-light text-gray-900 mb-2">
                Личный профиль
            </h1>
            <p class="text-gray-500">
                Управление личной информацией и настройками аккаунта
            </p>
        </div>

        <!-- Profile Update Section -->
        <div class="mb-12">
            <h2 class="text-xl font-light text-gray-900 mb-8">Личная информация</h2>
            
            @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-50 border-l-2 border-green-400 rounded-r">
                <p class="text-green-700 text-sm">
                    {{ session('message') }}
                </p>
            </div>
            @endif

            <form wire:submit.prevent="updateProfile" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Имя</label>
                        <input wire:model="name" 
                               type="text" 
                               id="name" 
                               name="name" 
                               class="w-full px-0 py-3 text-gray-900 border-0 border-b border-gray-200 focus:border-blue-500 focus:ring-0 bg-transparent placeholder-gray-400 transition-colors duration-200"
                               placeholder="Введите ваше имя">
                        @error('name')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input wire:model="email" 
                               type="email" 
                               id="email" 
                               name="email" 
                               class="w-full px-0 py-3 text-gray-900 border-0 border-b border-gray-200 focus:border-blue-500 focus:ring-0 bg-transparent placeholder-gray-400 transition-colors duration-200"
                               placeholder="Введите ваш email">
                        @error('email')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-blue-600 rounded-lg transition-colors duration-200">
                        Сохранить изменения
                    </button>
                </div>
            </form>
        </div>

        <!-- Password Update Section -->
        <div class="mb-12">
            <h2 class="text-xl font-light text-gray-900 mb-8">Изменить пароль</h2>
            
            @if (session()->has('password_message'))
            <div class="mb-6 p-4 bg-green-50 border-l-2 border-green-400 rounded-r">
                <p class="text-green-700 text-sm">
                    {{ session('password_message') }}
                </p>
            </div>
            @endif

            <form wire:submit.prevent="updatePassword" class="space-y-6">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Текущий пароль</label>
                    <input wire:model="current_password" 
                           type="password" 
                           id="current_password" 
                           name="current_password" 
                           class="w-full px-0 py-3 text-gray-900 border-0 border-b border-gray-200 focus:border-blue-500 focus:ring-0 bg-transparent placeholder-gray-400 transition-colors duration-200"
                           placeholder="Введите текущий пароль">
                    @error('current_password')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Новый пароль</label>
                        <input wire:model="password" 
                               type="password" 
                               id="password" 
                               name="password" 
                               class="w-full px-0 py-3 text-gray-900 border-0 border-b border-gray-200 focus:border-blue-500 focus:ring-0 bg-transparent placeholder-gray-400 transition-colors duration-200"
                               placeholder="Введите новый пароль">
                        @error('password')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Подтверждение пароля</label>
                        <input wire:model="password_confirmation" 
                               type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="w-full px-0 py-3 text-gray-900 border-0 border-b border-gray-200 focus:border-blue-500 focus:ring-0 bg-transparent placeholder-gray-400 transition-colors duration-200"
                               placeholder="Повторите новый пароль">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-blue-600 rounded-lg transition-colors duration-200">
                        Обновить пароль
                    </button>
                </div>
            </form>
        </div>

        <!-- Account Activity Section -->
        <div class="mb-12">
            <h2 class="text-xl font-light text-gray-900 mb-8">Активность аккаунта</h2>
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-1">Последний вход</p>
                        <p class="text-sm text-gray-500">{{ now()->format('d.m.Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-1">IP-адрес</p>
                        <p class="text-sm text-gray-500">{{ request()->ip() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="border-t border-red-100 pt-8">
            <h2 class="text-xl font-light text-red-600 mb-8">Опасная зона</h2>
            <div class="bg-red-50 border border-red-100 rounded-lg p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-medium text-red-800 mb-1">Удалить аккаунт</h3>
                        <p class="text-sm text-red-600">
                            Все ваши данные будут удалены. Это действие необратимо.
                        </p>
                    </div>
                    <button type="button" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-white hover:bg-red-50 border border-red-200 hover:border-red-300 rounded-lg transition-colors duration-200">
                        Удалить аккаунт
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
