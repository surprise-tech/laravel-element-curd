<?php
/**
 * @author wyzheng <wyzheng1997@163.com>
 * @date 2022/7/18
 */

namespace Wyz\ElementCurd;

use Illuminate\Support\Str;

class Help
{
    /**
     * 获取树状结构.
     */
    public static function getTreeData($data, $pid, $pidField, $keyField, \Closure|null $callback = null): array
    {
        $treeArray = [];
        foreach ($data as $item) {
            if (data_get($item, $pidField) == $pid) {
                $temp = $callback instanceof \Closure ? call_user_func($callback, $item) : $item;
                $children = self::getTreeData($data, data_get($item, $keyField), $pidField, $keyField, $callback);
                if (count($children)) {
                    data_set($temp, 'children', $children);
                }
                $treeArray[] = $temp;
            }
        }

        return $treeArray;
    }

    /**
     * 后台URL.
     */
    public static function adminBasePath(string $path = ''): string
    {
        $prefix = '/'.trim(config('admin.route.prefix'), '/');

        $prefix = ('/' == $prefix) ? '' : $prefix;

        $path = trim($path, '/');

        if (0 == strlen($path)) {
            return $prefix ?: '/';
        }

        return $prefix.'/'.$path;
    }

    /**
     * 匹配请求路径.
     *
     * @param $path
     */
    public static function matchRequestPath($path, ?string $current = null): bool|int
    {
        $request = request();
        $current = $current ?: $request->decodedPath();
        if (Str::contains($path, ':')) {
            [$methods, $path] = explode(':', $path);

            $methods = array_map('strtoupper', explode(',', $methods));

            if (!empty($methods) && !in_array($request->method(), $methods)) {
                return false;
            }
        }

        if (!Str::contains($path, '*')) {
            return $path === $current;
        }

        $path = str_replace(['*', '/'], ['([0-9a-z-_,])*', "\/"], $path);

        return preg_match("/$path/i", $current);
    }
}
