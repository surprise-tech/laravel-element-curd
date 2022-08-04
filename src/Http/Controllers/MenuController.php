<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/19
 */

namespace Wyz\ElementCurd\Http\Controllers;

use Wyz\ElementCurd\Enums\MenuKeepAlive;
use Wyz\ElementCurd\Enums\MenuShowLink;
use Wyz\ElementCurd\Form\Form;
use Wyz\ElementCurd\Grid\Grid;
use Wyz\ElementCurd\Model\Menu;
use Wyz\ElementCurd\Model\Role;

class MenuController extends AdminController
{
    protected function grid(): Grid
    {
        return Grid::make(Menu::query()->orderBy('rank'), function (Grid $grid) {
            $grid->column('id', '编号')->width('100px');
            $grid->column('title', '标题');
            $grid->column('path', '路由路径');
            $grid->column('name', '组件名');
            $grid->column('redirect', '重定向');
            $grid->column('icon', '图标')->icon();
            $grid->column('frame_src', '内嵌页面');

            $grid->enableTree()->disablePagination();
            $grid->createSize('500px');
            $grid->editSize('500px');

            $grid->disableShowBtn();
        });
    }

    protected function form(): Form
    {
        return Form::make(Menu::with(['roles']), function (Form $form, $model) {
            $form->text('title', '标题')->rules(['required']);
            $form->text('path', '路由路径')->rules(['required']);
            $form->text('name', '路由名');
            $form->text('redirect', '重定向');
            $form->text('icon', '图标');
            $form->text('frame_src', '内嵌frame');
            $form->multipleSelect('roles.*.id', '角色')->options(
                Role::query()->pluck('title', 'id')
            )->rules(['required']);
            $isEdit = $form->isEdit();
            $form->treeSelect('pid', '上级')->options(
                Menu::options('顶级', function ($query) use ($isEdit, $model) {
                    if ($isEdit) { // 编辑时排除禁用选项
                        $query->where('pid', '!=', $model->id)->where('id', '!=', $model->id);
                    }
                }))
                ->default(0)
                ->rules('required');
            $form->switch('show_link', '是否显示')->options([
                'off' => MenuShowLink::CLOSE,
                'on' => MenuShowLink::OPEN,
            ]);
            $form->switch('keep_alive', '是否缓存')->options([
                'off' => MenuKeepAlive::CLOSE,
                'on' => MenuKeepAlive::OPEN,
            ]);

            $form->saved(function (Form $form, $model, $withToData) {
                $model->roles()->sync($withToData['roles.*.id']);
            });

            $form->deleting(function (Form $form, $model) {
                // 删除前先删除角色关联
                $model->roles()->detach();
            });
        });
    }
}
