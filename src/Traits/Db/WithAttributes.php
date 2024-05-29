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

trait WithAttributes
{
    /**
     * @var string
     */
    protected string $parentIdColumn = 'parent_id';

    /**
     * @var string
     */
    protected string $sortField = '';

    /**
     * @var bool
     */
    protected bool $sortDesc = true;

    /**
     * @var bool
     */
    protected bool $asTree = false;

    /**
     * @var array|string[]
     */
    protected array $fields = ['*'];

    /**
     * @var bool
     */
    protected bool $isPaginate = true;

    /**
     * @var array
     */
    protected array $form = [];

    /**
     * @var array
     */
    protected array $formRelations = [];

    /**
     * @var bool
     */
    protected bool $dataRange = false;

    /**
     * @var bool
     */
    protected bool $autoNull2EmptyString = true;

    /**
     * @param string $parentId
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:38
     */
    public function setParentIdColumn(string $parentId): static
    {
        $this->parentIdColumn = $parentId;

        return $this;
    }

    /**
     * @param string $sortField
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:38
     */
    protected function setSortField(string $sortField): static
    {
        $this->sortField = $sortField;

        return $this;
    }

    /**
     * @param bool $isPaginate
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:38
     */
    protected function setPaginate(bool $isPaginate = true): static
    {
        $this->isPaginate = $isPaginate;

        return $this;
    }

    /**
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:38
     */
    public function withoutForm(): static
    {
        if (property_exists($this, 'form') && !empty($this->form)) {
            $this->form = [];
        }

        return $this;
    }

    /**
     * @return array
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:38
     */
    public function getForm(): array
    {
        if (property_exists($this, 'form') && !empty($this->form)) {
            return $this->form;
        }

        return [];
    }

    /**
     * @return string
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:38
     */
    public function getParentIdColumn(): string
    {
        return $this->parentIdColumn;
    }

    /**
     * @return array
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:38
     */
    public function getFormRelations(): array
    {
        if (property_exists($this, 'formRelations') && !empty($this->form)) {
            return $this->formRelations;
        }

        return [];
    }

    /**
     * @param bool $use
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:38
     */
    public function setDataRange(bool $use = true): static
    {
        $this->dataRange = $use;

        return $this;
    }

    /**
     * @param bool $auto
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:38
     */
    public function setAutoNull2EmptyString(bool $auto = true): static
    {
        $this->autoNull2EmptyString = $auto;

        return $this;
    }
}
