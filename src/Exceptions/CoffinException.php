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

use Nwidart\Modules\Enums\Code;
use Nwidart\Modules\Enums\Enum;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class CoffinException extends HttpException
{
    protected $code = 0;

    /**
     * @param string $message
     * @param int|Code $code
     */
    public function __construct(string $message = '', int|Enum $code = 0)
    {
        if ($code instanceof Enum) {
            $code = $code->value();
        }

        if ($this->code instanceof Enum && ! $code) {
            $code = $this->code->value();
        }

        parent::__construct($this->statusCode(), $message ?: $this->message, null, [], $code);
    }

    /**
     * status code
     *
     * @return int
     */
    public function statusCode(): int
    {
        return 500;
    }

    /**
     * render
     *
     * @return array
     */
    public function render(): array
    {
        return [
            'code' => $this->code,

            'message' => $this->message,
        ];
    }
}
