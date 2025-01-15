<?php

namespace Faj1\Utils;

class ArrayUtils
{

    /**
     * 提供一个数组把数组内的数组和对象内容转为JSON
     * @param $array
     * @return mixed
     */
    public static function convertArrayFieldsToJson($array): mixed
    {
        foreach ($array as $key => $value) {
            // 检查当前值是否是数组
            if (is_array($value) or is_object($value)) {
                // 将数组字段转换为 JSON
                $array[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
        }
        return $array;
    }

    /**
     * 提供一个数组把KEY转为小写
     * @param $array
     * @return array
     */
    public static function array_keys_to_lowercase($array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $lowerKey = is_string($key) ? strtolower($key) : $key;
            $result[$lowerKey] = $value;
        }
        return $result;
    }


    /**
     * 递归展开一个数组,把数组内的数组挪到顶层
     * @param $data
     * @return array
     */
    public static function flattenArray($data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $lowerKey = strtolower($key); // 将键转换为小写
            if (is_array($value)) {
                // 处理值为数组的情况
                $flattened = self::flattenArray($value);
                foreach ($flattened as $subKey => $subValue) {
                    // 将数组中的每个元素与当前键组合
                    if (is_int($subKey)) {
                        // 如果是索引数组，组合成新键
                        $newKey = "{$lowerKey}_{$subKey}";
                        $result[$newKey] = $subValue;
                    } else {
                        $result[$subKey] = $subValue;
                    }
                }
            } else {
                // 直接将值添加到结果中
                $result[$lowerKey] = $value;
            }
        }
        ksort($result); // 按键排序
        return $result;
    }


    /**
     * 提供一个数组和一个规则,用于把数组内的KEY替换,值是原来的KEY,key是新的key
     * 例如:($Data, ['from' => 'contract_from_address', 'to' => 'contract_to_address', 'amount' => 'contract_to_amount','txid'=>'hash'])
     * @param $array
     * @param $rules
     * @return mixed
     */
    public  static function transformArray($array, $rules): mixed
    {
        $newItem = $array;
        foreach ($array as $oldKey => $item) {
            foreach ($rules as $newKey => $value) {
                if($oldKey === $value) {
                    $newItem[$newKey] = $item;
                    unset($newItem[$oldKey]);
                }
            }
        }
        return $newItem;
    }


    /**
     * php 提供2个数组，对比KEY，第一个数组里的key如果在第二个数组里不存在，则删除这个KEY，返回处理后的第一个数组
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function  filterArrayKeys(array $array1, array $array2): array {
        return array_intersect_key($array1, array_flip(array_keys($array2)));
    }


    /**
     * 提供一个数组递归获取其子节点
     * @param $data
     * @param $parentId
     * @param string $name
     * @param array $visited
     * @return array
     */
    public static function getChildren($data, $parentId, string $name = "pid", array &$visited = []): array
    {
        // 检查当前节点是否已访问过
        if (in_array($parentId, $visited)) {
            return []; // 遇到循环依赖时停止递归
        }
        // 标记当前节点为已访问
        $visited[] = $parentId;
        $result = [];
        foreach ($data as $item) {
            if ($item[$name] == $parentId) {
                // 找到子节点，递归查找其子节点
                $item['children'] = self::getChildren($data, $item['id'],$name, $visited);
                $result[] = $item;
            }
        }
        // 移除当前节点的访问标记（如果不需要全局影响）
        array_pop($visited);
        return $result;
    }




    /**
     * 根据给定的键数组移除第一个数组中的相同键
     *
     * @param array $array1 要进行移除操作的原始数组
     * @param array $keys   包含需要移除键的数组
     * @return array 返回移除指定键后的数组
     */
    public static function removeKeysFromArray(array $array1, array $keys): array {
        // 遍历要移除的键数组，如果键存在于原始数组中，则移除
        foreach ($keys as $key) {
            if (array_key_exists($key, $array1)) {
                unset($array1[$key]);
            }
        }
        return $array1;
    }









}
