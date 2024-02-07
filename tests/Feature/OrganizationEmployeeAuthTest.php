<?php

namespace Tests\Feature;

use Domain\Organization\Models\Employee;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrganizationEmployeeAuthTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->superAdmin = User::query()->first();
    }

    /** @test */
    public function exists_employee_can_login(): void
    {
        Employee::factory()->create([
            'email' => 'ahmedbadawy@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/organization-api/login', [
            'email' => 'ahmedbadawy@gmail.com',
            'password' => 'password',
        ]);

        $response->assertOk();

        $meta_array = (array) json_decode($response->content());

        $this->assertArrayHasKey('token', $meta_array);
    }
}
