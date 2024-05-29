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

namespace Nwidart\Modules\Support\Macros;

class MacrosRegister
{
    public function __construct(
        protected Blueprint  $blueprint,
        protected Collection $collection,
        protected Builder    $builder
    ) {
    }

    /**
     * macros boot
     */
    public function boot(): void
    {
        $this->blueprint->boot();
        $this->collection->boot();
        $this->builder->boot();
    }
}
