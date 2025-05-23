<?php

namespace Tests\Feature\App\Organization\QuestionManagement;

use Tests\TestCase;
use Domain\Users\Models\User;
use Domain\JobTitle\Models\JobTitle;
use Database\Seeders\SintAdminsSeeder;
use Domain\Organization\Models\Employee;
use Domain\Organization\Models\Organization;
use Illuminate\Testing\Fluent\AssertableJson;
use Domain\QuestionManagement\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Domain\QuestionManagement\Models\QuestionVariant;

use Domain\InterviewManagement\Models\InterviewTemplate;
use Domain\QuestionManagement\Enums\QuestionVariantStatusEnum;
use Domain\InterviewManagement\Enums\InterviewTemplateAvailabilityStatusEnum;

class QuestionVariantControllerTest extends TestCase
{
    use RefreshDatabase;

    public Employee $employeeAuth;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(SintAdminsSeeder::class);

        $this->employeeAuth = Employee::factory()->createOne();

        $this->actingAs($this->employeeAuth, 'organization');
    }

    /** @test  */
    public function itShouldShowTheOrganizationQuestionVariants()
    {
        QuestionVariant::factory(30)->for($this->employeeAuth, 'creator')->create(['organization_id' => $this->employeeAuth->organization_id]);

        QuestionVariant::factory(30)->for(User::first(), 'creator')->create(['organization_id' => Organization::factory()->createOne()->getKey(), 'status' => QuestionVariantStatusEnum::Private->value]);

        $response = $this->get(route('organization.question-variants.index', ['per_page' => 1000]));
        $response->assertSuccessful();
        $response->assertJsonCount(30, 'data');
    }

    /** @test */
    public function itShouldShowOrganizationQuestionVariantsAndPublicQuestionVariants()
    {
        QuestionVariant::factory(20)->for($this->employeeAuth, 'creator')->create(['organization_id' => $this->employeeAuth->organization_id]);

        QuestionVariant::factory(20)->for(User::first(), 'creator')->create(['organization_id' => Organization::factory()->createOne()->getKey(), 'status' => QuestionVariantStatusEnum::Public->value]);

        $response = $this->get(route('organization.question-variants.index', ['per_page' => 1000]));

        $response->assertSuccessful();
        $response->assertJsonCount(40, 'data');
    }

    /** @test  */
    public function itShouldShowSingleQuestionVariant()
    {
        $questionVariant = QuestionVariant::factory(1)->for($this->employeeAuth, 'creator')->createOne(['organization_id' => $this->employeeAuth->organization_id]);

        $invalidQuestionVariant = QuestionVariant::factory(1)->for(User::first(), 'creator')->createOne(['organization_id' => Organization::factory()->createOne()->getKey(), 'status' => QuestionVariantStatusEnum::Private->value]);

        $this->get(route('organization.question-variants.show', $questionVariant))
            ->assertSuccessful();

        $this->get(route('organization.question-variants.show', $invalidQuestionVariant))
            ->assertNotFound();
    }

    /** @test  */
    public function itShouldStoreQuestionVariant(): void
    {
        $this->post(route('organization.question-variants.store'), [
            'text' => 'this is text',
            'description' => 'this is description',
            'question_id' => Question::factory()->for($this->employeeAuth, 'creator')->configure()->createOne()->getKey(),
            'status' => QuestionVariantStatusEnum::Public->value,
            'reading_time_in_seconds' => 60 * 3, // 3 minutes
            'answering_time_in_seconds' => 60 * 10, // 10 minutes
        ])->assertSuccessful();
    }

    /** @test  */
    public function itShouldNotStoreTwoQuestionVariantsWithSameText(): void
    {
        $question = Question::factory()->for($this->employeeAuth, 'creator')->configure()->createOne();

        QuestionVariant::factory()->for($this->employeeAuth, 'creator')->createOne(['organization_id' => $this->employeeAuth->organization_id, 'question_id' => $question->getKey(), 'text' => 'this is text']);

        $this->post(route('organization.question-variants.store'), [
            'text' => 'this is text',
            'description' => 'this is description',
            'question_id' => $question->getKey(),
            'status' => QuestionVariantStatusEnum::Public->value,
            'reading_time_in_seconds' => 60 * 3, // 3 minutes
            'answering_time_in_seconds' => 60 * 10, // 10 minutes
        ])->assertJsonValidationErrors('text');
    }

    /** @test  */
    public function itShouldUpdateQuestionVariant(): void
    {
        $questionVariant = QuestionVariant::factory()->for($this->employeeAuth, 'creator')->createOne(['organization_id' => $this->employeeAuth->organization_id, 'question_id' => Question::factory()->for($this->employeeAuth, 'creator')->configure()->createOne()->getKey()]);
        $anotherQuestionVariant = QuestionVariant::factory()->for($this->employeeAuth, 'creator')->createOne(['organization_id' => $this->employeeAuth->organization_id, 'question_id' => Question::factory()->for($this->employeeAuth, 'creator')->configure()->createOne()->getKey()]);

        $newQuestion = Question::factory()->for($this->employeeAuth, 'creator')->configure()->createOne();

        $this->put(route('organization.question-variants.update', $questionVariant), [
            'text' => $questionVariant->text,
            'description' => 'this is description updated',
            'question_id' => $newQuestion->getKey(),
            'status' => QuestionVariantStatusEnum::Public->value,
            'reading_time_in_seconds' => 40 * 3, // 2 minutes
            'answering_time_in_seconds' => 60 * 3, // 3 minutes
        ])->assertSuccessful()->assertJson(function (AssertableJson $json) use ($newQuestion, $questionVariant) {
            $json->where('data.text', $questionVariant->text)
                ->where('data.description', 'this is description updated')
                ->where('data.status', QuestionVariantStatusEnum::Public->value)
                ->where('data.reading_time_in_seconds', 40 * 3)
                ->where('data.answering_time_in_seconds', 60 * 3)
                ->where('data.question_id', $newQuestion->getKey());
        });

        $this->put(route('organization.question-variants.update', $questionVariant), [
            'text' => $anotherQuestionVariant->text,
            'description' => 'this is description updated',
            'question_id' => $newQuestion->getKey(),
            'status' => QuestionVariantStatusEnum::Public->value,
            'reading_time_in_seconds' => 40 * 3, // 2 minutes
            'answering_time_in_seconds' => 60 * 3, // 3 minutes
        ])->assertJsonValidationErrors('text');
    }

    /** @test  */
    public function itShouldNotLetAnotherOrganizationUpdateQuestionVariant(): void
    {

        $newQuestion = Question::factory()->for($this->employeeAuth, 'creator')->configure()->createOne();
        $questionVariant = QuestionVariant::factory()->for($this->employeeAuth, 'creator')->createOne([
            'organization_id' => $this->employeeAuth->organization_id,
            'question_id' => Question::factory()->for($this->employeeAuth, 'creator')->configure()->createOne()->getKey(),
            'status' => QuestionVariantStatusEnum::Public->value
        ]);

        $anotherEmployee = Employee::factory()->createOne();

        $this->actingAs($anotherEmployee, 'organization')->put(route('organization.question-variants.update', $questionVariant->id), [
            'text' => 'this is text updated',
            'description' => 'this is description updated',
            'question_id' => $newQuestion->getKey(),
            'status' => QuestionVariantStatusEnum::Public->value,
            'reading_time_in_seconds' => 40 * 3, // 2 minutes
            'answering_time_in_seconds' => 60 * 3, // 3 minutes
        ])->assertForbidden();
    }

    /** @test  */
    public function itShouldNotUpdatePublicQuestionVariantIfInInterviewTemplate(): void
    {
        $questionVariant = QuestionVariant::factory()->for($this->employeeAuth, 'creator')->createOne([
            'organization_id' => $this->employeeAuth->organization_id,
            'status' => QuestionVariantStatusEnum::Public->value
        ]);

        $interviewTemplate = InterviewTemplate::create([
            'name' => 'test',
            'availability_status' => InterviewTemplateAvailabilityStatusEnum::Available->value,
            'targeted_job_title_id' => JobTitle::factory()->createOne()->getKey(),
            'creator_id' => $this->employeeAuth->getKey(),
            'creator_type' => $this->employeeAuth->getMorphClass(),
            'organization_id' => $this->employeeAuth->organization_id,
        ]);

        $interviewTemplate->questionVariants()->attach($questionVariant);

        $newQuestion = Question::factory()->for($this->employeeAuth, 'creator')->configure()->createOne();

        $this->put(route('organization.question-variants.update', $questionVariant), [
            'text' => 'this is text updated',
            'description' => 'this is description updated',
            'question_id' => $newQuestion->getKey(),
            'reading_time_in_seconds' => 40 * 3,
            'answering_time_in_seconds' => 60 * 3,
        ])->assertJsonValidationErrorFor('status');
    }

    /** @test */
    public function itShouldNotUpdatePrivateQuestionVariantIfInterviewTemplateHasVacancyRunning()
    {
        $questionVariant = QuestionVariant::factory()->for($this->employeeAuth, 'creator')->createOne([
            'organization_id' => $this->employeeAuth->organization_id,
            'status' => QuestionVariantStatusEnum::Private->value
        ]);

        $interviewTemplate = InterviewTemplate::create([
            'name' => 'test',
            'availability_status' => InterviewTemplateAvailabilityStatusEnum::Available->value,
            'targeted_job_title_id' => JobTitle::factory()->createOne()->getKey(),
            'creator_id' => $this->employeeAuth->getKey(),
            'creator_type' => $this->employeeAuth->getMorphClass(),
            'organization_id' => $this->employeeAuth->organization_id,
        ]);

        $interviewTemplate->questionVariants()->attach($questionVariant);

        $interviewTemplate->vacancies()->create([
            'title' => 'test',
            'description' => 'test',
            'open_positions' => 1,
            'creator_id' => $this->employeeAuth->getKey(),
            'creator_type' => $this->employeeAuth->getMorphClass(),
            'organization_id' => $this->employeeAuth->organization_id,
            'started_at' => now()->subDay(),
            'ended_at' => now()->addDay(),
        ]);

        $newQuestion = Question::factory()->for($this->employeeAuth, 'creator')->configure()->createOne();

        $this->put(route('organization.question-variants.update', $questionVariant), [
            'text' => 'this is text updated',
            'description' => 'this is description updated',
            'question_id' => $newQuestion->getKey(),
            'reading_time_in_seconds' => 40 * 3,
            'answering_time_in_seconds' => 60 * 3,
        ])->assertJsonValidationErrorFor('status');
    }

    /** @test  */
    public function itShouldLetAdminUpdateQuestionVariant(): void
    {
        $questionVariant = QuestionVariant::factory()->for($this->employeeAuth, 'creator')->createOne(['organization_id' => $this->employeeAuth->organization_id, 'status' => QuestionVariantStatusEnum::Public->value]);

        $newQuestion = Question::factory()->for($this->employeeAuth, 'creator')->configure()->createOne();
        $this->actingAs(User::first(), 'admin')->put(route('organization.question-variants.update', $questionVariant), [
            'text' => 'this is text updated',
            'description' => 'this is description updated',
            'question_id' => $newQuestion->getKey(),
            'status' => QuestionVariantStatusEnum::Public->value,
            'reading_time_in_seconds' => 40 * 3, // 2 minutes
            'answering_time_in_seconds' => 60 * 3, // 3 minutes
        ])->assertSuccessful()->assertJson(function (AssertableJson $json) use ($newQuestion) {
            $json->where('data.text', 'this is text updated')
                ->where('data.description', 'this is description updated')
                ->where('data.status', QuestionVariantStatusEnum::Public->value)
                ->where('data.reading_time_in_seconds', 40 * 3)
                ->where('data.answering_time_in_seconds', 60 * 3)
                ->where('data.question_id', $newQuestion->getKey());
        });
    }

    /** @test  */
    public function itShouldDeleteQuestionVariant(): void
    {
        $questionVariant = QuestionVariant::factory()->for($this->employeeAuth, 'creator')->createOne(['organization_id' => $this->employeeAuth->organization_id]);

        $this->actingAs($this->employeeAuth, 'organization')->delete(route('organization.question-variants.destroy', $questionVariant->id))->assertSuccessful();
    }

    /** @test */
    public function itShouldNotDeleteQuestionVariantIfInInterviewTemplate()
    {
        $questionVariant = QuestionVariant::factory()->for($this->employeeAuth, 'creator')->createOne(['organization_id' => $this->employeeAuth->organization_id]);

        $interviewTemplate = InterviewTemplate::create([
            'name' => 'test',
            'availability_status' => InterviewTemplateAvailabilityStatusEnum::Available->value,
            'targeted_job_title_id' => JobTitle::factory()->createOne()->getKey(),
            'creator_id' => $this->employeeAuth->getKey(),
            'creator_type' => $this->employeeAuth->getMorphClass(),
            'organization_id' => $this->employeeAuth->organization_id,
        ]);

        $interviewTemplate->questionVariants()->attach($questionVariant);

        $this->actingAs($this->employeeAuth, 'organization')->delete(route('organization.question-variants.destroy', $questionVariant->id))
            ->assertConflict();
    }

    /** @test  */
    public function itShouldNotLetAnotherOrganizationDeleteQuestionVariant(): void
    {
        $questionVariant = QuestionVariant::factory()->for($this->employeeAuth, 'creator')->createOne(['organization_id' => $this->employeeAuth->organization_id, 'status' => QuestionVariantStatusEnum::Public->value]);

        $anotherEmployee = Employee::factory()->createOne();

        $this->actingAs($anotherEmployee, 'organization')->delete(route('organization.question-variants.destroy', $questionVariant->id))->assertForbidden();
    }

    /** @test  */
    public function itShouldLetAdminDeleteQuestionVariant(): void
    {
        $questionVariant = QuestionVariant::factory()->for($this->employeeAuth, 'creator')->createOne(['organization_id' => $this->employeeAuth->organization_id, 'status' => QuestionVariantStatusEnum::Public->value]);

        $this->actingAs(User::first(), 'admin')->delete(route('organization.question-variants.destroy', $questionVariant->id))->assertSuccessful();
    }
}
