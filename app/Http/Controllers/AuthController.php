<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login Admin
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.circles.index');
            }

            return redirect()->route('qcc.index');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login (Admin, Juri, & Pengguna Sistem)
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $login = $validated['login'];
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nik';
        $credentials = [
            $fieldType => $login,
            'password' => $validated['password'],
        ];

        $remember = (bool) ($request->boolean('remember'));

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            $request->session()->regenerate();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.circles.index'))->with('success', "Selamat datang kembali, {$user->name}!");
            }

            return redirect()->intended(route('qcc.index'))->with('success', "Selamat datang kembali, {$user->name}!");
        }

        return back()->withInput($request->only('login'))->with('error', 'Kombinasi Email/NIK dan Password tidak cocok.');
    }

    /**
     * Keluar dari sesi (Sign Out)
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('qcc.index')->with('success', 'Anda telah berhasil keluar (Sign Out).');
    }
}
