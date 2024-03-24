<?php

namespace Tests\Feature\App\Admin\InterviewManagement;

use Tests\TestCase;
use Domain\Organization\Models\Employee;
use Domain\InterviewManagement\Models\Interview;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Domain\InterviewManagement\Enums\InterviewStatusEnum;
use Domain\Users\Models\User;
use Domain\Vacancy\Models\Vacancy;

class ChangeInterviewStatusControllerTest extends TestCase
{
    use DatabaseMigrations;

    public Employee $employeeAuth;

    public User $sintAuth;

    public Vacancy $vacancy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->employeeAuth = Employee::factory()->createOne();

        $this->vacancy = Vacancy::factory()
            ->for($this->employeeAuth->organization, 'organization')
            ->for($this->employeeAuth, 'creator')
            ->createOne();

        $this->sintAuth = User::factory()->createOne();

        $this->actingAs($this->sintAuth, 'admin');
    }
    /** @test  */
    public function itShouldChangeInterviewStatus()
    {
        $interview = Interview::factory()
            ->for($this->vacancy, 'vacancy')
            ->createOne([
                'status' => InterviewStatusEnum::Passed->value,
            ]);

        $response = $this->post(route('admin.interviews.change-status', $interview->getKey()), [
            'status' => InterviewStatusEnum::Canceled->value,
        ]);

        $response->assertSuccessful();
        $response->assertJsonFragment(['status' =>  InterviewStatusEnum::Canceled->value]);
    }

    /** @test  */
    public function itShouldValidateStatus()
    {
        $interview = Interview::factory()
            ->for($this->vacancy, 'vacancy')
            ->createOne([
                'status' => InterviewStatusEnum::Passed->value,
            ]);

        $response = $this->post(route('admin.interviews.change-status', $interview->getKey()), [
            'status' => 'invalid-status',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('status');
    }
}
