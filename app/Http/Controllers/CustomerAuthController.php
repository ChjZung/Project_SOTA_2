<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
    public function loginForm() { return view('pages.customer-auth', ['register' => false]); }
    public function registerForm() { return view('pages.customer-auth', ['register' => true]); }

    public function login(Request $request)
    {
        $data = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        if (!Auth::attempt($data + ['is_active' => true], $request->boolean('remember'))) return back()->withErrors(['email' => 'Email or password is incorrect, or this account is inactive.'])->onlyInput('email');
        $request->session()->regenerate();
        return redirect()->intended(route('my-account'));
    }

    public function register(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:120', 'email' => 'required|email|unique:users,email|max:180', 'phone' => 'nullable|string|max:30', 'password' => 'required|string|min:6|confirmed']);
        $user = User::create($data + ['role' => 'customer', 'is_active' => true]);
        Auth::login($user); $request->session()->regenerate();
        return redirect()->route('my-account');
    }

    public function logout(Request $request)
    {
        Auth::logout(); $request->session()->regenerate();
        return redirect()->route('home');
    }
}
