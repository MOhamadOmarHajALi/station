<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

if (! schema_has_table('users')) {
    $migration = new class extends Illuminate\Database\Migrations\Migration {
        public function up(): void {}
    };
}

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

// run migrations manually
Artisan::call('migrate:fresh', ['--seed' => false]);

$user = App\Models\User::factory()->create();
$request = Request::create('/login', 'POST', ['email' => $user->email, 'password' => 'password']);
$request->setLaravelSession($app['session']->driver());
$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $httpKernel->handle($request);
var_dump($response->getStatusCode());
var_dump(Auth::check());
var_dump(session()->getId());
var_dump($request->session()->all());
