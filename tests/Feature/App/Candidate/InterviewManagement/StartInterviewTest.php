<?php

namespace Tests\Feature\App\Candidate\InterviewManagement;

use Domain\Candidate\Models\Candidate;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Models\Interview;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\Organization\Models\Employee;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class StartInterviewTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation;

    protected Candidate $authCandidate;

    protected Vacancy $vacancy;

    protected InterviewTemplate $interviewTemplate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();

        $this->authCandidate = Candidate::factory()->createOne();

        $this->vacancy = Vacancy::factory()
            ->for($this->interviewTemplate = InterviewTemplate::factory()->createOne(), 'defaultInterviewTemplate')
            ->for($employee = Employee::factory()->createOne(), 'creator')
            ->for($employee->organization, 'organization')->createOne(['max_reconnection_tries' => 0]);
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

        $response = $this->actingAs($candidate, 'api-candidate')
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

        $this->actingAs($this->authCandidate, 'api-candidate')
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

        $this->actingAs($this->authCandidate, 'api-candidate')
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

        $this->actingAs($this->authCandidate, 'api-candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();

        $data = [
            'vacancy_id' => Vacancy::factory()
                ->for($this->interviewTemplate = InterviewTemplate::factory()->createOne(), 'defaultInterviewTemplate')
                ->for($employee = Employee::factory()->createOne(), 'creator')
                ->for($employee->organization, 'organization')->createOne(['max_reconnection_tries' => 0])->id,
        ];

        $this->actingAs($this->authCandidate, 'api-candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();
    }

    /** @test  */
    public function itShouldStartNewInterviewAndWithdrewPreviousOne()
    {
        $data = [
            'vacancy_id' => $this->vacancy->getKey(),
        ];

        $this->actingAs($this->authCandidate, 'api-candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();

        $this->actingAs($this->authCandidate, 'api-candidate')
            ->post(route('candidate.interviews.start'), $data)->assertUnprocessable();
    }

    /** @test */
    public function itShouldStartNewInterviewForCandidateExceptAuthCandidate()
    {
        $data = [
            'vacancy_id' => $this->vacancy->getKey(),
            'interview_template_id' => $this->interviewTemplate->getKey(),
        ];

        $this->actingAs($this->authCandidate, 'api-candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();

        $this->actingAs($this->authCandidate, 'api-candidate')
            ->post(route('candidate.interviews.start'), $data)->assertUnprocessable();

        $this->actingAs(Candidate::factory()->createOne(), 'api-candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();
    }

    /** @test  */
    public function itShouldAllowAuthCandidateStartNewInterviewAfterFinishingRunningOne()
    {
        $data = [
            'vacancy_id' => $this->vacancy->getKey(),
            'interview_template_id' => $this->interviewTemplate->getKey(),
        ];

        $this->actingAs($this->authCandidate, 'api-candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();

        $this->actingAs($this->authCandidate, 'api-candidate')
            ->post(route('candidate.interviews.start'), $data)->assertUnprocessable();

        $this->assertCount(1, $this->authCandidate->interviews()->where('status', InterviewStatusEnum::Withdrew)->get());

        $this->actingAs($this->authCandidate, 'api-candidate')
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

        $this->actingAs($this->authCandidate, 'api-candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();

        $this->actingAs($this->authCandidate, 'api-candidate')
            ->post(route('candidate.interviews.start'), $data)->assertSuccessful();

        $this->actingAs($this->authCandidate, 'api-candidate')
            ->post(route('candidate.interviews.start'), $data)->assertUnprocessable();
    }
}
