<?php

namespace Tests\Feature;

use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class OrganizationTest extends TestCase
{
    use DatabaseMigrations;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        Artisan::call('passport:install');

        Artisan::call('passport:client', [
            '--password' => 1,
            '--name' => 'Laravel Password Grant Client FOR CANDIDATE',
            '--provider' => 'candidates',
        ]);

        Artisan::call("db:seed",[
            '--class'   => 'SintAdminsSeeder'
        ]);

        $this->superAdmin = User::query()->first();
    }

    public function testItShouldCreateNewOrganization(): void
    {
        $response = $this
            ->actingAs($this->superAdmin,'api')
            ->post('/admin-api/organizations', [
            'name' => 'new organization',
            'manager' => [
                'first_name' => 'foo',
                'last_name' => 'baa',
                'email' => 'foo@gmail.com',
                'password' => 'its@strongPass0word',
            ],
        ]);

        $response->assertSuccessful();
    }

    public function testItShouldDeleteOrganization(): void
    {
        $organization = Organization::factory()
            ->has(
                Employee::factory(2)
                    ->sequence([
                        'is_organization_manager' => true,
                    ],
                        ['is_organization_manager' => false]
                    ))
            ->create();

        Employee::factory()->for($organization)->create();

        $response = $this->actingAs($this->superAdmin,'api')->delete('/admin-api/organizations/'.$organization->getKey());

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                'deleted_at',
            ],
        ]);
    }
}
