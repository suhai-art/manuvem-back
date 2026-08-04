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
        Schema::create('budget_item', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('budget_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignUuid('item_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name_snapshot');
            $table->integer('unit_price');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_item');
    }
};
