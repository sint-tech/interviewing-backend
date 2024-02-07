<?php

namespace Tests\Feature\App\Organization\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected array $mainData = [
        'name' => 'Sint Fintech',
        'manager' => [
            'email' => 'ahmed.badawy@sint.com',
            'first_name' => 'Ahmed',
            'last_name' => 'Badawy',
            'password' => 'P@ssoWrd1',
            'password_confirmation' => 'P@ssoWrd1',
        ],
    ];

    protected array $optionalData = [
        'contact_email' => 'foo@sint.com',
        'website_url' => 'https://foo.sint.com',
        'address' => '18 Geash Road, Bab sharq, Alexandria',
        'number_of_employees' => '1-10',
        'industry' => 'Hiring & HR',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

    }

    /** @test  */
    public function itShouldRegisterWithTheMainFields()
    {
        $response = $this->post(route('organization.auth.register'), $this->mainData);
        $response->assertSuccessful();

        $response->assertJson(function (AssertableJson $json) {
            $json->hasAll(['token', 'data.email', 'data.organization.name']);
        });
    }

    /** @test */
    public function itShouldAcceptTheOrganizationInformation()
    {
        $response = $this->post(route('organization.auth.register'), $this->mainData + $this->optionalData);

        $response->assertSuccessful();
        $response->assertJson(function (AssertableJson $json) {
            foreach ($this->optionalData as $field => $value) {
                $json->where("data.organization.$field", $value);
            }
            $json->where('data.organization.name', $this->mainData['name']);

            return $json->etc();
        });
    }
}
