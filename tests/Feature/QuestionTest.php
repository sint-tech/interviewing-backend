<?php

namespace Tests\Feature;

use Domain\QuestionManagement\Models\Question;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation;

    public User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();

        $this->seedSuperAdminAccounts();

        $this->superAdmin = User::query()->first();
    }

    public function testItShouldUpdateQuestion(): void
    {
        $question = Question::factory()->for($this->superAdmin, 'creator')->create();

        $response = $this->actingAs($this->superAdmin, 'api')
            ->post("admin-api/questions/{$question->getKey()}?_method=PUT", [
                'title' => 'update question',
            ]);

        $question = $question->refresh();

        $this->assertEquals($this->superAdmin, $question->creator);

        $this->assertEquals('update question', $question->title);

        $response->assertOk();
    }
}
