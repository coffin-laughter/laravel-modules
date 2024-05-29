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

use Closure;

trait WithEvents
{
    /**
     * @var Closure|null
     */
    protected ?Closure $beforeGetList = null;

    /**
     * @var Closure|null
     */
    protected ?Closure $afterFirstBy = null;

    /**
     * @param Closure $closure
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:39
     */
    public function setBeforeGetList(Closure $closure): static
    {
        $this->beforeGetList = $closure;

        return $this;
    }

    /**
     * @param Closure $closure
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:39
     */
    public function setAfterFirstBy(Closure $closure): static
    {
        $this->afterFirstBy = $closure;

        return $this;
    }
}
