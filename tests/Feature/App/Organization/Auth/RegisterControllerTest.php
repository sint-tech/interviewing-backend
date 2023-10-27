<?php

namespace Tests\Feature\App\Organization\Auth;

use Domain\Organization\Models\Employee;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation;

    const REGISTER_ENDPOINT = '/organization-api/register';

    public array $requestData = [
        'name' => 'organization name',
        'manager' => [
            'first_name' => 'foo',
            'last_name' => 'baa',
            'email' => 'foo@gmail.com',
            'password' => 'pa@ss0rD 123',
            'password_confirmation' => 'pa@ss0rD 123',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();

    }

    public function test_it_should_deny_access_for_auth_employee()
    {
        $employee = Employee::factory()->createOne();

        $this->actingAs($employee, 'api-employee')
            ->post(self::REGISTER_ENDPOINT)
            ->assertForbidden();

        $this->post(self::REGISTER_ENDPOINT)->assertUnprocessable();
    }

    public function test_it_should_return_employee_data_including_organization_data()
    {
        $response = $this->post(self::REGISTER_ENDPOINT, $this->requestData);

        $response->assertSuccessful();

        $response->assertJson(function (AssertableJson $json) {
            $json->first(function (AssertableJson $json) {
                return $json->where('id', 1)
                    ->where('email', 'foo@gmail.com')
                    ->where('is_organization_manager', true)
                    ->missing('password')
                    ->hasAny('organization.name')
                    ->etc();
            });

            $json->has('token');
        });
    }
}
