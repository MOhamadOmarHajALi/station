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
        // admin = كل شيء + إدارة المستخدمين | accountant = إدارة البيانات | viewer = قراءة فقط
        Gate::define('manage', fn (User $u) => in_array($u->role, ['admin', 'accountant']));
        Gate::define('admin', fn (User $u) => $u->role === 'admin');

        // أول مستخدم يسجّل في النظام يصبح مديرًا
        User::creating(function (User $u) {
            if (! User::query()->exists()) {
                $u->role = 'admin';
            }
        });
    }
}
