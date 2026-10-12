<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $t) {
            $t->id();
            $t->string('number')->unique();
            $t->enum('type', ['income', 'expense']); // income = واردات | expense = صادرات
            $t->date('invoice_date')->index();
            $t->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('photographer_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title')->nullable();
            $t->decimal('total', 12, 2)->default(0);
            $t->text('notes')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('invoices'); }
};
