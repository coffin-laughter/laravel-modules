<?php

declare(strict_types=1);
/**
 *  +-------------------------------------------------------------------------------------------
 *  | Coffin [ 花开不同赏，花落不同悲。欲问相思处，花开花落时。 ]
 *  +-------------------------------------------------------------------------------------------
 *  | This is not a free software, without any authorization is not allowed to use and spread.
 *  +-------------------------------------------------------------------------------------------
 *  | Copyright (c) 2006~2024 All rights reserved.
 *  +-------------------------------------------------------------------------------------------
 *  | @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
 *  +-------------------------------------------------------------------------------------------
 */

namespace Nwidart\Modules\Support\Macros;

use Illuminate\Database\Eloquent\Builder as LaravelBuilder;
use Illuminate\Support\Str;

class Builder
{
    public function boot(): void
    {
        $this->whereLike();

        $this->quickSearch();

        $this->tree();
    }


    /**
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-07-17 上午10:25
     */
    public function quickSearch(): void
    {
        LaravelBuilder::macro(__FUNCTION__, function (array $params = []) {
            $params = array_merge(request()->all(), $params);

            if (!property_exists($this->model, 'searchable')) {
                return $this;
            }

            // filter null & empty string
            $params = array_filter($params, function ($value) {
                return (is_string($value) && strlen($value)) || is_numeric($value) || (is_array($value) && !empty($value));
            });

            $wheres = [];

            if (!empty($this->model->searchable)) {
                foreach ($this->model->searchable as $field => $op) {
                    // 临时变量
                    $_field = $field;
                    // contains alias
                    if (str_contains($field, '.')) {
                        [, $_field] = explode('.', $field);
                    }

                    if (isset($params[$_field]) && $searchValue = $params[$_field]) {
                        $operate = Str::of($op)->lower();
                        $value = $searchValue;
                        if ($operate->exactly('op')) {
                            $value = implode(',', $searchValue);
                        }

                        if ($operate->exactly('like')) {
                            $value = "%{$searchValue}%";
                        }

                        if ($operate->exactly('rlike')) {
                            $op = 'like';
                            $value = $searchValue . '%';
                        }

                        if ($operate->exactly('llike')) {
                            $op = 'like';
                            $value = '%' . $searchValue;
                        }

                        if (Str::of($_field)->endsWith('_at') || Str::of($_field)->endsWith('_time')) {
                            $value = is_string($searchValue) ? strtotime($searchValue) : $searchValue;
                        }

                        $wheres[] = [$field, strtolower($op), $value];
                    }
                }
            }

            // 组装 where 条件
            foreach ($wheres as $w) {
                [$field, $op, $value] = $w;
                if ($op == 'in') {
                    // in 操作的值必须是数组，所以 value 必须更改成 array
                    $this->whereIn($field, is_array($value) ? $value : explode(',', $value));
                } else {
                    $this->where($field, $op, $value);
                }
            }

            return $this;
        });
    }

    /**
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-07-17 上午10:25
     */
    public function tree(): void
    {
        LaravelBuilder::macro(__FUNCTION__, function (string $id, string $parentId, ...$fields) {
            $fields = array_merge([$id, $parentId], $fields);

            return $this->get($fields)->toTree(0, $parentId);
        });
    }

    /**
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-07-17 上午10:24
     */
    public function whereLike(): void
    {
        LaravelBuilder::macro(__FUNCTION__, function ($filed, $value) {
            return $this->where($filed, 'like', "%$value%");
        });
    }
}
