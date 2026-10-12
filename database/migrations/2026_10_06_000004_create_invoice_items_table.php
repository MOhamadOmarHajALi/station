<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $t->string('description');
            $t->decimal('quantity', 10, 2)->default(1);
            $t->decimal('price', 12, 2);
            $t->decimal('subtotal', 12, 2);
            $t->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('invoice_items'); }
};
