<?php

namespace Domain\Invitation\ValueObjects;

use Domain\Invitation\Models\Invitation;

class InvitationBatch
{
    private static InvitationBatch|null $instance = null;

    protected int $lastBatch;

    protected function __construct()
    {
        $this->lastBatch = (int) Invitation::query()->max('batch');
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public static function destroyInstance(): void
    {
        self::$instance = null;
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
