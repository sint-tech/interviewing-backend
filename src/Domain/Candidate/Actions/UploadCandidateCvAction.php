<?php

namespace Domain\Candidate\Actions;

use Domain\Candidate\DataTransferObjects\CvData;
use Domain\Candidate\Models\Candidate;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UploadCandidateCvAction
{
    public function __construct
    (
        public Candidate    $candidate,
        public CvData      $data,
    )
    {

    }

    /**
     * @return Media
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function execute():Media
    {
        $attributes = $this->data->toArray();

        unset($attributes['cv']);

        return $this->candidate
            ->addMedia($this->data->cv)
            ->withCustomProperties($attributes)
            ->toMediaCollection("cv");
    }
}
