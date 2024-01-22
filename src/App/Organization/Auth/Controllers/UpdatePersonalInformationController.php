<?php

namespace App\Organization\Auth\Controllers;

use App\Organization\EmployeeManagement\Resource\EmployeeResource;
use Domain\Organization\Actions\UpdateEmployeeAction;
use Domain\Organization\DataTransferObjects\EmployeeData;
use Illuminate\Http\Request;
use Support\Controllers\Controller;

class UpdatePersonalInformationController extends Controller
{
    public function __construct(protected UpdateEmployeeAction $updateEmployeeAction)
    {
    }

    public function __invoke(Request $request)
    {
        $request->validate([
            'first_name' => ['filled', 'string', 'max:64'],
            'last_name' => ['filled', 'string', 'max:64'],
        ]);

        $this->updateEmployeeAction->execute(
            auth()->user(),
            EmployeeData::from(
                array_merge(auth()->user()->toArray(), $request->only(['first_name', 'last_name']))
            )
        );

        return EmployeeResource::make(auth()->user()->fresh());
    }
}
