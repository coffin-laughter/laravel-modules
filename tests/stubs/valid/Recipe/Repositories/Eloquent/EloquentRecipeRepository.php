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

namespace Modules\Recipe\Repositories\Eloquent;

use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Recipe\Events\RecipeWasCreated;
use Modules\Recipe\Repositories\RecipeRepository;

class EloquentRecipeRepository extends EloquentBaseRepository implements RecipeRepository
{
    public function create($data)
    {
        $recipe = $this->model->create($data);

        event(new RecipeWasCreated($recipe, $data));

        return $recipe;
    }
}
