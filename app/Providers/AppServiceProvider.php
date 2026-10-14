<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // لا أدوار: كل من إيميله ضمن ADMIN_EMAILS هو مدير بكامل الصلاحيات
        $isAdmin = fn (User $u) => in_array(strtolower($u->email), config('station.admin_emails'), true);
        Gate::define('manage', $isAdmin);
        Gate::define('admin', $isAdmin);
    }
}
