<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Wyz\ElementCurd\Enums\FormMode;
use Wyz\ElementCurd\Exceptions\CurdCustomError;
use Wyz\ElementCurd\Form\Form;
use Wyz\ElementCurd\Grid\Grid;
use Wyz\ElementCurd\Show\Show;
use Wyz\ElementCurd\Traits\ApiResponse;

class AdminController extends Controller
{
    use ApiResponse;

    // 列表页面
    public function index(Request $request): JsonResponse
    {
        if ($request->has('_grid_configs_')) {
            return $this->sendData($this->grid()->render());
        } else {
            // 执行查询
            return $this->sendData($this->grid()->resource());
        }
    }

    // 详情页面
    public function show($id): JsonResponse
    {
        return $this->sendData($this->detail($id)->render());
    }

    // 创建页面
    public function create(): array
    {
        return $this->form()
            ->setMode(FormMode::MODE_CREATE)
            ->render();
    }

    // 编辑页面
    public function edit($id): array
    {
        return $this->form()
            ->setKey($id)
            ->setMode(FormMode::MODE_EDIT)
            ->render();
    }

    // 更新数据
    public function update($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->form()->setKey($id)
                ->setMode(FormMode::MODE_EDIT)
                ->save();
            DB::commit();
        } catch (CurdCustomError $exception) {
            DB::rollBack();

            return $this->failed($exception->getMessage());
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $this->success();
    }

    // 新增
    public function store(): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->form()
                ->setMode(FormMode::MODE_CREATE)
                ->save();
            DB::commit();
        } catch (CurdCustomError $exception) {
            DB::rollBack();

            return $this->failed($exception->getMessage());
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $this->success();
    }

    // 删除.
    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->form()->setMode(FormMode::MODE_DELETE)->setKey($id)->delete();
            DB::commit();
        } catch (CurdCustomError $exception) {
            DB::rollBack();

            return $this->failed($exception->getMessage());
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $this->success();
    }

    // 表单逻辑，子类重写
    protected function form(): Form|null
    {
        return null;
    }

    // 详情逻辑，子类重写
    protected function detail($id): Show|null
    {
        return null;
    }

    // 表格逻辑，子类重写
    protected function grid(): Grid|null
    {
        return null;
    }
}
