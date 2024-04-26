<?php

namespace Tests\Feature\App\Organization\QuestionManagement;

use Tests\TestCase;
use Domain\Users\Models\User;
use Database\Seeders\SintAdminsSeeder;
use Domain\Organization\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Domain\QuestionManagement\Models\QuestionCluster;


class QuestionClusterControllerTest extends TestCase
{
    use RefreshDatabase;

    public Employee $employeeAuth;

    public function setUp(): void
    {
        parent::setUp();

        $this->employeeAuth = Employee::factory()->createOne();

        $this->seed(SintAdminsSeeder::class);

        $this->actingAs($this->employeeAuth, 'organization');
    }

    /** @test  */
    public function itShouldShowQuestionClusters()
    {
        QuestionCluster::factory(100)->for(User::query()->first(), 'creator')->create();

        $response = $this->get(route('organization.question-clusters.index'));
        $response->assertSuccessful();
        $response->assertJsonCount(25, 'data');

        $response = $this->get(route('organization.question-clusters.index', ['per_page' => 1000]));
        $response->assertSuccessful();
        $response->assertJsonCount(100, 'data');
    }

    /** @test  */
    public function itShouldShowSingleQuestionCluster()
    {
        $question_cluster = QuestionCluster::factory(1)->for(User::query()->first(), 'creator')->createOne();

        $response = $this->get(route('organization.question-clusters.show', $question_cluster));
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'id' => $question_cluster->getKey(),
            'name' => $question_cluster->name,
            'description' => $question_cluster->description,
        ]);
    }
}
