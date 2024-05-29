<?php

declare(strict_types=1);

/**
 *  +-------------------------------------------------------------------------------------------
 *  | Module [ 花开不同赏，花落不同悲。欲问相思处，花开花落时。 ]
 *  +-------------------------------------------------------------------------------------------
 *  | This is not a free software, without any authorization is not allowed to use and spread.
 *  +-------------------------------------------------------------------------------------------
 *  | Copyright (c) 2006~2024 All rights reserved.
 *  +-------------------------------------------------------------------------------------------
 *  | @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
 *  +-------------------------------------------------------------------------------------------
 */

namespace Nwidart\Modules\Traits\Db;

trait ScopeTrait
{
    /**
     * 创建者
     * @param $query
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:43
     */
    public static function scopeCreator($query): void
    {
        $model = app(static::class);

        if (in_array($model->getCreatorIdColumn(), $model->getFillable())) {
            $userModel = app(getAuthUserModel());

            $query->addSelect([
                'creator' => $userModel->whereColumn($userModel->getKeyName(), $model->getTable() . '.' . $model->getCreatorIdColumn())->select('username')->limit(1),
            ]);
        }
    }
}
