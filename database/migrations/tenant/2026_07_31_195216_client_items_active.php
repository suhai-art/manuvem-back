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
        //
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('active')->default(true)->after('document');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->boolean('active')->default(true)->after('default_unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('active');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
};
