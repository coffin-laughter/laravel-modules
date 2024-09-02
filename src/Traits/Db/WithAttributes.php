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

trait WithAttributes
{
    /**
     * @var bool
     */
    protected bool $asTree = false;

    /**
     * @var bool
     */
    protected bool $autoNull2EmptyString = true;

    /**
     * @var bool
     */
    protected bool $dataRange = false;

    /**
     * @var array|string[]
     */
    protected array $fields = ['*'];

    /**
     * @var array
     */
    protected array $form = [];

    /**
     * @var array
     */
    protected array $formRelations = [];

    protected bool $isFillCreatorId = true;
    protected bool $isFillTenantId = true;

    /**
     * @var bool
     */
    protected bool $isPaginate = true;
    /**
     * @var string
     */
    protected string $parentIdColumn = 'parent_id';

    /**
     * @var bool
     */
    protected bool $sortDesc = true;

    /**
     * @var string
     */
    protected string $sortField = '';

    /**
     * @var bool
     */
    protected bool $tenantData = false;

}
