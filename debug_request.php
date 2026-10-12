<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);

$user = App\Models\User::factory()->create();
$request = Illuminate\Http\Request::create('/login', 'POST', [
    'email' => $user->email,
    'password' => 'password',
]);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request);

echo "status=" . $response->getStatusCode() . PHP_EOL;
echo "auth=" . (Illuminate\Support\Facades\Auth::check() ? 'true' : 'false') . PHP_EOL;
echo "session=" . json_encode($request->session()->all()) . PHP_EOL;

echo "location=" . $response->headers->get('Location', 'NONE') . PHP_EOL;
