<?php

namespace Tests\Feature\App\Organization\Auth;

use Domain\Organization\Models\Employee;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UpdatePersonalInformationControllerTest extends TestCase
{
    use DatabaseMigrations;

    public Employee $employeeAuth;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employeeAuth = Employee::factory()->createOne();

        $this->actingAs($this->employeeAuth, 'organization');
    }

    /** @test */
    public function itShouldUpdatePersonalInformation(): void
    {
        $this->post(route('organization.auth.update-personal-info'), [
            'first_name' => 'new first name',
            'last_name' => 'new last name',
        ])->assertSuccessful();
    }
}
