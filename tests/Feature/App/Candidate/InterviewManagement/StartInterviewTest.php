<?php

namespace Tests\Feature\App\Candidate\InterviewManagement;

use Domain\Candidate\Models\Candidate;
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();
    }

    /** @test */
    public function itShouldStartNewInterviewForPassedVacancyAndInterviewTemplate(): void
    {
        $vacancy = Vacancy::factory()
            ->for($interviewTemplate = InterviewTemplate::factory()->createOne(), 'defaultInterviewTemplate')
            ->for($employee = Employee::factory()->createOne(), 'creator')
            ->for($employee->organization, 'organization')->createOne();

        $candidate = Candidate::factory()->createOne();

        $this->assertDatabaseMissing(table_name(Interview::class), [
            'interview_template_id' => $interviewTemplate->getKey(),
            'vacancy_id' => $vacancy->getKey(),
            'candidate_id' => $candidate->getKey(),
        ]);

        $response = $this->actingAs($candidate, 'api-candidate')
            ->post(route('candidate.interviews.start'), [
                'vacancy_id' => $vacancy->getKey(),
                'interview_template_id' => $interviewTemplate->getKey(),
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas(table_name(Interview::class), [
            'interview_template_id' => $interviewTemplate->getKey(),
            'vacancy_id' => $vacancy->getKey(),
            'candidate_id' => $candidate->getKey(),
        ]);
    }

    /** @test */
    public function itShouldStartInterviewWhenPassOnlyVacancyWhichHasDefaultInterviewTemplate(): void
    {

        $vacancy = Vacancy::factory()
            ->for(InterviewTemplate::factory()->createOne(), 'defaultInterviewTemplate')
            ->for($employee = Employee::factory()->createOne(), 'creator')
            ->for($employee->organization, 'organization')->createOne();

        $candidate = Candidate::factory()->createOne();

        $this->assertDatabaseMissing(table_name(Interview::class), [
            'vacancy_id' => $vacancy->getKey(),
            'candidate_id' => $candidate->getKey(),
        ]);

        $this->actingAs($candidate, 'api-candidate')
            ->post(route('candidate.interviews.start'), [
                'vacancy_id' => $vacancy->getKey(),
            ])->assertSuccessful();

        $this->assertDatabaseHas(table_name(Interview::class), [
            'interview_template_id' => $vacancy->defaultInterviewTemplate->getKey(),
            'vacancy_id' => $vacancy->getKey(),
            'candidate_id' => $candidate->getKey(),
        ]);
    }

    /** @test  */
    public function itShouldThrowValidationExceptionWhenInterviewTemplateDoesntOpenForVacancy(): void
    {
        $vacancy = Vacancy::factory()
            ->for(InterviewTemplate::factory()->createOne(), 'defaultInterviewTemplate')
            ->for($employee = Employee::factory()->createOne(), 'creator')
            ->for($employee->organization, 'organization')->createOne();

        $otherInterviewTemplate = InterviewTemplate::factory()->createOne();

        $this->actingAs(Candidate::factory()->createOne(), 'api-candidate')
            ->post(route('candidate.interviews.start'), [
                'vacancy_id' => $vacancy->getKey(),
                'interview_template_id' => $otherInterviewTemplate->getKey(),
            ])->assertUnprocessable()
            ->assertJsonValidationErrorFor('interview_template_id');
    }
}
