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

use Illuminate\Support\Facades\DB;

trait Trans
{
    /**
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:42
     */
    public function beginTransaction(): void
    {
        DB::beginTransaction();
    }

    /**
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:42
     */
    public function commit(): void
    {
        DB::commit();
    }

    /**
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:42
     */
    public function rollback(): void
    {
        DB::rollBack();
    }

    /**
     * @param \Closure $closure
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:42
     */
    public function transaction(\Closure $closure): void
    {
        DB::transaction($closure);
    }
}
