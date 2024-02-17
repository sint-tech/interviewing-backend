<?php

namespace Tests\Feature\App\Organization\InterviewManagement;

use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;
use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\JobTitle\Models\JobTitle;
use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;
use Domain\QuestionManagement\Models\Question;
use Domain\QuestionManagement\Models\QuestionCluster;
use Domain\QuestionManagement\Models\QuestionVariant;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;
use Tests\TestCase;

class InterviewTemplateControllerTest extends TestCase
{
    use DatabaseMigrations;

    public Employee $employeeAuth;

    public Collection $questionVariants;

    protected User $sintUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->employeeAuth = Employee::factory()->createOne();

        $this->questionVariants = QuestionVariant::factory(5)
            ->for($this->employeeAuth, 'creator')
            ->for($this->employeeAuth->organization, 'organization')
            ->create();

        $this->actingAs($this->employeeAuth, 'organization');

        $this->sintUser = User::factory()->createOne();
        $this->actingAs($this->sintUser, 'admin');
    }

    /** @test  */
    public function itShouldShowOnlyInterviewTemplateBelongsToTheAuthOrganization()
    {
        InterviewTemplate::factory(25)->create([
            'organization_id' => $this->employeeAuth->organization_id,
        ]);

        InterviewTemplate::factory(30)->create([
            'organization_id' => Organization::factory()->createOne()->getKey(),
        ]);

        $response = $this->get(route('organization.interview-templates.index'), ['per_page' => 1000]);
        $response->assertSuccessful();
        $response->assertJsonCount(25, 'data');
    }

    /** @test  */
    public function itShouldShowSingleInterviewTemplateBelongsToAuthOrganization()
    {
        $organizationInterviewTemplate = InterviewTemplate::factory()->createOne(['organization_id' => $this->employeeAuth->organization_id]);
        $otherInterviewTemplate = InterviewTemplate::factory()->createOne(['organization_id' => Organization::factory()->createOne()->getKey()]);

        $response = $this->get(route('organization.interview-templates.show', $organizationInterviewTemplate));
        $response->assertSuccessful();

        $this->get(route('organization.interview-templates.show', $otherInterviewTemplate))->assertNotFound();
    }

    /** @test  */
    public function itShouldShowInterviewTemplateWithIncludes(): void
    {
        $interviewTemplate = InterviewTemplate::factory()->createOne([
            'organization_id' => $this->employeeAuth->organization_id,
        ]);

        $response = $this->get(route('organization.interview-templates.show', [
            'interview_template' => $interviewTemplate->getKey(),
            'include' => 'questionVariants,questionClusters,questionClusters.skills,questionClusters.questions',
        ]));

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'question_variants',
                'question_clusters' => [
                    '*' => [
                        'skills',
                        'questions' => [
                            '*' => [
                                'question_variants',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
    /** @test  */
    public function itShouldMatchQuestionVariantIds(): void
    {
        $interviewTemplate = InterviewTemplate::factory()->createOne([
            'organization_id' => $this->employeeAuth->organization_id,
        ]);

        $questionClusters = QuestionCluster::factory(3)->create([
            'creator_type' => 'admin',
            'creator_id' => $this->sintUser->getKey(),
        ]);

        $questionClusters->each(function (QuestionCluster $questionCluster) {
            Question::factory(1)->create([
                'question_cluster_id' => $questionCluster->getKey(),
                'creator_type' => 'admin',
                'creator_id' => $this->sintUser->getKey(),
            ]);
        });

        $questions = Question::query()->get();

        $questions->each(function (Question $question) {
            QuestionVariant::factory(1)->create([
                'question_id' => $question->getKey(),
                'creator_type' => 'organization',
                'creator_id' => $this->employeeAuth->getKey(),
                'organization_id' => $this->employeeAuth->organization_id,
            ]);
        });

        $questionVariants = QuestionVariant::whereNotNull('question_id')->get();

        $questionVariants->each(function (QuestionVariant $questionVariant) use ($interviewTemplate) {
            $interviewTemplate->questionVariants()->attach($questionVariant, [
                'question_cluster_id' => $questionVariant->question->questionCluster->getKey(),
            ]);
        });

        $response = $this->get(route('organization.interview-templates.show', [
            'interview_template' => $interviewTemplate->getKey(),
            'include' => 'questionVariants,questionClusters,questionClusters.skills,questionClusters.questions',
        ]));

        $this->assertEquals(
            $interviewTemplate->questionVariants->pluck('id')->toArray(),
            collect($response->json('data.question_clusters.*.questions.*.question_variants.*.id'))->flatten()->toArray()
        );
    }
    /** @test  */
    public function itShouldStoreInterviewTemplate(): void
    {
        $this->assertCount(0, InterviewTemplate::query()->get());

        $this->post(route('organization.interview-templates.store'), [
            'name' => 'testing name',
            'description' => null,
            'availability_status' => InterviewTemplateAvailabilityStatusEnum::Available->value,
            'reusable' => 1,
            'job_profile_id' => JobTitle::factory()->createOne()->getKey(),
            'question_variants' => [
                $this->questionVariants->first()->getKey(),
                $this->questionVariants->last()->getKey(),
            ],
        ])->assertSuccessful();

        $this->assertCount(1, InterviewTemplate::query()->get());
    }

    /** @test  */
    public function itShouldCreateInterviewTemplateForParentInterviewTemplate(): void
    {
        $this->post(route('organization.interview-templates.store'), [
            'name' => 'testing name',
            'description' => null,
            'availability_status' => InterviewTemplateAvailabilityStatusEnum::Available->value,
            'reusable' => 1,
            'job_profile_id' => JobTitle::factory()->createOne()->getKey(),
            'question_variants' => [
                $this->questionVariants->first()->getKey(),
                $this->questionVariants->last()->getKey(),
            ],
            'parent_id' => InterviewTemplate::factory()->create([
                'organization_id' => $this->employeeAuth->organization_id,
            ])->getKey(),
        ])->assertSuccessful();

        $this->assertNotNull(InterviewTemplate::query()->latest('id')->first()->parent_id);
    }

    /** @test  */
    public function itShouldUpdateVacancy()
    {
        $interview_template = InterviewTemplate::factory()->for($this->employeeAuth, 'creator')->createOne([
            'organization_id' => $this->employeeAuth->organization_id,
        ]);

        $response = $this->put(route('organization.interview-templates.update', $interview_template));

        $response->assertSuccessful();
    }
}
