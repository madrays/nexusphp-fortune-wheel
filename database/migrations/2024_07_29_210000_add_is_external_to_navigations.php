<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('navigations', function (Blueprint $table) {
            $table->boolean('is_external')->default(false)->after('url')->comment('是否为外链');
        });
    }

    public function down(): void
    {
        Schema::table('navigations', function (Blueprint $table) {
            $table->dropColumn('is_external');
        });
    }
}; 