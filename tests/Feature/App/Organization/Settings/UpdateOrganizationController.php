<?php

namespace Tests\Feature\App\Organization\Settings;

use Database\Seeders\SintAdminsSeeder;
use Domain\Organization\Models\Employee;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UpdateOrganizationController extends TestCase
{
    use DatabaseMigrations;

    public Employee $employeeAuth;

    public function setUp(): void
    {
        parent::setUp();

        $this->employeeAuth = Employee::factory()->createOne();

        $this->seed(SintAdminsSeeder::class);

        $this->actingAs($this->employeeAuth, 'organization');
    }

    /** @test */
    public function itShouldUpdateTheAuthOrganization()
    {
        $response = $this->post(route('organization.settings.update-organization'), [
            'name' => 'new organization name',
            'contact_email' => 'foo.baa@gmail.com',
        ]);
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'name' => 'new organization name',
            'contact_email' => 'foo.baa@gmail.com',
        ]);
    }

    /** @test  */
    public function onlyOrganizationManagerCanUpdateOrganization()
    {
        $response = $this->post(route('organization.settings.update-organization'), [
            'name' => 'new organization name',
            'contact_email' => 'foo.baa@gmail.com',
        ]);
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'name' => 'new organization name',
            'contact_email' => 'foo.baa@gmail.com',
        ]);

        $this->actingAs(Employee::factory()->for($this->employeeAuth->organization, 'organization')->createOne(), 'organization')
            ->post(route('organization.settings.update-organization'))
            ->assertForbidden();
    }
}
