<?php

namespace Tests\Feature\App\Organization\InvitationManagement;

use Domain\Invitation\Models\Invitation;
use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class InvitationControllerTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation;

    public Employee $employeeAuth;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employeeAuth = Employee::factory()->createOne();

        $this->actingAs($this->employeeAuth, 'api-employee');
    }

    /** @test  */
    public function itShouldShowAllInvitationsForAuthEmployee(): void
    {
        Invitation::factory(15)->for(Vacancy::factory()->for($this->employeeAuth->organization)->create())->create();

        Invitation::factory(15)->for(Vacancy::factory()->for(Organization::factory(), 'organization')->create())->create();

        $this->get(route('organization-api.invitations.index'))
            ->assertSuccessful()
            ->assertJsonCount(15, 'data');
    }

    /** @test  */
    public function itShouldShowInvitation(): void
    {
        $scopedInvitations = Invitation::factory(15)->for(Vacancy::factory()->for($this->employeeAuth->organization)->create())->create();

        $outOfScopeInvitations = Invitation::factory(15)->for(Vacancy::factory()->for(Organization::factory(), 'organization'))->create();

        $this->get(route('organization-api.invitations.show', $scopedInvitations->first()))
            ->assertSuccessful();

        $this->get(route('organization-api.invitations.show', $outOfScopeInvitations->first()))
            ->assertNotFound();
    }

    /** @test  */
    public function itShouldStoreInvitation(): void
    {
        $request_data = [
            'name' => 'ahmed badawy',
            'email' => 'ahmedbadawy.fcai@gmail.com',
            'mobile_country_code' => '+20',
            'mobile_number' => '1123456789',
            'vacancy_id' => Vacancy::factory()->createOne(['organization_id' => $this->employeeAuth->organization->getKey()])->getKey(),
            'should_be_invited_at' => now()->addDays(2)->format('Y-m-d H:i'),
            'expired_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ];

        $this->assertEmpty(Invitation::query()->get());

        $this->post(route('organization-api.invitations.store'), $request_data)
            ->assertSuccessful();

        $this->assertCount(1, Invitation::query()->get());
    }

    /** @test  */
    public function itShouldDeleteInvitation(): void
    {
        $invitation = Invitation::factory()->for(Vacancy::factory()->for($this->employeeAuth->organization, 'organization')->createOne())->createOne();

        $this->delete(route('organization-api.invitations.destroy', $invitation))
            ->assertSuccessful()
            ->assertJson(function (AssertableJson $json) {
                return $json->has('data.deleted_at')
                    ->whereType('data.deleted_at', 'string')
                    ->etc();
            });
    }
}
