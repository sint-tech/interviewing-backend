<?php

namespace Tests\Feature;

use Domain\QuestionManagement\Models\QuestionCluster;
use Domain\Skill\Models\Skill;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class QuestionClusterTest extends TestCase
{
    use DatabaseMigrations;

    protected User $superAdmin;

    protected Collection $skills;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        Artisan::call('passport:install');

        Artisan::call('passport:client', [
            '--password' => 1,
            '--name' => 'Laravel Password Grant Client FOR CANDIDATE',
            '--provider' => 'candidates',
        ]);

        Artisan::call('db:seed', [
            '--class' => 'SintAdminsSeeder',
        ]);

        $this->superAdmin = User::query()->first();

        $this->skills = Skill::factory(10)->create();
    }

    public function testItShouldCreateQuestionCluster(): void
    {
        $response = $this->actingAs($this->superAdmin, 'api')
            ->post('/admin-api/question-clusters', [
                'name' => 'lorem',
                'description' => 'lorem is optional',
                'skills' => $this->skills->pluck('id')->toArray(),
            ]);

        //asserting creator is the auth user
        $this->assertEquals($this->superAdmin->getKey(), QuestionCluster::query()->latest()->first()->creator->getKey());

        $response->assertSuccessful();
    }

    public function testItShouldUpdateQuestionCluster(): void
    {
        $questionCluster = QuestionCluster::factory()->for($this->superAdmin, 'creator')->create();

        $body = [
            'name' => 'question cluster 1',
            'description' => null,
            'skills' => $this->skills->take(5)->pluck('id')->toArray(),
        ];

        $response = $this->actingAs($this->superAdmin, 'api')
            ->post("admin-api/question-clusters/{$questionCluster->getKey()}?_method=PUT", $body);

        $questionCluster = $questionCluster->refresh();

        $this->assertEquals($body['name'], $questionCluster->name);

        $this->assertCount(5, $questionCluster->skills);

        $response->assertSuccessful();
    }
}
