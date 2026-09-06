<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register', [
            'adminExists' => User::where('role', 'admin')->exists(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:admin,customer'],
            'birthday' => ['required', 'date', 'before_or_equal:today'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->max(24)->letters()->numbers()->symbols(),
            ],
        ]);

        if ($validated['role'] === 'admin' && User::where('role', 'admin')->exists()) {
            throw ValidationException::withMessages([
                'role' => 'The admin account already exists and cannot be registered again.',
            ]);
        }

        event(new Registered(User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'birthday' => $validated['birthday'],
            'password' => Hash::make($validated['password']),
        ])));

        return redirect()
            ->route('login')
            ->with('status', 'Registration complete. Please log in to continue.');
    }
}
