<?php

namespace Domain\Candidate\Actions;

use Domain\Candidate\Models\Candidate;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UploadCandidateCvAction
{
    public function __construct(
        public Candidate $candidate,
        public UploadedFile $cv,
        public bool $used_when_registered = false,
    ) {
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function execute(): Media
    {
        return $this->candidate
            ->addMedia($this->cv)
            ->withCustomProperties(['used_when_registered' => $this->used_when_registered])
            ->toMediaCollection('cv');
    }
}
