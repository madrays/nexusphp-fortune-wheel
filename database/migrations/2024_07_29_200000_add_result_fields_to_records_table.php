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
        Schema::table('fortune_wheel_records', function (Blueprint $table) {
            $table->string('result_status', 50)->nullable()->comment('结果状态：awarded,compensated,extended,already_owned,compensated_high_class,already_owned_high_class,nothing');
            $table->string('result_value', 255)->nullable()->comment('结果数值');
            $table->string('result_unit', 50)->nullable()->comment('结果单位');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fortune_wheel_records', function (Blueprint $table) {
            $table->dropColumn(['result_status', 'result_value', 'result_unit']);
        });
    }
}; 