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

namespace Nwidart\Modules\Commands;

use Illuminate\Console\Command;
use Nwidart\Modules\ModuleManifest;

class ModuleDiscoverCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create module compiled class file';
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'module:discover';

    public function handle(ModuleManifest $manifest): void
    {
        $this->components->info('Discovering modules');

        $manifest->build();

        collect($manifest->providersArray())
            ->map(fn ($provider) => preg_match('/Modules\\\\(.*?)\\\\/', $provider, $matches) ? $matches[1] : null)
            ->unique()
            ->each(fn ($description) => $this->components->task($description))
            ->whenNotEmpty(fn () => $this->newLine());
    }
}
