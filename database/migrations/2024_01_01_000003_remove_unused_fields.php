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
        Schema::table('fortune_wheel_prizes', function (Blueprint $table) {
            // $table->dropColumn(['daily_limit', 'color', 'stock']);
            $table->dropColumn(['daily_limit', 'color']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fortune_wheel_prizes', function (Blueprint $table) {
            // $table->integer('daily_limit')->default(0)->after('sort_order');
            // $table->string('color', 7)->default('#FF6B35')->after('probability');
            // $table->integer('stock')->default(-1)->after('probability');
            $table->integer('daily_limit')->default(0)->after('sort_order');
            $table->string('color', 7)->default('#FF6B35')->after('probability');
        });
    }
};
