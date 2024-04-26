<?php

namespace Tests\Feature\App\Candidate\Auth;

use Database\Seeders\SintAdminsSeeder;
use Domain\Candidate\Models\Candidate;
use Domain\Candidate\Models\RegistrationReason;
use Domain\Invitation\Models\Invitation;
use Domain\JobTitle\Models\JobTitle;
use Domain\Users\Models\User;

use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->seed(SintAdminsSeeder::class);
    }

    /** @test  */
    public function itShouldRegisterNewCandidateFromInvitation()
    {
        $invitation = Invitation::factory()->for(User::first(), 'creator')->createOne([
            'email' => 'ahmed.badawy@gmail.com',
        ]);

        $response = $this->post(route('candidate.auth.register', $invitation), [
            'first_name' => 'Ahmed',
            'last_name' => 'Badawy',
            'email' => 'ahmed.badawy@gmail.com',
            'password' => '1234567@gm',
        ]);

        $response->assertSuccessful();

        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->where('data.email', 'ahmed.badawy@gmail.com')
                ->missingAll(['mobile', 'current_job_title', 'password'])
                ->has('token')
                ->etc()
        );

        $this->assertInstanceOf(Candidate::class, Candidate::query()->firstWhere('email', 'ahmed.badawy@gmail.com'));

        //todo assert invitation email belongs to the candidate email
    }

    /** @test */
    public function itShouldLoginAfterRegisterFromInvitation()
    {
        $invitation = Invitation::factory()->for(User::first(), 'creator')->createOne([
            'email' => 'ahmed.badawy@gmail.com',
        ]);

        $response = $this->post(route('candidate.auth.register', $invitation), [
            'first_name' => 'Ahmed',
            'last_name' => 'Badawy',
            'email' => 'ahmed.badawy@gmail.com',
            'password' => '1234567@gm',
        ]);

        $response->assertSuccessful();

        $this->assertInstanceOf(Candidate::class, Candidate::query()->firstWhere('email', 'ahmed.badawy@gmail.com'));

        $this->post(route('candidate.auth.login'), [
            'email' => 'ahmed.badawy@gmail.com',
            'password' => '1234567@gm',
        ])->assertSuccessful();
    }

    /** @test  */
    public function itShouldRegisterWithoutInvitation()
    {
        JobTitle::factory(3)->state(['availability_status' => 'active'])->create();

        RegistrationReason::factory(3)->state(['availability_status' => 'active'])->create();

        $response = $this->post(route('candidate.auth.register'), [
            'first_name' => 'Ahmed',
            'last_name' => 'Badawy',
            'email' => 'ahmed.badawy@gmail.com',
            'password' => '123456@BB',
            'mobile' => [
                'dial_code' => '+20',
                'number' => '1114470241',
            ],
            'current_job_title_id' => 3,
            'registration_reasons' => [1, 2, 3],
            'desire_hiring_positions' => [1, 2],
            'cv' => UploadedFile::fake()->create('cv.pdf'),
        ]);

        $response->assertSuccessful();

        $response->assertJson(function (AssertableJson $json) {
            $json->has('token')
                ->first(function (AssertableJson $data) {
                    $data->where('email', 'ahmed.badawy@gmail.com')
                        ->missing('password')
                        ->hasAll(['first_name', 'last_name', 'mobile', 'current_job_title'])
                        ->etc();
                })
                ->etc();
        });

        $this->assertInstanceOf(Candidate::class, Candidate::query()->firstWhere('email', 'ahmed.badawy@gmail.com'));

        $this->assertCount(1, Candidate::query()->get());
    }
}
