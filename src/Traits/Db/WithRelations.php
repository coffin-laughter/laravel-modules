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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait WithRelations
{
    /**
     * @param Model $model
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:40
     */
    public function deleteRelations(Model $model): void
    {
        $relations = $this->getRelations();
        foreach ($relations as $relation) {
            $isRelation = $model->{$relation}();
            // BelongsToMany
            if ($isRelation instanceof BelongsToMany) {
                $isRelation->detach();
            }
        }
    }

    /**
     * @param Model $model
     * @param array $data
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:40
     */
    public function updateRelations(Model $model, array $data): void
    {
        foreach ($this->getRelationsData($data) as $relation => $relationData) {
            $isRelation = $model->{$relation}();

            // BelongsToMany
            if ($isRelation instanceof BelongsToMany) {
                $isRelation->sync($relationData);
            }
        }
    }
    /**
     * @param array $data
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:40
     */
    protected function createRelations(array $data): void
    {
        foreach ($this->getRelationsData($data) as $relation => $relationData) {
            $isRelation = $this->{$relation}();
            if (!count($relationData)) {
                continue;
            }

            if ($isRelation instanceof BelongsToMany) {
                $isRelation->attach($relationData);
            }

            if ($isRelation instanceof HasMany || $isRelation instanceof HasOne) {
                $isRelation->create($relationData);
            }
        }
    }

    /**
     * @param array $data
     * @return array
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:40
     */
    protected function getRelationsData(array $data): array
    {
        $relations = $this->getFormRelations();

        if (empty($relations)) {
            return [];
        }

        $relationsData = [];

        foreach ($relations as $relation) {
            if (!isset($data[$relation]) || !$this->isRelation($relation)) {
                continue;
            }

            $relationData = $data[$relation];

            $relationsData[$relation] = $relationData;
        }

        return $relationsData;
    }
}
