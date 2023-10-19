<?php

namespace Tests\Feature\Organization\EmployeeManagement;

use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class EmployeeControllerTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();
    }

    public function test_it_should_fetch_only_the_current_employees_organization_for_auth_employee(): void
    {
        $organization = Organization::factory()->createOne(['name' => 'test']);

        $otherOrganization = Organization::factory()->createOne(['name' => 'other organization']);

        Employee::factory(10)->create([
            'organization_id' => $organization->getKey(),
        ]);

        Employee::factory()->create(['organization_id' => $otherOrganization->getKey()]);

        $response = $this->actingAs(Employee::query()->first(), 'api')->get('/organization-api/employees');

        $response->assertOk();

        $response->assertJsonCount(10, 'data');
    }

    /** @test */
    public function itShouldOnlyShowSingleEmployeeForBelongsToAuthOrganization()
    {
        $organization = Organization::factory()->createOne(['name' => 'test']);

        $otherOrganization = Organization::factory()->createOne(['name' => 'other organization']);

        Employee::factory(10)->create([
            'organization_id' => $organization->getKey(),
        ]);

        $otherEmployee = Employee::factory()->for($otherOrganization)->createOne();

        $this->actingAs(Employee::query()->first(), 'api')->get('/organization-api/employees/'.Employee::query()->first()->getKey())
            ->assertOk();

        $this->actingAs(Employee::query()->first(), 'api')->get('/organization-api/employees/'.$otherEmployee->getKey())
            ->assertNotFound();
    }

    /** @test */
    public function itShouldCreateNewEmployeeBelongsToTheSameOrganization()
    {
        $organization = Organization::factory()->createOne();

        $organizationManager = Employee::factory()->createOne(['organization_id' => $organization->getKey()]);

        $request_data = [
            'first_name' => 'ahmed',
            'last_name' => 'Badawy',
            'email' => 'ahmedbadawy.fcai@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->actingAs($organizationManager, 'api')
            ->post(route('organization_api.employees.store'), $request_data)
            ->assertCreated()
            ->assertJson(function (AssertableJson $json) {
                return $json->hasAll('data.first_name', 'data.last_name', 'data.email')
                    ->where('data.email', 'ahmedbadawy.fcai@gmail.com');
            });
    }

    /** @test */
    public function itShouldNotDeleteAuthEmployee()
    {
        $employee = Employee::factory()->for(Organization::factory())->createOne(['is_organization_manager' => true]);

        $this->actingAs($employee, 'api-employee')->delete(route('organization_api.employees.destroy', ['employee' => $employee->getKey()]))
            ->assertForbidden();
    }

    /** @test  */
    public function itShouldDeleteOnlyEmployeesInTheAuthOrganization()
    {
        $employee = Employee::factory(3)->for(Organization::factory())->create(['is_organization_manager' => true])->first();

        $outside_employee = Employee::factory()->for(Organization::factory())->createOne(['is_organization_manager' => true]);

        $this->actingAs($employee, 'api-employee')->delete(route('organization_api.employees.destroy',
            [
                'employee' => Employee::query()->whereKey(2)->first()->getKey(),
            ]))
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json->has('data.deleted_at')->whereType('data.deleted_at', 'string'));

        $this->actingAs($employee, 'api-employee')->delete(route('organization_api.employees.destroy', ['employee' => $outside_employee->getKey()]))
            ->assertNotFound();

    }
}
