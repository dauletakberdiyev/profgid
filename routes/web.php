<?php

use App\Livewire\Pages\Home;
use App\Livewire\Pages\Login;
use App\Livewire\Pages\ForgotPassword;
use App\Livewire\Pages\ResetPassword;
use App\Livewire\Pages\Profile;
use App\Livewire\Pages\Register;
use App\Livewire\Pages\Settings;
use App\Livewire\Pages\TalentTest;
use App\Livewire\Pages\TalentTestResults;
use App\Livewire\Pages\ProfessionRecommendations;
use App\Livewire\Pages\MyProfessions;
use App\Livewire\Pages\MySpheres;
use App\Livewire\Pages\ProfessionMap;
use App\Livewire\Pages\TestHistory;
use App\Livewire\PaymentPage;
use App\Livewire\PaymentStatus;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Language Switcher Route
Route::get('/locale/{locale}', [\App\Http\Controllers\LocaleController::class, 'setLocale'])->name('locale.set');


Route::get('/', Home::class)->name('home');
Route::get('/about', function() {
    return view('livewire.pages.about');
})->name('about');

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');
Route::get('/forgot-password', ForgotPassword::class)->name('forgot-password');
Route::get('/reset-password/{token}', ResetPassword::class)->name('reset-password');

Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback']);

Route::post('/logout', function () {
    // auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', Profile::class)->name('profile');
    Route::get('/settings', Settings::class)->name('settings');
    Route::get('/test-preparation', \App\Livewire\Pages\TestPreparation::class)->name('test-preparation');
    Route::get('/test', TalentTest::class)->name('test');
    Route::get('/talent-test', TalentTest::class)->name('talent-test');
    Route::get('/test/results', TalentTestResults::class)->name('test.results');
    Route::get('/talent-test-results/{sessionId?}', TalentTestResults::class)->name('talent-test-results');
    Route::get('/profession-recommendations/{sessionId?}', ProfessionRecommendations::class)->name('profession-recommendations');
    Route::get('/my-professions', MyProfessions::class)->name('my-professions');
    Route::get('/my-spheres', MySpheres::class)->name('my-spheres');
    Route::get('/profession-map', ProfessionMap::class)->name('profession-map');
    Route::get('/test/history', TestHistory::class)->name('test.history');
    Route::get('/payment/{sessionId?}', PaymentPage::class)->name('payment');
    Route::get('/payment-status/{sessionId}/{plan}', PaymentStatus::class)->name('payment-status');
    
    // Profession management routes
    Route::post('/profession/add-to-favorites', [\App\Http\Controllers\ProfessionController::class, 'addToFavorites'])->name('profession.add-to-favorites');
    Route::post('/profession/remove-from-favorites', [\App\Http\Controllers\ProfessionController::class, 'removeFromFavorites'])->name('profession.remove-from-favorites');
});
