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

namespace Nwidart\Modules\Support\Excel;

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Validators\ValidationException;
use Nwidart\Modules\Exceptions\FailedException;

abstract class Import implements ToCollection, WithChunkReading, WithStartRow, WithValidation, WithEvents
{
    use Importable;
    use RegistersEventListeners;
    use SkipsFailures;

    protected array $err = [];

    protected static int $total = 0;

    protected array $params = [];

    protected int $size = 500;

    protected int $chunk = 0;

    protected int $start = 2;

    protected static int $importMaxNum = 5000;

    protected int $chunkSize = 200;

    public function import(string|UploadedFile $filePath, string $disk = null, string $readerType = null): int|array
    {
        if (empty($filePath)) {
            throw new FailedException('没有上传导入文件');
        }

        if ($filePath instanceof UploadedFile) {
            $filePath = $filePath->store('excel/import/' . date('Ymd') . '/');
        }

        try {
            $this->getImporter()->import(
                $this,
                $filePath,
                $disk ?? $this->disk ?? null,
                $readerType ?? $this->readerType ?? null
            );
        } catch (ValidationException $e) {
            $failures = $e->failures();

            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = sprintf('第%d行错误:%s', $failure->row(), implode('|', $failure->errors()));
            }

            return [
                'error' => $errors,
                'total' => static::$total,
                'path' => $filePath,
            ];
        }

        return static::$total;
    }

    public function setParams($params): static
    {
        $this->params = $params;

        return $this;
    }

    public static function beforeImport(BeforeImport $event): void
    {
        $total = $event->getReader()->getTotalRows()['Worksheet'];

        static::$total = $total;

        if ($total > static::$importMaxNum) {
            throw new FailedException(sprintf('最大支持导入数量 %d 条', self::$importMaxNum));
        }
    }

    public function chunkSize(): int
    {
        return $this->chunkSize;
    }

    public function startRow(): int
    {
        // TODO: Implement startRow() method.
        return $this->start;
    }

    public function rules(): array
    {
        // TODO: Implement rules() method.
        return [];
    }
}
