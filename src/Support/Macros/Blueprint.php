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

namespace Nwidart\Modules\Support\Macros;

use Illuminate\Database\Schema\Blueprint as LaravelBlueprint;

class Blueprint
{
    public function boot(): void
    {
        $this->createdAt();

        $this->updatedAt();

        $this->deletedAt();

        $this->status();

        $this->creatorId();

        $this->tenantId();

        $this->unixTimestamp();

        $this->parentId();

        $this->sort();
    }

    public function createdAt(): void
    {
        LaravelBlueprint::macro(__FUNCTION__, function () {
            $this->unsignedInteger('created_at')->default(0)->comment('created time');
        });
    }

    public function creatorId(): void
    {
        LaravelBlueprint::macro(__FUNCTION__, function () {
            $this->unsignedInteger('creator_id')->default(0)->comment('creator id');
        });
    }

    public function deletedAt(): void
    {
        LaravelBlueprint::macro(__FUNCTION__, function () {
            $this->unsignedInteger('deleted_at')->default(0)->comment('delete time');
        });
    }

    public function parentId(): void
    {
        LaravelBlueprint::macro(__FUNCTION__, function () {
            $this->unsignedInteger('parent_id')->default(0)->comment('parent id');
        });
    }

    public function sort(int $default = 1): void
    {
        LaravelBlueprint::macro(__FUNCTION__, function () use ($default) {
            $this->integer('sort')->comment('sort')->default($default);
        });
    }

    public function status(): void
    {
        LaravelBlueprint::macro(__FUNCTION__, function ($default = 1) {
            $this->tinyInteger('status')->default($default)->comment('1:normal 2: forbidden');
        });
    }

    public function tenantId($column = 'tenant_id'): void
    {
        LaravelBlueprint::macro(__FUNCTION__, function () use ($column) {
            $this->unsignedInteger($column)->default(0)->comment('所属租户 id');
        });
    }

    public function unixTimestamp(bool $softDeleted = true): void
    {
        LaravelBlueprint::macro(__FUNCTION__, function () use ($softDeleted) {
            $this->createdAt();
            $this->updatedAt();

            if ($softDeleted) {
                $this->deletedAt();
            }
        });
    }

    public function updatedAt(): void
    {
        LaravelBlueprint::macro(__FUNCTION__, function () {
            $this->unsignedInteger('updated_at')->default(0)->comment('updated time');
        });
    }
}
