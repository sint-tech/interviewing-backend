<?php

namespace Tests\Feature;

use Domain\Skill\Models\Skill;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SkillTest extends TestCase
{
    use DatabaseMigrations;

    public User $superAdmin;

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

    }

    public function testItShouldCreateSkill(): void
    {
        $response = $this->actingAs($this->superAdmin, 'api')
            ->post('admin-api/skills', [
                'name' => 'confidence',
            ]);

        $response->assertSuccessful();

        $this->assertEquals('confidence', Skill::query()->latest()->first());
    }

    public function testItShouldUpdateSkill(): void
    {
        $skill = Skill::factory()->create();

        $response = $this->actingAs($this->superAdmin, 'api')
            ->post("admin-api/skills/{$skill->getKey()}?_method=PUT", [
                'name' => 'updated skill name',
            ]);

        $this->assertEquals('updated skill name', Skill::query()->find($skill->getKey())->name);

        $response->assertSuccessful();
    }
}
