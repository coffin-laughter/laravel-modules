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

namespace Nwidart\Modules\Base;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class CoffinController extends Controller
{
    /**
     * @param string|null $guard
     * @param string|null $field
     * @return mixed
     */
    protected function getLoginUser(string|null $guard = null, string|null $field = null): mixed
    {
        $user = Auth::guard($guard ?: getGuardName())->user();

        if (! $user) {
            throw new FailedException('登录失效, 请重新登录', Code::LOST_LOGIN);
        }

        if ($field) {
            return $user->getAttribute($field);
        }

        return $user;
    }

    /**
     * @param $guard
     * @return mixed
     */
    protected function getLoginUserId($guard = null): mixed
    {
        return $this->getLoginUser($guard, 'id');
    }
}
