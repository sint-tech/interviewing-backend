<?php

namespace Tests\Feature\App\Admin\Organization\OrganizationController;

use Database\Seeders\SintAdminsSeeder;
use Domain\Organization\Models\Organization;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RestoreOrganizationTest extends TestCase
{
    use DatabaseMigrations,WithFaker;

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
    public function itShouldRestoreDeletedOrganization(): void
    {
        $organization = Organization::factory()->createOne();

        $organization->delete();

        $this->assertNotNull($organization->deleted_at);

        $this->post(route('admin.organizations.restore', $organization))
            ->assertSuccessful()
            ->assertJson(function (AssertableJson $json) {
                return $json->missing('data.deleted_at')
                    ->etc();
            });

        $this->assertNull($organization->refresh()->deleted_at);
    }

    /** @test  */
    public function itShouldRestoreOnlyDeletedOrganization(): void
    {
        $organization = Organization::factory()->createOne();

        $this->post(route('admin.organizations.restore', $organization))
            ->assertNotFound();

        $organization->delete();

        $this->post(route('admin.organizations.restore', $organization))
            ->assertSuccessful();
    }
}
