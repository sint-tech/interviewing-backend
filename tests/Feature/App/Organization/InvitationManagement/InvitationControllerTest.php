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
    public function itShouldNotSendInvitationWhenLimitExceeded(): void
    {
        $this->employeeAuth->organization->update([
            'consumption' => 1,
            'limit' => 1,
        ]);

        $request_data = [
            'name' => 'ahmed badawy',
            'email' => 'ahmedbadawy.fcai@gmail.com',
            'mobile_country_code' => '+20',
            'mobile_number' => '1123456789',
            'vacancy_id' => Vacancy::factory()->for($this->employeeAuth, 'creator')->createOne(['organization_id' => $this->employeeAuth->organization->getKey()])->getKey(),
            'should_be_invited_at' => now()->addDays(2)->format('Y-m-d H:i'),
            'expired_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ];

        $this->post(route('organization.invitations.store'), $request_data)->assertJsonValidationErrorFor('vacancy_id');

        $this->assertDatabaseCount('invitations', 0);
    }
}
