<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('title')->nullable()->comment('标题');
            $table->bigInteger('pid')->default(0)->comment('上级ID');
            $table->json('http_method')->nullable()->comment('请求方法');
            $table->text('http_path')->nullable()->comment('请求路径');
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('title')->nullable()->comment('标题');
            $table->bigInteger('pid')->default(0)->comment('上级ID');
            $table->timestamps();
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->bigInteger('permission_id');
            $table->unique(['role_id', 'permission_id']);
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('菜单标题');
            $table->string('path')->comment('路由地址');
            $table->string('name')->nullable()->unique()->comment('路由名称');
            $table->string('redirect')->nullable()->comment('重定向');
            $table->string('icon')->nullable()->comment('图标');
            $table->tinyInteger('show_link')->nullable()->comment('是否显示');
            $table->integer('rank')->default(0)->comment('排序');
            $table->tinyInteger('keep_alive')->default(1)->comment('缓存');
            $table->string('frame_src')->nullable()->comment('内嵌iframe');
            $table->bigInteger('pid')->default(0)->comment('父级ID');
            $table->timestamps();
        });

        Schema::create('role_has_menus', function (Blueprint $table) {
            $table->bigInteger('menu_id');
            $table->bigInteger('role_id');
            $table->unique(['role_id', 'menu_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('permissions');
        Schema::drop('roles');
        Schema::drop('role_has_permissions');
        Schema::drop('menus');
        Schema::drop('role_has_menus');
    }
};
