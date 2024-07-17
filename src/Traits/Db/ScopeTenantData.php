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

namespace Nwidart\Modules\Traits\Db;

use Illuminate\Support\Facades\Auth;

trait ScopeTenantData
{
    public function scopeTenantData($query)
    {
        $model = app(static::class);
        if (in_array($model->getTenantIdColumn(), $model->getFillable())) {
            $currenUser = Auth::guard(getGuardName())->user();
            if (! empty($currenUser)) {
                if ($currenUser->isSuperAdmin()) {
                    return $query;
                } else {
                    return $query->where($model->getTenantIdColumn(), $currenUser->tenant_id);
                }
            }
        }

        return $query;
    }
}
