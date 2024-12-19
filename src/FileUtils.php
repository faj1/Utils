<?php

namespace Faj1\Utils;

class FileUtils
{
    /**
     * 检查并创建指定目录
     *
     * @param string $path 目标路径（需要检查或创建的目录）
     * @param int $permissions 文件权限（默认为 0755）
     * @return bool 返回 true 表示成功创建或已存在，false 表示失败
     */
    public function createDirectory(string $path, int $permissions = 0755): bool
    {
        // 判断目录是否已经存在
        if (is_dir($path)) {
            return true; // 目录已存在，直接返回 true
        }

        // 使用 mkdir 递归创建目录
        if (mkdir($path, $permissions, true)) {
            return true; // 成功创建目录
        }

        // 如果失败则返回 false
        return false;
    }


    /**
     * 判断目录是否存在,存在则进行删除处理
     * @param $dir
     * @return bool
     */
    public function deleteDirectory($dir): bool
    {
        // 判断目录是否存在
        if (is_dir($dir)) {
            // 打开目录
            $items = scandir($dir);
            foreach ($items as $item) {
                if ($item != '.' && $item != '..') {
                    $path = $dir . DIRECTORY_SEPARATOR . $item;
                    // 再次判断路径是否是目录
                    if (is_dir($path)) {
                        $this->deleteDirectory($path); // 递归删除子目录
                    } else {
                        unlink($path); // 删除文件
                    }
                }
            }
            // 删除空目录
            return rmdir($dir);
        } else {
            return false;
        }
    }



}
