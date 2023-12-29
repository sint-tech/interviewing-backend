<?php

namespace Tests\Feature\App\Admin\Organization;

use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class EmployeeControllerTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation,WithFaker;

    protected User $sintUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();

        Artisan::call('db:seed', [
            '--class' => 'SintAdminsSeeder',
        ]);

        $this->sintUser = User::query()->first();

        $this->actingAs($this->sintUser, 'api');
    }

    /** @test  */
    public function itShouldShowEmployees()
    {
        Employee::factory(1000)->create();

        $response = $this->get(route('admin.employees.index'));
        $response->assertSuccessful();
        $response->assertJsonCount(25, 'data');

        $response = $this->get(route('admin.employees.index', ['per_page' => 1000]));
        $response->assertSuccessful();
        $response->assertJsonCount(1000, 'data');
    }

    /** @test  */
    public function itShouldShowSingleEmployee()
    {
        $employee = Employee::factory()->createOne();

        $response = $this->get(route('admin.employees.show', $employee));
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'first_name' => $employee->first_name,
            'last_name' => $employee->last_name,
            'email' => $employee->email,
            'organization_id' => $employee->organization_id,
            'is_manager' => $employee->is_organization_manager,
        ]);
    }

    /** @test  */
    public function itShouldCreateEmployee()
    {
        $response = $this->post(route('admin.employees.store'), [
            'first_name' => 'foo',
            'last_name' => 'baa',
            'email' => 'foo.baa@example.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'is_organization_manager' => true,
            'organization_id' => $organization_id = Organization::factory()->createOne()->getKey(),
        ]);
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'first_name' => 'foo',
            'last_name' => 'baa',
            'email' => 'foo.baa@example.com',
            'is_manager' => true,
            'organization_id' => $organization_id,
        ]);

        $this->assertDatabaseCount(Employee::class, 1);
    }

    /** @test  */
    public function itShouldUpdateEmployee()
    {
        $employee = Employee::factory()->createOne();

        $response = $this->put(route('admin.employees.update', $employee), [
            'first_name' => 'A',
            'last_name' => 'B',
            'email' => $employee->email,
        ]);
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'first_name' => 'A',
            'last_name' => 'B',
            'email' => $employee->email,
        ]);
    }

    /** @test  */
    public function itShouldNotUpdateOrganizationId()
    {
        $employee = Employee::factory()->createOne();

        $response = $this->put(route('admin.employees.update', $employee), [
            'organization_id' => $organization_id = Organization::factory()->createOne()->getKey(),
        ]);
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'organization_id' => $employee->refresh()->organization_id,
        ]);

        $this->assertNotEquals($organization_id, $employee->organization_id);
    }

    /** @test  */
    public function itShouldDeleteEmployee()
    {
        $employee = Employee::factory()->createOne();

        $this->assertDatabaseCount(Employee::class, 1);

        $response = $this->delete(route('admin.employees.destroy', $employee));
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'deleted_at' => now()->format('Y-m-d H:i'),
        ]);

        $this->delete(route('admin.employees.destroy', $employee))->assertNotFound();
    }
}
