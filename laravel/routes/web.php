<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TodoController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('todos')->name('todos.')->group(function() {
        Route::get('/', [TodoController::class, 'index'])->name('index');
        Route::post('/', [TodoController::class, 'store'])->name('store');
        Route::put('/{todo}', [TodoController::class, 'update'])->name('update');
        Route::delete('/{todo}', [TodoController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';

// Test-only helper route: create a user and return the session cookie for E2E tests.
// This route is only available in local or testing environments to avoid exposing it in production.
if (app()->environment(['local', 'testing'])) {

    Route::post('/_e2e/create-user', function (Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        // Return the session cookie name and value so the test runner can authenticate the browser.
        $cookieName = config('session.cookie');
        $cookieValue = request()->cookie($cookieName) ?? session()->getId();

        return response()->json([
            'cookie' => [
                'name' => $cookieName,
                'value' => $cookieValue,
            ],
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
        ]);
    })->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
}
