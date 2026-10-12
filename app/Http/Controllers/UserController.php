<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', ['users' => User::orderBy('id')->get(['id', 'name', 'email', 'role'])]);
    }

    public function update(Request $r, User $user)
    {
        $data = $r->validate(['role' => 'required|in:admin,accountant,viewer']);

        if ($user->id === $r->user()->id) {
            return back()->with('error', 'لا يمكنك تغيير صلاحيتك أنت.');
        }

        $user->forceFill(['role' => $data['role']])->save();
        return back()->with('success', 'تم تحديث صلاحية '.$user->name);
    }
}
