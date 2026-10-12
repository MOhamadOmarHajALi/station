<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::factory()->create();
var_dump($user->email);
var_dump($user->password);
var_dump(Illuminate\Support\Facades\Hash::check('password', $user->password));
var_dump(Illuminate\Support\Facades\Auth::attempt(['email' => $user->email, 'password' => 'password']));
var_dump(Illuminate\Support\Facades\Auth::check());
