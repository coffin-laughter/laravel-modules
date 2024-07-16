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

namespace Nwidart\Modules\Base;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nwidart\Modules\Support\Db\SoftDelete;
use Nwidart\Modules\Traits\Db\BaseOperate;
use Nwidart\Modules\Traits\Db\ScopeTenantData;
use Nwidart\Modules\Traits\Db\ScopeTrait;
use Nwidart\Modules\Traits\Db\Trans;
use Nwidart\Modules\Traits\Db\WithAttributes;

abstract class CoffinModel extends Model
{
    use BaseOperate;
    use ScopeTrait;
    use ScopeTenantData;
    use SoftDeletes;
    use Trans;
    use WithAttributes;

    protected $dateFormat = 'U';

    protected array $defaultCasts = [
        'created_at' => 'datetime:Y-m-d H:i',

        'updated_at' => 'datetime:Y-m-d H:i',
    ];

    protected array $defaultHidden = ['deleted_at'];

    protected $perPage = 10;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->init();
    }

    /**
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-21 下午5:54
     */
    public static function bootSoftDeletes(): void
    {
        static::addGlobalScope(new SoftDelete());
    }

    protected function init()
    {
        $this->makeHidden($this->defaultHidden);

        $this->mergeCasts($this->defaultCasts);

        // auto use data range
        foreach (class_uses_recursive(static::class) as $trait) {
            if (str_contains($trait, 'DataRange')) {
                $this->setDataRange();
            }

            if (str_contains($trait, 'TenantData')) {
                $this->setTenantData();
            }

        }
    }

    /**
     * 重写 serializeDate
     * @param DateTimeInterface $date
     * @return string
     *
     * @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
     * @time  : 2024-05-21 下午5:54
     */
    protected function serializeDate(DateTimeInterface $date): ?string
    {
        return Carbon::instance($date)->toISOString(true);
    }
}
