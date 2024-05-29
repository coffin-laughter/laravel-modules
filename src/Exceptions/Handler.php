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

namespace Nwidart\Modules\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Nwidart\Modules\Enums\Code;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    /**
     * 异常类型及其相应自定义日志级别的列表。
     * @var array<int, class-string<\Throwable>>
     */
    protected $levels = [
        //
    ];

    /**
     * 未报告的异常类型列表。
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * 在出现验证异常时不会闪存到会话中的输入列表。
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * 为应用程序注册异常处理回调。
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午3:20
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * @param            $request
     * @param \Throwable $e
     * @return JsonResponse|Response
     * @throws \Throwable
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午3:20
     */
    public function render($request, \Throwable $e): JsonResponse|Response
    {
        $message = $e->getMessage();

        if (method_exists($e, 'getStatusCode')) {
            if ($e->getStatusCode() == Response::HTTP_NOT_FOUND) {
                $message = '路由未找到或未注册';
            }
        }

        $e = new FailedException($message ?: 'Server Error', $e instanceof CoffinException ? $e->getCode() : Code::FAILED);

        $response = parent::render($request, $e);

        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Methods', '*');
        $response->header('Access-Control-Allow-Headers', '*');

        return $response;
    }
}
