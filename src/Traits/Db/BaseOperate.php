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

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Nwidart\Modules\Enums\Status;

trait BaseOperate
{
    use WithEvents;
    use WithRelations;

    /**
     * 字段别名
     * @param string|array $fields
     * @return string|array
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:46
     */
    public function aliasField(string|array $fields): string|array
    {
        $table = $this->getTable();

        if (is_string($fields)) {
            return sprintf('%s.%s', $table, $fields);
        }

        foreach ($fields as &$field) {
            $field = sprintf('%s.%s', $table, $field);
        }

        return $fields;
    }

    /**
     * 创建数据
     *
     * @param array $data
     * @return mixed
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:46
     */
    public function createBy(array $data): mixed
    {
        $model = $this->newInstance();

        if ($model->fill($this->filterData($data))->save()) {
            return $model->getKey();
        }

        return false;
    }

    /**
     * 删除
     * @param      $id
     * @param bool $force
     * @return bool|null
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:46
     */
    public function deleteBy($id, bool $force = false): ?bool
    {
        /* @var Model $model */
        $model = static::find($id);

        if (empty($model)) {
            return true;
        }

        if (in_array($this->getParentIdColumn(), $this->getFillable()) && $this->where($this->getParentIdColumn(), $model->id)->first()) {
            throw new \Exception('请先删除子级');
        }

        if ($force) {
            $deleted = $model->forceDelete();
        } else {
            $deleted = $model->delete();
        }

        if ($deleted) {
            $this->deleteRelations($model);
        }

        return $deleted;
    }

    /**
     * 批量删除
     * @param array|string  $ids
     * @param bool          $force
     * @param \Closure|null $callback
     * @return bool
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:46
     */
    public function deletesBy(array|string $ids, bool $force = false, \Closure $callback = null): bool
    {
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        DB::transaction(function () use ($ids, $force, $callback) {
            foreach ($ids as $id) {
                $this->deleteBy($id, $force);
            }

            if ($callback) {
                $callback($ids);
            }
        });

        return true;
    }

    /**
     * @param bool $is
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:50
     */
    public function fillCreatorId(bool $is = true): static
    {
        $this->isFillCreatorId = $is;

        return $this;
    }

    public function fillTenantId(bool $is = true): static
    {
        $this->isFillTenantId = $is;

        return $this;
    }


    /**
     * get first by ID
     *
     * @param       $value
     * @param       $field
     * @param array $columns
     * @return Model|null
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:46
     */
    public function firstBy($value, $field = null, array $columns = ['*']): ?Model
    {
        $field = $field ?: $this->getKeyName();

        $model = static::where($field, $value)->first($columns);

        if ($this->afterFirstBy) {
            $model = call_user_func($this->afterFirstBy, $model);
        }

        return $model;
    }

    /**
     * 获取新建时候的字段
     * @return string|null
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:46
     */
    public function getCreatedAtColumn(): ?string
    {
        $createdAtColumn = parent::getCreatedAtColumn();

        if (!in_array(parent::getUpdatedAtColumn(), $this->getFillable())) {
            $createdAtColumn = null;
        }

        return $createdAtColumn;
    }

    /**
     * 获取创建者字段
     * @return string
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-15 下午2:46
     */
    public function getCreatorIdColumn(): string
    {
        return 'creator_id';
    }

    /**
     * @return array
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:50
     */
    public function getForm(): array
    {
        if (property_exists($this, 'form') && !empty($this->form)) {
            return $this->form;
        }

        return [];
    }

    /**
     * @return array
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:50
     */
    public function getFormRelations(): array
    {
        if (property_exists($this, 'formRelations') && !empty($this->form)) {
            return $this->formRelations;
        }

        return [];
    }

    /**
     * @return mixed
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:50
     */
    public function getList(): mixed
    {
        $fields = property_exists($this, 'fields') ? $this->fields : ['*'];

        $builder = static::select($fields)
            ->creator()
            ->quickSearch();

        // 数据权限
        if ($this->dataRange) {
            $builder = $builder->dataRange();
        }

        // 租户数据
        if ($this->tenantData) {
            $builder = $builder->tenantData();
        }

        // before list
        if ($this->beforeGetList instanceof Closure) {
            $builder = call_user_func($this->beforeGetList, $builder);
        }

        // 排序
        if ($this->sortField && in_array($this->sortField, $this->getFillable())) {
            $builder = $builder->orderBy($this->aliasField($this->sortField), $this->sortDesc ? 'desc' : 'asc');
        }

        // 动态排序
        $dynamicSortField = Request::get('sortField');
        if ($dynamicSortField && $dynamicSortField <> $this->sortField) {
            $builder = $builder->orderBy($this->aliasField($dynamicSortField), Request::get('order', 'asc'));
        }
        $builder = $builder->orderByDesc($this->aliasField($this->getKeyName()));

        // 分页
        if ($this->isPaginate) {
            return $builder->paginate(Request::get('limit', $this->perPage));
        }

        $data = $builder->get();
        // if set as tree, it will show tree data
        if ($this->asTree) {
            return $data->toTree();
        }

        return $data;
    }

    /**
     * @return string
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:51
     */
    public function getParentIdColumn(): string
    {
        return $this->parentIdColumn;
    }
    public function getTenantIdColumn(): string
    {
        return 'tenant_id';
    }


    /**
     * @return string|null
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:52
     */
    public function getUpdatedAtColumn(): ?string
    {
        $updatedAtColumn = parent::getUpdatedAtColumn();

        if (!in_array(parent::getUpdatedAtColumn(), $this->getFillable())) {
            $updatedAtColumn = null;
        }

        return $updatedAtColumn;
    }

    /**
     * @param bool $auto
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:52
     */
    public function setAutoNull2EmptyString(bool $auto = true): static
    {
        $this->autoNull2EmptyString = $auto;

        return $this;
    }

    /**
     * @param bool $use
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:52
     */
    public function setDataRange(bool $use = true): static
    {
        $this->dataRange = $use;

        return $this;
    }

    /**
     * @param string $parentId
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:52
     */
    public function setParentIdColumn(string $parentId): static
    {
        $this->parentIdColumn = $parentId;

        return $this;
    }
    public function setTenantData(bool $tenantData = true): static
    {
        $this->tenantData = $tenantData;

        return $this;
    }


    /**
     * @param array $data
     * @return mixed
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:52
     */
    public function storeBy(array $data): mixed
    {
        if ($this->fill($this->filterData($data))->save()) {
            if ($this->getKey()) {
                $this->createRelations($data);
            }

            return $this->getKey();
        }

        return false;
    }

    /**
     * @param        $id
     * @param string $field
     * @return bool
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:52
     */
    public function toggleBy($id, string $field = 'status'): bool
    {
        $model = $this->firstBy($id);

        $status = $model->getAttribute($field) == Status::Enable->value() ? Status::Disable->value() : Status::Enable->value();

        $model->setAttribute($field, $status);

        if ($model->save() && in_array($this->getParentIdColumn(), $this->getFillable())) {
            $this->updateChildren($id, $field, $model->getAttribute($field));
        }

        return true;
    }

    /**
     * @param array|string $ids
     * @param string       $field
     * @return bool
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:52
     */
    public function togglesBy(array|string $ids, string $field = 'status'): bool
    {
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        DB::transaction(function () use ($ids, $field) {
            foreach ($ids as $id) {
                $this->toggleBy($id, $field);
            }
        });

        return true;
    }

    /**
     * @param       $id
     * @param array $data
     * @return mixed
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:52
     */
    public function updateBy($id, array $data): mixed
    {
        $model = $this->where($this->getKeyName(), $id)->first();

        $updated = $model->fill($this->filterData($data))->save();

        if ($updated) {
            $this->updateRelations($this->find($id), $data);
        }

        return $updated;
    }


    /**
     * @param mixed  $parentId
     * @param string $field
     * @param mixed  $value
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:52
     */
    public function updateChildren(mixed $parentId, string $field, mixed $value): void
    {
        if (!$parentId instanceof Arrayable) {
            $parentId = Collection::make([$parentId]);
        }

        $childrenId = $this->whereIn($this->getParentIdColumn(), $parentId)->pluck('id');

        if ($childrenId->count()) {
            if ($this->whereIn($this->getParentIdColumn(), $parentId)->update([
                $field => $value
            ])) {
                $this->updateChildren($childrenId, $field, $value);
            }
        }
    }

    /**
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:52
     */
    public function withoutForm(): static
    {
        if (property_exists($this, 'form') && !empty($this->form)) {
            $this->form = [];
        }

        return $this;
    }

    /**
     * @param array $data
     * @return array
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:53
     */
    protected function filterData(array $data): array
    {
        // 表单保存的数据集合
        $fillable = array_unique(array_merge($this->getFillable(), $this->getForm()));

        foreach ($data as $k => $val) {
            if ($this->autoNull2EmptyString && is_null($val)) {
                $data[$k] = '';
            }

            if (!empty($fillable) && !in_array($k, $fillable)) {
                unset($data[$k]);
            }

            if (in_array($k, [$this->getUpdatedAtColumn(), $this->getCreatedAtColumn()])) {
                unset($data[$k]);
            }
        }

        if (Auth::guard(getGuardName())->hasUser()) {
            $user = Auth::guard(getGuardName())->user();

            if ($this->isFillCreatorId && in_array($this->getCreatorIdColumn(), $this->getFillable())) {
                $data['creator_id'] = Auth::guard(getGuardName())->id();
            }

            if ($this->isFillTenantId && empty($data['tenant_id']) && in_array($this->getTenantIdColumn(), $this->getFillable())) {
                $data['tenant_id'] = $user->tenant_id;
            }
        }

        return $data;
    }

    /**
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:54
     */
    protected function setCreatorId(): static
    {
        $this->setAttribute($this->getCreatorIdColumn(), Auth::guard(getGuardName())->id());

        return $this;
    }

    /**
     * @param bool $isPaginate
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:55
     */
    protected function setPaginate(bool $isPaginate = true): static
    {
        $this->isPaginate = $isPaginate;

        return $this;
    }

    /**
     * @param string $sortField
     * @return $this
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-09-02 09:55
     */
    protected function setSortField(string $sortField): static
    {
        $this->sortField = $sortField;

        return $this;
    }
}
