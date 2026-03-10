<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Jobs\SendWelcomeEmail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /** Muestra el formulario de registro. */
    public function show(): View
    {
        return view('auth.register');
    }

    /** Procesa el registro, crea el usuario admin y lo autentica. */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::Admin,
            'is_active' => true,
            'trial_ends_at' => now()->addDays(14),
        ]);

        Auth::login($user);

        SendWelcomeEmail::dispatch($user);

        return redirect('/admin');
    }
}
