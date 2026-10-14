<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portfolio_items', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('category'); // wedding | portrait | product | event
            $t->string('mime')->default('image/jpeg');
            $t->longText('image_data'); // الصورة base64 داخل قاعدة البيانات (ملفات Railway تُمسح عند كل نشر)
            $t->unsignedInteger('sort_order')->default(0);
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('portfolio_items'); }
};
