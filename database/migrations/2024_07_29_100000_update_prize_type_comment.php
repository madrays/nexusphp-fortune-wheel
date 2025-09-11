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
            $newComment = '奖品类型: bonus,upload,vip_days,rainbow_id_days,invite_temp,invite_perm,medal,rename_card,nothing';
            $table->string('type', 20)->comment($newComment)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fortune_wheel_prizes', function (Blueprint $table) {
            $oldComment = '奖品类型：bonus,vip,medal,upload,download,invitation,nothing';
            $table->string('type', 20)->comment($oldComment)->change();
        });
    }
}; 