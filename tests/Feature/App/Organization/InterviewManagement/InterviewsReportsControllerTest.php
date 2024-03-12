<?php

namespace Tests\Feature\App\Organization\InterviewManagement;

use Tests\TestCase;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Support\Collection;
use Domain\Candidate\Models\Candidate;
use Domain\Organization\Models\Employee;
use Domain\InterviewManagement\Models\Interview;
use Domain\ReportManagement\Models\InterviewReport;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;

class InterviewsReportsControllerTest extends TestCase
{
    use DatabaseMigrations;

    public Employee $employeeAuth;

    public InterviewTemplate $interviewTemplate;


    public Vacancy $vacancy;

    public Collection $candidates;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->employeeAuth = Employee::factory()->createOne();

        $this->employeeAuth = Employee::factory()->createOne();

        $this->actingAs($this->employeeAuth, 'organization');

        $this->interviewTemplate = InterviewTemplate::factory()->createOne([
            'organization_id' => $this->employeeAuth->organization_id,
            'availability_status' => InterviewTemplateAvailabilityStatusEnum::Available,
            'creator_id' => $this->employeeAuth->getKey(),
            'creator_type' => $this->employeeAuth->getMorphClass(),
        ]);

        $this->vacancy = Vacancy::factory()->createOne([
            'organization_id' => $this->employeeAuth->organization_id,
            'creator_id' => $this->employeeAuth->getKey(),
            'creator_type' => $this->employeeAuth->getMorphClass(),
            'interview_template_id' => $this->interviewTemplate->getKey(),
            'open_positions' => fake()->numberBetween(1, 10),
        ]);

        $minPassedInterviews = $this->vacancy->open_positions;

        Candidate::factory($minPassedInterviews)->create()->each(function ($candidate) {
            $interview = Interview::factory()->createOne([
                'candidate_id' => $candidate->getKey(),
                'vacancy_id' => $this->vacancy->getKey(),
                'interview_template_id' => $this->interviewTemplate->getKey(),
                'status' => InterviewStatusEnum::Passed,
            ]);

            InterviewReport::factory()->createOne([
                'reportable_id' => $interview->getKey(),
                'reportable_type' => $interview->getMorphClass(),
            ]);
        });

        Candidate::factory(20)->create()->each(function ($candidate) {
            $interview = Interview::factory()->createOne([
                'candidate_id' => $candidate->getKey(),
                'vacancy_id' => $this->vacancy->getKey(),
                'interview_template_id' => $this->interviewTemplate->getKey(),
                'status' => fake()->randomElement(InterviewStatusEnum::endedStatuses()),
            ]);

            InterviewReport::factory()->createOne([
                'reportable_id' => $interview->getKey(),
                'reportable_type' => $interview->getMorphClass(),
            ]);
        });
    }

    /** @test  */
    public function itShouldRetrieveAllInterviewsReports()
    {
        $interviewsCount = Interview::query()->withWhereHas('defaultLastReport')->count();

        $response = $this->get(route('organization.interviews.reports.index'));

        $response->assertSuccessful();

        $response->assertJsonCount($interviewsCount, 'data');
    }

    /** @test  */
    public function itShouldRetrievePassedInterviewsReports()
    {
        $passedInterviewsCount = Interview::query()->orderByAvgScoreDesc()->whereNotIn('id', Interview::query()->whereAccepted($this->vacancy->open_positions)->pluck('id'))->count();

        $response = $this->get(route('organization.interviews.reports.index', ['filter[status]' => 'passed', 'filter[vacancy_id]' => $this->vacancy->getKey()]));

        $response->assertSuccessful();

        $response->assertJsonCount($passedInterviewsCount, 'data');
    }

    /** @test  */
    public function itShouldRetrieveTopAcceptedInterviewsReports()
    {

        $response = $this->get(route('organization.interviews.reports.index', ['filter[status]' => 'accepted', 'filter[vacancy_id]' => $this->vacancy->getKey()]));

        $response->assertSuccessful();

        $response->assertJsonCount($this->vacancy->open_positions, 'data');
    }

    /** @test  */
    public function itShouldRetrieveCancelledInterviewsReports()
    {
        $cancelledInterviewsCount = Interview::query()->whereStatus(InterviewStatusEnum::Canceled)->count();

        $response = $this->get(route('organization.interviews.reports.index', ['filter[status]' => InterviewStatusEnum::Canceled->value]));

        $response->assertSuccessful();

        $response->assertJsonCount($cancelledInterviewsCount, 'data');
    }

    /** @test  */
    public function itShouldRetrieveRejectedInterviewsReports()
    {
        $rejectedInterviewsCount = Interview::query()->whereStatus(InterviewStatusEnum::Rejected)->count();

        $response = $this->get(route('organization.interviews.reports.index', ['filter[status]' => InterviewStatusEnum::Rejected->value]));

        $response->assertSuccessful();

        $response->assertJsonCount($rejectedInterviewsCount, 'data');
    }

}
