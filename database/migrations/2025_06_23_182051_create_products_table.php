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
        Schema::create('products', function (Blueprint $table) 
        {
            $table->id('product_id');
            $table->string('product_name');
            $table->string('product_category');
            $table->decimal('initial_price',11 ,2);
            $table->decimal('selling_price',11,2);
            $table->text('product_description');
            $table->string('product_image');
            $table->decimal('product_quantity',11,2);
            $table->foreignId('vendor_id')->constrained('users')
            ->onDelect('cascade');
            $table->softDeletes();
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
