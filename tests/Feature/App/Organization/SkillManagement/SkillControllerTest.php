<?php

namespace Tests\Feature\App\Organization\SkillManagement;

use Tests\TestCase;
use Domain\Skill\Models\Skill;
use Database\Seeders\SintAdminsSeeder;
use Domain\Organization\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;


class SkillControllerTest extends TestCase
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
    public function itShouldShowSkills()
    {
        Skill::factory(100)->create();

        $response = $this->get(route('organization.skills.index'));

        $response->assertSuccessful();
        $response->assertJsonCount(25, 'data');

        $response = $this->get(route('organization.skills.index', ['per_page' => 1000]));

        $response->assertSuccessful();
        $response->assertJsonCount(100, 'data');
    }

    /** @test  */
    public function itShouldShowSingleSkill()
    {
        $this->get(route('organization.skills.show', 1))->assertNotFound();

        $skill = Skill::factory()->createOne();

        $response = $this->get(route('organization.skills.show', $skill));

        $response->assertSuccessful();
        $response->assertJsonFragment([
            'id' => $skill->getKey(),
            'name' => $skill->name,
            'description' => $skill->description,
        ]);
    }
}
