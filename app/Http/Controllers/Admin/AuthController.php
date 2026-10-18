<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Hiển thị trang đăng nhập quản trị viên.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Xử lý xác thực đăng nhập.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $remember = $request->boolean('remember');

        $user = \App\Models\User::where('email', $credentials['email'])
            ->where('role', 'admin')
            ->first();

        if ($user && !\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Mật khẩu quản trị viên không chính xác.',
            ])->onlyInput('email');
        }

        if ($user && !$user->isActive()) {
            return back()->withErrors([
                'email' => 'Tài khoản quản trị của bạn đã bị khóa hoặc ngừng kích hoạt.',
            ])->onlyInput('email');
        }

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'role' => 'admin', 'is_active' => true], $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Đăng nhập thành công! Chào mừng bạn quay trở lại.');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác, hoặc tài khoản không có quyền quản trị.',
        ])->onlyInput('email');
    }

    /**
     * Đăng xuất khỏi hệ thống quản trị.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Bạn đã đăng xuất an toàn.');
    }
}
