<?php

namespace Domain\Candidate\DataTransferObjects;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class CvData extends Data
{
    public function __construct
    (
        public readonly UploadedFile $cv,
        public readonly bool $used_when_registered = false
    )
    {

    }
}
