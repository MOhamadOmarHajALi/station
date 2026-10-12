<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $u = $request->user();

        return array_merge(parent::share($request), [
            'auth' => ['user' => $u ? $u->only('id', 'name', 'email', 'role') : null],
            'can' => [
                'manage' => $u ? Gate::forUser($u)->allows('manage') : false,
                'admin' => $u ? Gate::forUser($u)->allows('admin') : false,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'currency' => config('station.currency'),
        ]);
    }
}
