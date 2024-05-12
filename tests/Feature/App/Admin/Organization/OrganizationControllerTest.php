<?php

namespace Tests\Feature\App\Admin\Organization;

use Database\Seeders\SintAdminsSeeder;
use Domain\Organization\Models\Organization;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $sintUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->seed(SintAdminsSeeder::class);

        $this->sintUser = User::query()->first();

        $this->actingAs($this->sintUser, 'admin');
    }

    /** @test  */
    public function itShouldShowOrganizations()
    {
        Organization::factory(50)->create();

        $response = $this->get(route('admin.organizations.index'));
        $response->assertSuccessful();
        $response->assertJsonCount(25, 'data');

        $response = $this->get(route('admin.organizations.index', ['per_page' => 50]));
        $response->assertSuccessful();
        $response->assertJsonCount(50, 'data');
    }

    /** @test  */
    public function itShouldShowSingleOrganization()
    {
        $organization = Organization::factory()->createOne([
            'limit' => 1000,
            'interview_consumption' => 500,
        ]);

        $response = $this->get(route('admin.organizations.show', $organization));
        $response->assertSuccessful();

        $response->assertJson([
            'data' => [
                'name' => $organization->name,
                'contact_email' => $organization->contact_email,
                'address' => $organization->address,
                'website_url' => $organization->website_url,
                'number_of_employees' => $organization->number_of_employees,
                'industry' => $organization->industry,
                'logo' => $organization->logo,
                'limit' => $organization->limit,
                'interview_consumption' => $organization->interview_consumption,
                'limit_exceeded' => $organization->limitExceeded(),
            ]
        ]);
    }

    /** @test  */
    public function itShouldCreateOrganization()
    {
        $organization = Organization::factory()->makeOne();

        $this->post(
            route('admin.organizations.store'),
            $organization->toArray() + [
                'manager' => [
                    'first_name' => $this->faker->name,
                    'last_name' => $this->faker->name,
                    'email' => $this->faker->email,
                    'password' => '@Password123',
                ]
            ]
        )->assertSuccessful();
    }

    /** @test  */
    public function itShouldUpdateOrganization()
    {
        $organization = Organization::factory()->createOne();

        $this->put(
            route('admin.organizations.update', $organization),
            $organization->toArray() + [
                'limit' => 1000,
            ]
        )->assertSuccessful();

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'limit' => 1000,
        ]);
    }

    /** @test  */
    public function itShouldDeleteOrganization()
    {
        $organization = Organization::factory()->createOne();

        $this->delete(route('admin.organizations.destroy', $organization))
            ->assertSuccessful();
    }
}
