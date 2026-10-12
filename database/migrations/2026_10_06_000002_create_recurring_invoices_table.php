<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recurring_invoices', function (Blueprint $t) {
            $t->id();
            $t->enum('type', ['income', 'expense']);
            $t->string('title');
            $t->decimal('amount', 12, 2);
            $t->unsignedTinyInteger('day_of_month'); // 1-31
            $t->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('photographer_id')->nullable()->constrained()->nullOnDelete();
            $t->boolean('active')->default(true);
            $t->date('last_run_on')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('recurring_invoices'); }
};
