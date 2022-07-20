<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/19
 */

namespace Wyz\ElementCurd\Http\Controllers;

use Wyz\ElementCurd\Form\Form;
use Wyz\ElementCurd\Grid\Grid;
use Wyz\ElementCurd\Model\Permission;

class PermissionController extends AdminController
{
    /**
     * 表格.
     */
    protected function grid(): Grid
    {
        return Grid::make(new Permission(), function (Grid $grid) {
            $grid->model()->orderByDesc('created_at');
            $grid->tableTitle('权限管理');
            $grid->column('id', '编号')->width('100px');
            $grid->column('title', '权限名');
            $grid->column('name', '标识标识');
            $grid->column('http_method', '请求方法')
                ->display(function ($http_method, $item) {
                    return data_get($item, 'http_path') ? $http_method ?: ['ANY'] : null;
                })
                ->multipleLabel();
            $grid->column('http_path', '请求路径')
                ->display(function ($http_method) {
                    return $http_method ? explode("\n", $http_method) : [];
                })
                ->multipleLabel();

            // 设置详情参数
            $grid->disableShowBtn();

            // 设置编辑参数
            $grid->openEditDialog();
            $grid->editSize('400px');

            // 设置新增参数
            $grid->openCreateDialog();
            $grid->setCreateCache(false);
            $grid->createSize('400px');

            // 禁用分页
            $grid->disablePagination();

            // 开启树状表格
            $grid->enableTree();
        });
    }

    /**
     * 表单.
     */
    public function form(): Form
    {
        return Form::make(Permission::class, function (Form $form, $model) {
            $form->text('title', '权限名')->rules(['required']);
            $form->text('name', '标识')->rules(['required']);
            $form->multipleSelect('http_method', '请求方法')
                ->options($this->getHttpMethodsOptions());
            $form->textarea('http_path', '请求路径')->rules(['required']);
            $isEdit = $form->isEdit();
            $form->treeSelect('pid', '上级')
                ->options(Permission::options('顶级', function ($query) use ($isEdit, $model) {
                    if ($isEdit) { // 编辑时排除禁用选项
                        $query->where('pid', '!=', $model->id)->where('id', '!=', $model->id);
                    }
                }))
                ->default(0)
                ->rules('required');
        });
    }

    /**
     * http_method 选项.
     */
    private function getHttpMethodsOptions(): array
    {
        $httpMethods = [
           'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD',
        ];

        return array_combine($httpMethods, $httpMethods);
    }
}
