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
        // 创建奖品配置表
        Schema::create('fortune_wheel_prizes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('奖品名称');
            $table->string('type', 20)->comment('奖品类型：bonus,vip,medal,upload,download,invitation,nothing');
            $table->bigInteger('value')->default(0)->comment('奖品数值');
            $table->decimal('probability', 5, 2)->default(0)->comment('中奖概率(%)');
            $table->integer('quantity')->default(-1)->comment('奖品数量，-1表示无限制');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->boolean('enabled')->default(true)->comment('是否启用');
            $table->timestamps();
            
            $table->index(['enabled', 'sort_order']);
        });

        // 创建抽奖记录表
        Schema::create('fortune_wheel_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->unsignedBigInteger('prize_id')->nullable()->comment('奖品ID');
            $table->boolean('is_win')->default(false)->comment('是否中奖');
            $table->bigInteger('cost_bonus')->default(0)->comment('消耗的魔力');
            $table->text('prize_data')->nullable()->comment('奖品数据JSON');
            $table->string('ip', 45)->nullable()->comment('IP地址');
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['is_win', 'created_at']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('prize_id')->references('id')->on('fortune_wheel_prizes')->onDelete('set null');
        });

        // 创建用户抽奖统计表
        Schema::create('fortune_wheel_user_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->date('date')->comment('日期');
            $table->integer('draw_count')->default(0)->comment('抽奖次数');
            $table->integer('win_count')->default(0)->comment('中奖次数');
            $table->bigInteger('total_cost')->default(0)->comment('总消耗魔力');
            $table->timestamps();
            
            $table->unique(['user_id', 'date']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fortune_wheel_user_stats');
        Schema::dropIfExists('fortune_wheel_records');
        Schema::dropIfExists('fortune_wheel_prizes');
    }
};
