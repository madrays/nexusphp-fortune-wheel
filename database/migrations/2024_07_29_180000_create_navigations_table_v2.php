<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $tableName = 'navigations';

    public function up()
    {
        if (Schema::hasTable($this->tableName)) {
            return;
        }

        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('url');
            $table->integer('parent_id')->default(0)->index();
            $table->integer('sort_order')->default(0);
            $table->boolean('new_tab')->default(false);
            $table->string('permission')->nullable()->comment('Required permission to view');
            $table->timestamps();
        });

        $this->seedDefaults();
    }

    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }

    private function seedDefaults()
    {
        $defaultItems = [
            ['name' => '首页', 'url' => 'index.php', 'sort_order' => 10, 'permission' => null],
            ['name' => '论坛', 'url' => 'forums.php', 'sort_order' => 20, 'permission' => null],
            ['name' => '种子', 'url' => 'torrents.php', 'sort_order' => 30, 'permission' => null],
            ['name' => '候补', 'url' => 'offers.php', 'sort_order' => 40, 'permission' => 'offers'],
            ['name' => '求种', 'url' => 'viewrequests.php', 'sort_order' => 50, 'permission' => 'requests'],
            ['name' => '发布', 'url' => 'upload.php', 'sort_order' => 60, 'permission' => null],
            ['name' => '字幕', 'url' => 'subtitles.php', 'sort_order' => 70, 'permission' => null],
            ['name' => '幸运转盘', 'url' => 'fortune-wheel.php', 'sort_order' => 80, 'permission' => null],
            ['name' => 'TOP 10', 'url' => 'topten.php', 'sort_order' => 90, 'permission' => 'topten'],
            ['name' => '规则', 'url' => 'rules.php', 'sort_order' => 100, 'permission' => null],
            ['name' => 'FAQ', 'url' => 'faq.php', 'sort_order' => 110, 'permission' => null],
            ['name' => '管理中心', 'url' => 'staff.php', 'sort_order' => 120, 'permission' => 'staffmem'],
            ['name' => '日志', 'url' => 'log.php', 'sort_order' => 130, 'permission' => 'log'],
        ];

        foreach ($defaultItems as $item) {
            DB::table($this->tableName)->insert([
                'name' => $item['name'],
                'url' => $item['url'],
                'sort_order' => $item['sort_order'],
                'permission' => $item['permission'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}; 