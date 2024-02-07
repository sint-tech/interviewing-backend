<?php

namespace Tests\Feature\App\Organization\Auth;

use Domain\Organization\Models\Employee;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class MyOrganizationControllerTest extends TestCase
{
    use DatabaseMigrations;

    public Employee $employeeAuth;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->employeeAuth = Employee::factory()->createOne();
    }

    /** @test  */
    public function itShouldShowMyOrganizationData()
    {
        $response = $this->actingAs($this->employeeAuth, 'organization')->get(route('organization.auth.my-organization'));

        $response->assertSuccessful();
        $response->assertJson(function (AssertableJson $json) {
            return $json->where('data.name', $this->employeeAuth->organization->name)->etc();
        });
    }

    /** @test  */
    public function itShouldUnAuthorizeAccessMyOrganizationWhenNotAuth()
    {
        $rs = $this->assertGuest()->get(route('organization.auth.my-organization'));

        $rs->assertUnauthorized();
    }
}
