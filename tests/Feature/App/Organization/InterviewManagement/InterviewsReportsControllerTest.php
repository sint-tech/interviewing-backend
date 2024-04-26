<?php

namespace Tests\Feature\App\Organization\InterviewManagement;

use Tests\TestCase;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Support\Collection;
use Domain\Candidate\Models\Candidate;
use Domain\Organization\Models\Employee;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Domain\ReportManagement\Models\InterviewReport;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;

class InterviewsReportsControllerTest extends TestCase
{
    use RefreshDatabase;

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
        $passedInterviewsCount = Interview::query()->wherePassed()->whereNotIn('id', Interview::query()->whereAccepted($this->vacancy->open_positions)->pluck('id'))->count();

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

    /** @test  */
    public function itShouldRetrieveWithdrewInterviewsReports()
    {
        $withdrewInterviewsCount = Interview::query()->whereStatus(InterviewStatusEnum::Withdrew)->count();

        $response = $this->get(route('organization.interviews.reports.index', ['filter[status]' => InterviewStatusEnum::Withdrew->value]));

        $response->assertSuccessful();

        $response->assertJsonCount($withdrewInterviewsCount, 'data');
    }

    /** @test  */
    public function itShouldReturnSelectedInterviewsAsAccepted()
    {
        $openPositions = $this->vacancy->open_positions;
        $rejectedInterview = Interview::query()->whereRejected()->first();

        $response = $this->get(route('organization.interviews.reports.index', ['filter[status]' => 'accepted', 'filter[vacancy_id]' => $this->vacancy->getKey()]));
        $this->assertFalse(collect($response->json('data'))->contains($rejectedInterview));

        $this->post(route('organization.interviews.change-status', $rejectedInterview->getKey()), [
            'status' => InterviewStatusEnum::Selected->value,
        ])->assertSuccessful();

        $acceptedInterviewsAfterChange = Interview::query()->whereAccepted($openPositions)->get();

        $this->assertTrue($acceptedInterviewsAfterChange->contains($rejectedInterview));

        $response = $this->get(route('organization.interviews.reports.index', ['filter[status]' => 'accepted', 'filter[vacancy_id]' => $this->vacancy->getKey()]));

        $response->assertSuccessful();

        $response->assertJsonCount($openPositions, 'data');

        $response->assertJsonFragment(['id' => $rejectedInterview->getKey()]);

        $passedInterviewsCount = $acceptedInterviewsAfterChange->filter(fn ($interview) => $interview->status === InterviewStatusEnum::Passed)->count();
        $selectedInterviewsCount = $acceptedInterviewsAfterChange->filter(fn ($interview) => $interview->status === InterviewStatusEnum::Selected)->count();

        $this->assertEquals($openPositions - $selectedInterviewsCount, $passedInterviewsCount);
    }
}
