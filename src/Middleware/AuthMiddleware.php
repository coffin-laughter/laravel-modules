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

namespace Nwidart\Modules\Middleware;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Nwidart\Modules\Enums\Code;
use Nwidart\Modules\Events\User as UserEvent;
use Nwidart\Modules\Exceptions\FailedException;
use Throwable;

class AuthMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        try {
            if (! $user = Auth::guard(getGuardName())->user()) {
                throw new AuthenticationException();
            }

            Event::dispatch(new UserEvent($user));

            return $next($request);
        } catch (Exception|Throwable $e) {
            throw new FailedException(Code::LOST_LOGIN->message() . ":{$e->getMessage()}", Code::LOST_LOGIN);
        }
    }
}
