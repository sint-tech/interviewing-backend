<?php

namespace Tests\Feature\App\Candidate\InterviewManagement;

use Database\Seeders\SintAdminsSeeder;
use Domain\Candidate\Models\Candidate;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Organization\Models\Employee;
use Domain\Vacancy\Models\Vacancy;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StartInterviewTest extends TestCase
{
    use RefreshDatabase;

    protected Candidate $authCandidate;

    protected Vacancy $vacancy;

    protected InterviewTemplate $interviewTemplate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->seed(SintAdminsSeeder::class);

        $this->authCandidate = Candidate::factory()->createOne();

        $this->vacancy = Vacancy::factory()
            ->for($this->interviewTemplate = InterviewTemplate::factory()->createOne(), 'defaultInterviewTemplate')
            ->for($employee = Employee::factory()->createOne(), 'creator')
            ->for($employee->organization, 'organization')->createOne(['max_reconnection_tries' => 0, 'started_at' => now()->subDay()]);
    }

    /** @test */
    public function itShouldStartNewInterviewForPassedVacancyAndInterviewTemplate(): void
    {
        $candidate = $this->authCandidate;

        $this->assertDatabaseMissing(table_name(Interview::class), [
            'interview_template_id' => $this->interviewTemplate->getKey(),
            'vacancy_id' => $this->vacancy->getKey(),
            'candidate_id' => $candidate->getKey(),
        ]);

        $response = $this->actingAs($candidate, 'candidate')
            ->post(route('candidate.interviews.start'), [
                'vacancy_id' => $this->vacancy->getKey(),
                'interview_template_id' => $this->interviewTemplate->getKey(),
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas(table_name(Interview::class), [
            'interview_template_id' => $this->interviewTemplate->getKey(),
            'vacancy_id' => $this->vacancy->getKey(),
            'candidate_id' => $candidate->getKey(),
        ]);
    }

    /** @test */
    public function itShouldStartInterviewWhenPassOnlyVacancyWhichHasDefaultInterviewTemplate(): void
    {
        $this->assertDatabaseMissing(table_name(Interview::class), [
            'vacancy_id' => $this->vacancy->getKey(),
            'candidate_id' => $this->authCandidate->getKey(),
        ]);

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), [
                'vacancy_id' => $this->vacancy->getKey(),
            ])->assertSuccessful();

        $this->assertDatabaseHas(table_name(Interview::class), [
            'interview_template_id' => $this->vacancy->defaultInterviewTemplate->getKey(),
            'vacancy_id' => $this->vacancy->getKey(),
            'candidate_id' => $this->authCandidate->getKey(),
        ]);
    }

    /** @test  */
    public function itShouldThrowValidationExceptionWhenInterviewTemplateDoesntOpenForVacancy(): void
    {
        $otherInterviewTemplate = InterviewTemplate::factory()->createOne();

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), [
                'vacancy_id' => $this->vacancy->getKey(),
                'interview_template_id' => $otherInterviewTemplate->getKey(),
            ])->assertUnprocessable()
            ->assertJsonValidationErrorFor('interview_template_id');
    }

    /** @test  */
    public function itShouldNotContinueSameInterviewWhenHasRunningInterview()
    {
        $data = [
            'vacancy_id' => $this->vacancy->getKey(),
        ];

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();

        $data = [
            'vacancy_id' => Vacancy::factory()
                ->for($this->interviewTemplate = InterviewTemplate::factory()->createOne(), 'defaultInterviewTemplate')
                ->for($employee = Employee::factory()->createOne(), 'creator')
                ->for($employee->organization, 'organization')->createOne(['max_reconnection_tries' => 0, 'started_at'=> now()->subDay()])->id,
        ];

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();
    }

    /** @test  */
    public function itShouldStartNewInterviewAndWithdrewPreviousOne()
    {
        $data = [
            'vacancy_id' => $this->vacancy->getKey(),
        ];

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), $data)->assertUnprocessable();
    }

    /** @test */
    public function itShouldStartNewInterviewForCandidateExceptAuthCandidate()
    {
        $data = [
            'vacancy_id' => $this->vacancy->getKey(),
            'interview_template_id' => $this->interviewTemplate->getKey(),
        ];

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), $data)->assertUnprocessable();

        $this->actingAs(Candidate::factory()->createOne(), 'candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();
    }

    /** @test  */
    public function itShouldAllowAuthCandidateStartNewInterviewAfterFinishingRunningOne()
    {
        $data = [
            'vacancy_id' => $this->vacancy->getKey(),
        ];

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), $data)->assertUnprocessable();

        $this->assertCount(1, $this->authCandidate->interviews()->where('status', InterviewStatusEnum::Withdrew)->get());

        $data = [
            'vacancy_id' => Vacancy::factory()->createOne(['started_at'=> now()->subDay()])->id,
        ];

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();
    }

    /** @test  */
    public function itShouldContinueInterviewWhenConnectionTriesNotReachedTheLimit()
    {
        $data = [
            'vacancy_id' => $this->vacancy->getKey(),
            'interview_template_id' => $this->interviewTemplate->getKey(),
        ];

        $limit = 2;

        $this->vacancy->update(['max_reconnection_tries' => $limit]);

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), $data)->assertUnprocessable();
    }

    /** @test  */
    public function itShouldNotStartMoreThanOneInterviewOnTheSameVacancy()
    {
        $data = [
            'vacancy_id' => $this->vacancy->getKey(),
            'interview_template_id' => $this->interviewTemplate->getKey(),
        ];

        Interview::factory()->createOne(array_merge($data, [
            'candidate_id' => $this->authCandidate->id,
            'status' => InterviewStatusEnum::Withdrew,
            'ended_at' => now(),
        ]));

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), $data)
            ->assertConflict();

        //assert can start interview on new vacancy
        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), [
                'vacancy_id' => $new_vacancy_id = Vacancy::factory()->createOne([
                    'max_reconnection_tries' => 3,
                    'started_at' => now()->subDay(),
                ])->id,
            ])
            ->assertSuccessful();

        //assert can continue interview on the latest vacancy
        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), [
                'vacancy_id' => $new_vacancy_id,
            ])->assertSuccessful();
    }
    /** @test  */
    public function itShouldNotStartInterviewWhenVacancyIsNotStarted()
    {
        $this->vacancy->update(['started_at' => now()->addDay()]);

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), [
                'vacancy_id' => $this->vacancy->getKey(),
            ])->assertUnprocessable()
            ->assertJsonValidationErrorFor('vacancy_id');
    }

    /** @test  */
    public function itShouldNotStartInterviewWhenVacancyIsEnded()
    {
        $this->vacancy->update(['ended_at' => now()->subDay()]);

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), [
                'vacancy_id' => $this->vacancy->getKey(),
            ])->assertUnprocessable()
            ->assertJsonValidationErrorFor('vacancy_id');
    }

    /** @test  */
    public function itShouldMarkInvitationAsUsedWhenStartInterview()
    {
        $invitation = $this->vacancy->invitations()->create([
            'name' => $this->authCandidate->first_name,
            'email' => $this->authCandidate->email,
            'mobile_number' => 1208205900,
            'mobile_country_code' => $this->authCandidate->mobile_dial_code,
            'should_be_invited_at' => now()->subDay(),
            'batch' => 1,
            'creator_id' => $this->vacancy->creator->getKey(),
            'creator_type' => $this->vacancy->creator->getMorphClass(),
        ]);

        $this->assertNull($invitation->used_at);

        $this->actingAs($this->authCandidate, 'candidate')
            ->post(route('candidate.interviews.start'), [
                'vacancy_id' => $this->vacancy->getKey(),
            ])->assertSuccessful();

        $invitation->refresh();

        $this->assertNotNull($invitation->used_at);
    }
}
