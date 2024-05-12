<?php

namespace App\Admin\Organization\Factories;

use Illuminate\Http\Request;
use Spatie\LaravelData\Optional;
use App\Admin\Organization\Requests\OrganizationStoreRequest;
use Domain\Organization\DataTransferObjects\OrganizationData;
use App\Admin\Organization\Requests\OrganizationUpdateRequest;

class OrganizationDataFactory
{
    public static function fromRequest(Request $request)
    {
        if ($request instanceof OrganizationStoreRequest) {
            return self::fromStoreRequest($request);
        } elseif ($request instanceof OrganizationUpdateRequest) {
            return self::fromUpdateRequest($request);
        }

        throw new \InvalidArgumentException('not handled request type: '.$request::class);
    }

    private static function fromStoreRequest(OrganizationStoreRequest $request): OrganizationData
    {
        return OrganizationData::from([
            'name' => $request->validated('name'),
            'logo' => $request->validated('logo', Optional::create()),
        ]);
    }

    private static function fromUpdateRequest(OrganizationUpdateRequest $request): OrganizationData
    {
        return OrganizationData::from($request);
    }
}
