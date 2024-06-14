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

namespace Nwidart\Modules\Support\Excel;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Nwidart\Modules\Exceptions\FailedException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

abstract class Export implements FromArray, ShouldAutoSize, WithHeadings, WithColumnWidths
{
    protected array $data;

    protected ?string $filename = null;

    protected array $header = [];

    protected array $search;

    protected bool $unlimitedMemory = false;

    public function columnWidths(): array
    {
        $columns = [];

        $column = ord('A') - 1;

        foreach ($this->header as $k => $item) {
            $column += 1;
            if (is_string($k) && is_numeric($item)) {
                $columns[chr($column)] = $item;
            }
        }

        return $columns;
    }

    public function download(string $filename = null): BinaryFileResponse
    {
        $filename = $filename ?: $this->getFilename();
        $writeType = $this->getWriteType();

        return Excel::download(
            $this,
            $filename,
            $writeType,
            [
                'filename'   => $filename,
                'write_type' => $writeType,
            ]
        );
    }

    public function export(): string
    {
        try {
            // 内存限制
            if ($this->unlimitedMemory) {
                ini_set('memory_limit', -1);
            }

            // 写入文件类型
            $writeType = $this->getWriteType();

            // 文件保存地址
            $file = sprintf('%s/%s', $this->getExportPath(), $this->getFilename($writeType));

            // 保存
            Excel::store($this, $file, null, $writeType);

            // 导出事件
            Event::dispatch(\Nwidart\Modules\Events\Excel\Export::class);

            return $file;
        } catch (\Exception|\Throwable $e) {
            throw new FailedException('导出失败: ' . $e->getMessage() . $e->getLine());
        }
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'use_bom'   => false,
        ];
    }

    public function getExportPath(): string
    {
        $path = config('modules.excel.export.path') . date('Ymd');

        if (!is_dir($path) && !mkdir($path, 0777, true) && !is_dir($path)) {
            throw new FailedException(sprintf('Directory "%s" was not created', $path));
        }

        return $path;
    }

    public function getFilename(string $type = null): string
    {
        if (!$this->filename) {
            return Str::random() . '.' . strtolower($type ?: $this->getWriteType());
        }

        return $this->filename;
    }

    public function getHeader(): array
    {
        return $this->header;
    }

    public function getSearch(): array
    {
        return $this->search;
    }

    public function headings(): array
    {
        $headings = [];

        foreach ($this->header as $k => $item) {
            if (is_string($k) && is_numeric($item)) {
                $headings[] = $k;
            }

            if (is_string($item)) {
                $headings[] = $item;
            }
        }

        return $headings;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function setHeader(array $header): static
    {
        $this->header = $header;

        return $this;
    }

    public function setSearch(array $search): static
    {
        $this->search = $search;

        return $this;
    }

    protected function getWriteType(): string
    {
        $toCsvLimit = config('modules.excel.export.csv_limit');

        if ($this instanceof WithCustomCsvSettings && count($this->array()) >= $toCsvLimit) {
            return \Maatwebsite\Excel\Excel::CSV;
        }

        return \Maatwebsite\Excel\Excel::XLSX;
    }
}
