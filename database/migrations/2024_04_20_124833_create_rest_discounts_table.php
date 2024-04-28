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
        Schema::create('rest_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variant_id');
            $table->unsignedBigInteger('rule_id');
            $table->integer('price');
            $table->integer('compare_at_price')->nullable();
            $table->enum('rule_status', ['on', 'off'])->default('on');
            $table->timestamps();

            $table->foreign('rule_id')->references('id')->on('rest_rules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rest_discounts');
    }
};
