<?php
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

namespace Nwidart\Modules\Contracts;

use Nwidart\Modules\Module;

interface ActivatorInterface
{
    /**
     * Deletes a module activation status
     */
    public function delete(Module $module): void;
    /**
     * Enables a module
     */
    public function delete(Module $module): void;

    /**
     * Disables a module
     */
    public function disable(Module $module): void;

    /**
     * Determine whether the given status same with a module status.
     */
    public function hasStatus(Module $module, bool $status): bool;

    /**
     * Deletes any module activation statuses created by this class.
     */
    public function reset(): void;

    /**
     * Set active state for a module.
     */
    public function setActive(Module $module, bool $active): void;

    /**
     * Sets a module status by its name
     */
    public function setActiveByName(string $name, bool $active): void;
}
