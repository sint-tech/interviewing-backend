<?php

namespace Tests\Feature\App\Organization\InvitationManagement;

use Domain\Invitation\Models\Invitation;
use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;
use Domain\Users\Models\User;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvitationControllerTest extends TestCase
{
    use RefreshDatabase;

    public Employee $employeeAuth;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employeeAuth = Employee::factory()->createOne();

        $this->actingAs($this->employeeAuth, 'organization');
    }

    /** @test  */
    public function itShouldShowAllInvitationsForAuthEmployee(): void
    {
        Invitation::factory(15)->for($this->employeeAuth, 'creator')->for(Vacancy::factory()->for($this->employeeAuth, 'creator')->for($this->employeeAuth->organization)->create())->create();

        Invitation::factory(15)->for(User::factory(), 'creator')->for(Vacancy::factory()->for(User::factory(), 'creator')->for(Organization::factory(), 'organization')->create())->create();

        $this->get(route('organization.invitations.index'))
            ->assertSuccessful()
            ->assertJsonCount(15, 'data');
    }

    /** @test  */
    public function itShouldShowInvitation(): void
    {
        $scopedInvitations = Invitation::factory(15)->for($this->employeeAuth, 'creator')->for(Vacancy::factory()->for($this->employeeAuth, 'creator')->for($this->employeeAuth->organization)->create())->create();

        $outOfScopeInvitations = Invitation::factory(15)->for(User::factory(), 'creator')->for(Vacancy::factory()->for(User::factory(), 'creator')->for(Organization::factory(), 'organization'))->create();

        $this->get(route('organization.invitations.show', $scopedInvitations->first()))
            ->assertSuccessful();

        $this->get(route('organization.invitations.show', $outOfScopeInvitations->first()))
            ->assertNotFound();
    }

    /** @test  */
    public function itShouldStoreInvitation(): void
    {
        $this->assertEmpty(Invitation::query()->get());

        $this->post(route('organization.invitations.store'), $this->requestData())
            ->assertSuccessful();

        $this->assertCount(1, Invitation::query()->get());
    }

    /** @test  */
    public function itShouldStoreInvitationWithMobileCountryCodeWithoutPlusSign(): void
    {
        $request_data = [
            'name' => 'ahmed badawy',
            'email' => 'ahmedbadawy.fcai@gmail.com',
            'mobile_country_code' => '20',
            'mobile_number' => '1123456789',
            'vacancy_id' => Vacancy::factory()->for($this->employeeAuth, 'creator')->createOne(['organization_id' => $this->employeeAuth->organization->getKey()])->getKey(),
            'should_be_invited_at' => now()->addDays(2)->format('Y-m-d H:i'),
            'expired_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ];

        $this->assertEmpty(Invitation::query()->get());

        $this->post(route('organization.invitations.store'), $request_data)
            ->assertSuccessful();

        $this->assertCount(1, Invitation::query()->get());
    }

    /** @test  */
    public function itShouldNotStoreInvitationForExistEmailBeforeInOtherInvitation()
    {
        $request_data = $this->requestData();

        $this->post(route('organization.invitations.store'), $request_data)
            ->assertSuccessful();

        $this->post(route('organization.invitations.store'), $request_data)
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('vacancy_id');
    }

    /** @test  */
    public function itShouldDeleteInvitation(): void
    {
        $invitation = Invitation::factory()->for($this->employeeAuth, 'creator')->for(Vacancy::factory()->for($this->employeeAuth, 'creator')->for($this->employeeAuth->organization, 'organization')->createOne())->createOne();

        $this->delete(route('organization.invitations.destroy', $invitation))
            ->assertSuccessful()
            ->assertJson(function (AssertableJson $json) {
                return $json->has('data.deleted_at')
                    ->whereType('data.deleted_at', 'string')
                    ->etc();
            });
    }

    /** @test  */
    public function itShouldNotSendInvitationWhenLimitExceeded(): void
    {
        $this->employeeAuth->organization->update([
            'interview_consumption' => 1,
            'limit' => 1,
        ]);

        $this->post(route('organization.invitations.store'), $this->requestData())->assertJsonValidationErrorFor('vacancy_id');

        $this->assertDatabaseCount('invitations', 0);
    }

        /** @test  */
        public function itShouldAddConsumptionWhenInvitationIsCreated(): void
        {
            $this->post(route('organization.invitations.store'), $this->requestData())->assertSuccessful();

            $this->assertEquals(1, $this->employeeAuth->refresh()->organization->interview_consumption);
        }

        /** @test  */
        public function itShouldNotCountInvitationAsConsumptionWhenInvitationIsUsedAndVacancyIsEnded(): void
        {
            $this->post(route('organization.invitations.store'), $this->requestData(
                vacancy: $vacancy = Vacancy::factory()->for($this->employeeAuth, 'creator')->createOne(['organization_id' => $this->employeeAuth->organization->getKey()])
            ))->assertSuccessful();

            $this->assertEquals(1, $this->employeeAuth->refresh()->organization->interview_consumption);

            $vacancy->update(['ended_at' => now()]);

            $this->assertEquals(false, $this->employeeAuth->refresh()->organization->limitExceeded());
        }

        private function requestData(
            string $name = 'ahmed badawy',
            string $email = 'ahmedbadawy.fcai@gmail.com',
            string $mobile_country_code = '+20',
            string $mobile_number = '1123456789',
            Vacancy $vacancy = null,
            string $should_be_invited_at = null,
            string $expired_at = null
        ) {
            $vacancy = $vacancy ?? Vacancy::factory()->for($this->employeeAuth, 'creator')->createOne(['organization_id' => $this->employeeAuth->organization->getKey()]);
            $should_be_invited_at = $should_be_invited_at ?? now()->addDays(2)->format('Y-m-d H:i');
            $expired_at = $expired_at ?? now()->addDays(5)->format('Y-m-d H:i');

            return [
                'name' => $name,
                'email' => $email,
                'mobile_country_code' => $mobile_country_code,
                'mobile_number' => $mobile_number,
                'vacancy_id' => $vacancy->getKey(),
                'should_be_invited_at' => $should_be_invited_at,
                'expired_at' => $expired_at,
            ];
        }
}
