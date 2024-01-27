<?php

namespace Tests\Unit\Support\Actions;

use App\Exceptions\ModelHasRelationsPreventDeleteException;
use Domain\QuestionManagement\Models\QuestionCluster;
use Domain\Skill\Models\Skill;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Support\Actions\PreventDeleteModelWhenHasRelationsAction;
use Tests\TestCase;

class PreventDeleteModelWhenHasRelationsActionTest extends TestCase
{
    use RefreshDatabase,WithFaker;

    protected User $creator;

    public function setup(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->creator = User::factory()->createOne();
    }

    /** @test  */
    public function itShouldReturnBooleanWhenPreventThrowException()
    {
        $skill = Skill::factory()->createOne();

        $result = (new PreventDeleteModelWhenHasRelationsAction())->preventThrowException()->execute(Skill::query()->first(), ['questionClusters']);

        $this->assertFalse($result);

        QuestionCluster::factory()->hasAttached($skill)->for($this->creator, 'creator')->count(10)->create();

        $result = (new PreventDeleteModelWhenHasRelationsAction())->preventThrowException()->execute(Skill::query()->first(), ['questionClusters']);

        $this->assertTrue($result);
    }

    /** @test  */
    public function itShouldThrowExceptionWhenRelationFounded()
    {
        $this->expectException(ModelHasRelationsPreventDeleteException::class);

        $skill = Skill::factory()->createOne();

        QuestionCluster::factory()->hasAttached($skill)->for($this->creator, 'creator')->count(10)->create();

        (new PreventDeleteModelWhenHasRelationsAction())->execute(Skill::query()->first(), ['questionClusters']);
    }

    /** @test  */
    public function itShouldPreventDeleteUsingTheModelTrait()
    {
        $this->expectException(ModelHasRelationsPreventDeleteException::class);

        $skill = Skill::factory()->createOne();

        QuestionCluster::factory()->hasAttached($skill)->for($this->creator, 'creator')->count(10)->create();

        $skill->delete();
    }
}
