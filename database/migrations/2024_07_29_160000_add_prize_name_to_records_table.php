<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fortune_wheel_records', function (Blueprint $table) {
            $table->string('prize_name')->nullable()->after('prize_id')->comment('中奖时的奖品名称');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fortune_wheel_records', function (Blueprint $table) {
            $table->dropColumn('prize_name');
        });
    }
}; 