<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/19
 */

namespace Wyz\ElementCurd\Http\Controllers;

use Wyz\ElementCurd\Form\Form;
use Wyz\ElementCurd\Grid\Filter;
use Wyz\ElementCurd\Grid\Grid;
use Wyz\ElementCurd\Model\Permission;
use Wyz\ElementCurd\Model\Role;

class RoleController extends AdminController
{
    /**
     * 表格.
     */
    protected function grid(): Grid
    {
        return Grid::make(Role::with(['permissions']), function (Grid $grid) {
            $grid->tableTitle('角色管理');
            $grid->column('id', '编号')->width('60px');
            $grid->column('name', '角色标识');
            $grid->column('title', '角色名');
            $grid->column('permissions.*.title', '权限')->multipleLabel();
            $grid->column('created_at', '创建时间');

            $grid->filter(function (Filter $filter) {
                $filter->text('name', '角色名', 'like');
            });

            // 设置编辑参数
            $grid->openEditDialog();
            $grid->editSize('600px');

            // 设置新增参数
            $grid->openCreateDialog();
            $grid->createSize('600px');

            $grid->disableShowBtn();
        });
    }

    /**
     * 表单.
     */
    public function form(): Form
    {
        return Form::make(Role::with('permissions'), function (Form $form) {
            $form->text('name', '角色标识')->rules(['required']);
            $form->text('title', '角色名')->rules(['required']);
            $form->treeMultipleSelect('permissions.*.id', '权限')
                ->options(Permission::options())
                ->rules(['required']);
            $form->saving(function (Form $form, $data) {
                $data['pid'] = 0;

                return $data;
            });
            $form->saved(function (Form $form, $model, $withToData) {
                $model->permissions()->sync($withToData['permissions.*.id']);
            });
        });
    }
}
