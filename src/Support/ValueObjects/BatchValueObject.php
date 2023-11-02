<?php

namespace Support\ValueObjects;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BatchValueObject
{
    private static ?BatchValueObject $instance = null;

    protected int $lastBatch;

    private static ?string $table = null;

    protected function __construct(Model|string|Builder $table)
    {
        $this->lastBatch = (int) DB::table(table_name($table))->max('batch');
    }

    public static function getInstance(Model|string|Builder $table): self
    {
        if (self::$instance === null && self::$table === null) {
            self::$table = $table;

            self::$instance = new self($table);
        }

        return self::$instance;
    }

    public function refreshInstance(): self
    {
        $table = self::$table;

        self::destroyInstance();

        return self::getInstance($table);
    }

    public static function destroyInstance(): void
    {
        [self::$instance, self::$table] = null;
    }

    public function getLastBatch(): int
    {
        return $this->lastBatch;
    }

    public function getNextBatch(): int
    {
        return $this->lastBatch + 1;
    }
}
