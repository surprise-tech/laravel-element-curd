element-pro CURD 快速开发包
-----
前端要结合配合[laravel-pure-admin](https://github.com/wyzheng1997/laravel-pure-admin)一起食用
# 快速开始
## 安装扩展包
```bash
composer require wyzheng/element-curd
```
## 发布资源文件
```bash
php artisan vendor:publish --provider=Wyz\ElementCurd\AdminServiceProvider
```
### 创建后台管理员表，并调整`config/admin.php`中的配置, 同时配置`config/auth.php`
`-m`参数是生成对应的迁移文件，迁移文件必须包含`name`,`username`,`password`,`avatar`四个字段
`Admin`模型 应使用`HasRoles`来扩展权限
```bash
php artisan make:model Admin -m
```
#### 迁移文件示例
```php
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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('管理员名称');
            $table->string('username')->unique()->comment('用户名');
            $table->string('password')->comment('密码');
            $table->string('avatar')->nullable()->comment('头像');
            $table->tinyInteger('status')->default(1)->comment('状态：0-禁用；1-正常');
            $table->timestamps();
        });

        Schema::create('admin_has_roles', function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->bigInteger('admin_id');
            $table->unique(['role_id', 'admin_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
        Schema::dropIfExists('admin_has_roles');
    }
};

```
#### 模型示例
```php
<?php

namespace App\Models;

use App\Enums\AdminStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Wyz\ElementCurd\Traits\HasRoles;

class Admin extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use HasRoles;

    protected $casts = [
        'status' => AdminStatus::class,
    ];

    protected $guarded = [];

    /**
     * 默认头像.
     * @return Attribute
     */
    public function avatar(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $value ?: config('admin.avatar'),
            set: fn ($value) => $value,
        );
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
```
### 初始化数据
```bash
php artisan migrate
php artisan db:seed --class=Wyz\ElementCurd\Seeder\RolesAndPermissionsSeeder
```

### 增删改查示例
```php
<?php

namespace App\Admin\Controllers;

use App\Enums\AdminStatus;
use App\Models\Admin;
use Wyz\ElementCurd\Form\Form;
use Wyz\ElementCurd\Grid\Filter;
use Wyz\ElementCurd\Grid\Grid;
use Wyz\ElementCurd\Http\Controllers\AdminController;
use Wyz\ElementCurd\Model\Role;

class AdminUserController extends AdminController
{
    /**
     * 表格.
     */
    protected function grid(): Grid
    {
        return Grid::make(Admin::with(['roles']), function (Grid $grid) {
            $grid->tableTitle('管理员');
            $grid->column('id', '编号')->width('60px');
            $grid->column('name', '名称');
            $grid->column('username', '用户名');
            $grid->column('avatar', '头像')->image();
            $grid->column('status', '状态')->label(AdminStatus::label());
            $grid->column('roles.*.title', '角色')->multipleLabel();
            $grid->column('created_at', '创建时间');

            $grid->filter(function (Filter $filter) {
                $filter->text('name', '名称', 'like');
                $filter->text('username', '用户名', 'like');
                $filter->select('status', '状态', '=')->options(AdminStatus::options());
                $filter->multipleSelect('role_id', '角色', ['in', 'roles.id'])->options(
                    Role::query()->pluck('name', 'id')
                );
            });

            $grid->disableShowBtn();

            // 设置编辑参数
            $grid->openEditDialog();
            $grid->editSize('500px');

            // 设置新增参数
            $grid->openCreateDialog();
            $grid->createSize('500px');
        });
    }

    /**
     * 表单.
     */
    public function form(): Form
    {
        return Form::make(Admin::with(['roles']), function (Form $form) {
            $form->text('name', '姓名')->rules(['required']);
            $form->text('username', '用户名')->rules(['required']);
            if ($form->isCreate()) {
                $form->password('password', '密码')->rules(['required']);
            }
            if ($form->isEdit()) {
                $form->password('password', '密码');
            }
            $form->switch('status', '状态')->options([
                'on' => AdminStatus::Normal,
                'off' => AdminStatus::Disabled,
            ])->rules(['required']);
            $form->multipleSelect('roles.*.id', '角色')
                ->options(Role::query()->pluck('title', 'id'))
                ->rules(['required']);

            // 保存前回调
            $form->saving(function (Form $form, $data) {
                $data['status'] = $data['status'] ? AdminStatus::Normal : AdminStatus::Disabled;
                if ($form->isEdit()) {
                    if ($data['password']) {
                        $data['password'] = bcrypt($data['password']);
                    } else {
                        unset($data['password']); // 删除password字段
                    }
                }
                if ($form->isCreate()) {
                    $data['password'] = bcrypt($data['password']);
                }

                return $data;
            });

            // 保存后回调
            $form->saved(function (Form $form, $model, $withToData) {
                // 更新角色
                $model->syncRoles(Role::query()->find($withToData['roles.*.id']));
            });
        });
    }
}
```
`AdminStatus.php`
```php
<?php

namespace App\Enums;

enum AdminStatus: int
{
    case Normal = 1;
    case Disabled = 0; // 禁用
    /**
     * label.
     */
    public static function label(): array
    {
        return [
            AdminStatus::Disabled->value => ['color' => 'danger', 'text' => '禁用'],
            AdminStatus::Normal->value => ['color' => 'success', 'text' => '正常'],
        ];
    }

    /**
     * select options.
     */
    public static function options(): array
    {
        return [
            AdminStatus::Disabled->value => '禁用',
            AdminStatus::Normal->value => '正常',
        ];
    }
}
```

### 路由配置
```php
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('admin.route.prefix'),
    'middleware' => config('admin.route.middleware')
], function (Router $router) {
    $router->resource('/demo', DemoController::class);
});
```

暂时没时间写文档，一些其他的用法自己摸索....

