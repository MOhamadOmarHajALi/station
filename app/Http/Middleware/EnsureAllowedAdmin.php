<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EnsureAllowedAdmin
{
    /** يطرد أي حساب إيميله ليس ضمن القائمة المسموحة، حتى لو كان موجودًا في قاعدة البيانات */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! in_array(strtolower($user->email), config('station.admin_emails'), true)) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->flash('error', 'هذا الحساب غير مصرّح له بدخول النظام.');

            return $request->header('X-Inertia') ? Inertia::location('/login') : redirect('/login');
        }

        return $next($request);
    }
}
