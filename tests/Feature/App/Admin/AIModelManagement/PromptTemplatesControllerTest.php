<?php

namespace Tests\Feature\App\Admin\AIModelManagement;

use Tests\TestCase;
use Domain\Users\Models\User;
use Database\Seeders\SintAdminsSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Domain\AiPromptMessageManagement\Models\PromptTemplate;
use Domain\AiPromptMessageManagement\Enums\PromptTemplateVariableEnum;

class PromptTemplatesControllerTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    protected User $sintUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->seed(SintAdminsSeeder::class);

        $this->sintUser = User::query()->first();

        $this->actingAs($this->sintUser, 'admin');
    }

    /** @test */
    public function itShouldShowAllPromptTemplates()
    {
        PromptTemplate::factory(40)->create();

        $response = $this->get(route('admin.prompt-templates.index'));
        $response->assertSuccessful();
        $response->assertJsonCount(25, 'data');
    }

    /** @test */
    public function itShouldStorePromptTemplate()
    {
        $response = $this->post(route('admin.prompt-templates.store'), $this->requestData());
        $response->assertSuccessful();
    }

    /** @test */
    public function itShouldUpdatePromptTemplate()
    {
        $prompt_template = PromptTemplate::factory()->createOne($this->requestData());

        $response = $this->put(route('admin.prompt-templates.update', $prompt_template->id), $this->requestData([
            'text' => 'updated text _JOB_TITLE_',
        ]));

        $response->assertSuccessful();

        $this->assertDatabaseHas('prompt_templates', [
            'id' => $prompt_template->id,
            'text' => 'updated text _JOB_TITLE_',
        ]);
    }

    /** @test */
    public function itShouldValidatePromptTemplateStoreRequest()
    {
        $response = $this->post(route('admin.prompt-templates.store'), $this->requestData([
            'text' => 'invalid text',
            'stats_text' => 'invalid stats text',
        ]));
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['text', 'stats_text']);
    }

    /** @test */
    public function itShouldIncrementVersionIfSameTemplateNameExists()
    {
        $response = $this->post(route('admin.prompt-templates.store'), $this->requestData());
        $response->assertSuccessful();

        $response = $this->post(route('admin.prompt-templates.store'), $this->requestData());
        $response->assertSuccessful();

        $response = $this->post(route('admin.prompt-templates.store'), $this->requestData());
        $response->assertSuccessful();

        $response = $this->get(route('admin.prompt-templates.index'));
        $response->assertSuccessful();

        $this->assertDatabaseHas('prompt_templates', [
            'name' => $this->requestData()['name'],
            'version' => 3,
        ]);
    }

    /** @test */
    public function itShouldReplaceVariablesInText()
    {
        $impact_template = PromptTemplate::factory()->create($this->requestData());

        $impact_template_content = str_replace(
            [PromptTemplateVariableEnum::JobTitle->value],
            ['Software Engineer'],
            $impact_template->text
        );

        $impact_template_content .= str_replace(
            [PromptTemplateVariableEnum::QuestionClusterName->value, PromptTemplateVariableEnum::QuestionClusterAvgScore->value],
            ['Problem Solving', 80],
            $impact_template->stats_text
        );

        $impact_template_content .= $impact_template->conclusion_text;

        $this->assertStringContainsString('Software Engineer', $impact_template_content);
        $this->assertStringContainsString('Problem Solving', $impact_template_content);
        $this->assertStringContainsString('80', $impact_template_content);
        $this->assertStringContainsString('impacts in bullets point in html format:', $impact_template->conclusion_text);
    }

    /** @test */
    public function itShouldOnlyHaveOneSelectedTemplate()
    {
        $response = $this->post(route('admin.prompt-templates.store'), $this->requestData([
            'name' => 'impacts',
            'is_selected' => true,
        ]));
        $response->assertSuccessful();

        $response = $this->post(route('admin.prompt-templates.store'), $this->requestData([
            'name' => 'impacts',
            'is_selected' => true,
        ]));
        $response->assertSuccessful();

        $response = $this->get(route('admin.prompt-templates.index'));
        $response->assertSuccessful();

        $this->assertDatabaseHas('prompt_templates', [
            'name' => $this->requestData()['name'],
            'is_selected' => false,
        ]);
    }

    /** @test */
    public function itShouldUpdateSelectedTemplate()
    {
        $prompt_template_1 = PromptTemplate::factory()->createOne($this->requestData([
            'name' => 'impacts',
            'is_selected' => true,
        ]));

        $prompt_template_2 = PromptTemplate::factory()->createOne($this->requestData([
            'name' => 'impacts',
            'is_selected' => false,
        ]));

        $response = $this->put(route('admin.prompt-templates.update', $prompt_template_2->id), $this->requestData([
            'name' => 'impacts',
            'is_selected' => true,
        ]));

        $response->assertSuccessful();

        $this->assertDatabaseHas('prompt_templates', [
            'id' => $prompt_template_1->id,
            'is_selected' => false,
        ]);
    }

    /** @test */
    public function itShouldDeletePromptTemplate()
    {
        $prompt_template = PromptTemplate::factory()->createOne($this->requestData([
            'is_selected' => false,
        ]));

        $response = $this->delete(route('admin.prompt-templates.destroy', $prompt_template->id));
        $response->assertSuccessful();

        $this->assertDatabaseMissing('prompt_templates', [
            'id' => $prompt_template->id,
        ]);
    }

    /** @test */
    public function itShouldNotDeleteSelectedPromptTemplate()
    {
        $prompt_template = PromptTemplate::factory()->createOne($this->requestData([
            'name' => 'impact',
            'is_selected' => true,
        ]));

        $response = $this->delete(route('admin.prompt-templates.destroy', $prompt_template->id));
        $response->assertStatus(422);
    }

    private function requestData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'impacts',
            'text' => "You are an HR Expert, and an interviewee gave you the report they got from, you are explaining to the interviewee ther impacts of his scores based on his job profile and the scores from interviewee's report.
            Generate 3 or 4 impacts in bullet points in html format based on the scores in a professional manner.
            The interviewee is applying for _JOB_TITLE_, take that into consideration while generating the impacts based on the scores from interviewee's report.
            from interviewee's report scores",
            'stats_text' => 'you got _QUESTION_CLUSTER_AVG_SCORE_% at _QUESTION_CLUSTER_NAME_ \n',
            'conclusion_text' => 'impacts in bullets point in html format:',
            'is_selected' => true,
        ], $overrides);
    }
}
