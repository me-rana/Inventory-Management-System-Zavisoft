<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->smallInteger('quantity')->nullable()->default(1);
            $table->bigInteger('sell_price')->nullable()->default(12);
            $table->bigInteger('purchase_price')->nullable()->default(12);
            $table->string('image_path', 200)->nullable();
            $table->string('slug')->nullable();
            // Category foreign key
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
