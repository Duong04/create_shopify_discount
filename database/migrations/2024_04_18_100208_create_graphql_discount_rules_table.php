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
        Schema::create('graphql_discount_rules', function (Blueprint $table) {
            $table->id();
            $table->string('variant_id', 255);
            $table->integer('price');
            $table->integer('compare_at_price')->nullable();
            $table->enum('rule_status', ['on', 'off'])->default('on');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('graphql_discount_rules');
    }
};
