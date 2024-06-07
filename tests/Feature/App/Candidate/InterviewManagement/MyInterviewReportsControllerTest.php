<?php

namespace Tests\Feature\App\Candidate\InterviewManagement;

use Tests\TestCase;
use Domain\Vacancy\Models\Vacancy;
use Database\Seeders\SintAdminsSeeder;
use Domain\Candidate\Models\Candidate;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\Organization\Models\Employee;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\ReportManagement\Models\InterviewReport;

class MyInterviewReportsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Candidate $authCandidate;
    protected Vacancy $vacancy;
    protected InterviewTemplate $interviewTemplate;
    protected Interview $interview;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SintAdminsSeeder::class);

        $this->authCandidate = Candidate::factory()->createOne();

        $this->vacancy = Vacancy::factory()
            ->for($this->interviewTemplate = InterviewTemplate::factory()->createOne(), 'defaultInterviewTemplate')
            ->for($employee = Employee::factory()->createOne(), 'creator')
            ->for($employee->organization, 'organization')->createOne(['max_reconnection_tries' => 0, 'started_at' => now()->subDay(), 'ended_at' => now()->addDay()]);

        Interview::factory([
            'status' => InterviewStatusEnum::Passed->value,
        ])
            ->for($this->vacancy)
            ->for($this->authCandidate)
            ->has(InterviewReport::factory(), 'defaultLastReport')
            ->create();

        $this->actingAs($this->authCandidate, 'candidate');
    }

    /** @test  */
    public function itShouldReturnCandidateInterviewReportsWhereVacancyEnded(): void
    {
        $this->setVacancyEndDate(now()->subDay());

        $this->get(route('candidate.interviews.reports'))
            ->assertJsonCount(1, 'data');
    }

    /** @test  */
    public function itShouldNotReturnCandidateInterviewReportsWhereVacancyNotEnded(): void
    {
        $this->setVacancyEndDate(now()->addDay());

        $this->get(route('candidate.interviews.reports'))
            ->assertJsonCount(0, 'data');
    }

    private function setVacancyEndDate($date)
    {
        $this->vacancy->update(['ended_at' => $date]);
    }
}
