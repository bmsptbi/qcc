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
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.criteria.index');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login Admin
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

            if ($user->role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withInput($request->only('login'))->with('error', 'Akses dibatasi. Hanya akun dengan hak akses Administrator yang dapat masuk ke panel ini.');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('admin.criteria.index'))->with('success', "Selamat datang kembali, {$user->name}!");
        }

        return back()->withInput($request->only('login'))->with('error', 'Kombinasi Email/NIK dan Password tidak cocok.');
    }

    /**
     * Keluar dari sesi Administrator (Sign Out)
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('qcc.index')->with('info', 'Anda telah berhasil keluar dari sesi Administrator.');
    }
}
