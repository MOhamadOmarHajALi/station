<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('role')->default('viewer')->after('email'); // admin | accountant | viewer
        });

        // أقدم مستخدم موجود (صاحب المشروع) يصبح مديرًا تلقائيًا
        $first = DB::table('users')->min('id');
        if ($first) {
            DB::table('users')->where('id', $first)->update(['role' => 'admin']);
        }
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('role'));
    }
};
