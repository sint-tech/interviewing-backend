<?php

namespace Domain\Organization\Actions;

use Domain\Organization\Models\Organization;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UploadOrganizationLogoAction
{
    public function __construct(
        public Organization $organization,
        public UploadedFile $logo
    ) {
    }

    public function execute(): Media
    {
        $this->organization->addMedia($this->logo)->toMediaCollection('logo');

        return $this->organization->logo;
    }
}
