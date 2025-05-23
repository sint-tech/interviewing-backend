<?php

namespace Tests\Feature\App\Organization\Auth;

use Domain\Organization\Models\Employee;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public Employee $employeeAuth;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->employeeAuth = Employee::factory()->createOne();
    }

    /** @test  */
    public function itShouldShowMyProfileDataAndOrganizationData()
    {
        $response = $this->actingAs($this->employeeAuth, 'organization')->get(route('organization.auth.my-profile'));

        $response->assertSuccessful();
        $response->assertJson(function (AssertableJson $json) {
            return $json->where('data.email', $this->employeeAuth->email)
                ->where('data.organization.name', $this->employeeAuth->organization->name)
                ->etc();
        });
    }

    /** @test  */
    public function itShouldUnAuthorizeAccessMyProfileWhenNotAuth()
    {
        $rs = $this->assertGuest()->get(route('organization.auth.my-profile'));

        $rs->assertUnauthorized();
    }
}
