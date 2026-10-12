<?php

namespace App\Http\Middleware;

use App\Models\ContactMessage;
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
            'unreadMessages' => fn () => $u && Gate::forUser($u)->allows('manage') ? ContactMessage::whereNull('read_at')->count() : 0,
            'currency' => config('station.currency'),
        ]);
    }
}
