<?php

namespace Tests\Feature;

use Domain\QuestionManagement\Models\QuestionCluster;
use Domain\Skill\Models\Skill;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class QuestionClusterTest extends TestCase
{
    use DatabaseMigrations;

    protected User $superAdmin;

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

        Artisan::call("db:seed",[
            '--class'   => 'SintAdminsSeeder'
        ]);

        $this->superAdmin = User::query()->first();
    }

    public function testItShouldGetQuestionClustersAsPaginated(): void
    {

        return ;
    }

    public function testItShouldCreateQuestionCluster(): void
    {
        $skills = Skill::factory(10)->create();

        $response = $this->actingAs($this->superAdmin,'api')
            ->post('/admin-api/question-clusters',[
                'name'  => 'lorem',
                'description'   => 'lorem is optional',
                'skills'        => $skills->pluck('id')->toArray(),
            ]);

        //asserting creator is the auth user
        $this->assertEquals($this->superAdmin->getKey(),QuestionCluster::query()->latest()->first()->creator->getKey());

        $response->assertSuccessful();
    }
}
