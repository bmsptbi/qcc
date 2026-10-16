<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'))->with('error', 'Silakan masuk dengan akun Administrator terlebih dahulu untuk mengakses menu ini.');
        }

        if ($request->user()->role !== 'admin') {
            return redirect()->route('qcc.index')->with('error', 'Akses ditolak. Anda tidak memiliki izin Administrator untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
