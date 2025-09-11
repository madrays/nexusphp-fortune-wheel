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
            // 添加颜色字段
            if (!Schema::hasColumn('fortune_wheel_prizes', 'color')) {
                $table->string('color', 7)->default('#FF6B35')->comment('显示颜色')->after('enabled');
            }
            
            // 添加每日限制字段
            if (!Schema::hasColumn('fortune_wheel_prizes', 'daily_limit')) {
                $table->integer('daily_limit')->default(0)->comment('每日限制，0表示无限制')->after('color');
            }
            
            // 添加描述字段
            if (!Schema::hasColumn('fortune_wheel_prizes', 'description')) {
                $table->text('description')->nullable()->comment('奖品描述')->after('daily_limit');
            }
            
            // 修改quantity字段名为更通用的名称，但保持兼容性
            if (Schema::hasColumn('fortune_wheel_prizes', 'quantity') && !Schema::hasColumn('fortune_wheel_prizes', 'stock')) {
                $table->renameColumn('quantity', 'stock');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fortune_wheel_prizes', function (Blueprint $table) {
            $table->dropColumn(['color', 'daily_limit', 'description']);
            
            if (Schema::hasColumn('fortune_wheel_prizes', 'stock')) {
                $table->renameColumn('stock', 'quantity');
            }
        });
    }
};
