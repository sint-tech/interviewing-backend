<?php

namespace Tests\Feature;

use Domain\Candidate\Enums\CandidateSocialAppEnum;
use Domain\Candidate\Models\Candidate;
use Domain\Candidate\Models\RegistrationReason;
use Domain\JobTitle\Models\JobTitle;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class CandidateAuthenticationTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();
    }

    /**
     * A basic feature test example.
     *
     * @test
     */
    public function candidateCanRegisterToTheSystem(): void
    {
        Storage::fake();

        $cv = UploadedFile::fake()->create('my-cv.pdf');

        $job_titles = JobTitle::factory(20)->availabilityIsActive()->create();

        $registration_reasons = RegistrationReason::factory(10)->availabilityIsActive()->create();

        $current_job_title = $job_titles->first();

        $response = $this
            ->post('/api/register', [
                'first_name' => 'Ahmed',
                'last_name' => 'Badawy',
                'email' => 'foo@gmail.com',
                'password' => 'u2ArHI&mxLwuh2%k',
                'mobile' => [
                    'dial_code' => '+20',
                    'number' => '1114470249',
                ],
                'current_job_title_id' => $current_job_title->getKey(),
                'desire_hiring_positions' => $job_titles
                    ->where('id', '!=', $current_job_title->getKey())
                    ->unique()
                    ->pluck('id')->toArray(),
                'registration_reasons' => $registration_reasons->pluck('id')->toArray(),
                'cv' => $cv,
            ]);

        $response->assertSuccessful();

        $this->assertModelExists(Candidate::query()->where('email', 'foo@gmail.com')->first());

        $response->assertJsonStructure([
            'data' => [
                'first_name',
                'last_name',
                'email',
                'mobile' => [
                    'country',
                    'number',
                ],
            ],
            'token',
        ]);

        $candidate_cvs = Candidate::query()
            ->where('email', 'foo@gmail.com')
            ->first()->getMedia('cv');

        $this->assertCount(1, $candidate_cvs);

        $this->assertTrue($candidate_cvs->first()->getCustomProperty('used_when_registered'));

        $this->assertFileExists($candidate_cvs->first()->getPath());
    }

    /**
     * @return void
     *
     * @test
     */
    public function candidateCanLogin()
    {
        Candidate::factory()->create([
            'email' => 'foo@gmail.com',
            'password' => Hash::make('u2ArHI&mxLwuh2%k'),
        ]);

        $response = $this
            ->post('/api/login', [
                'email' => 'foo@gmail.com',
                'password' => 'u2ArHI&mxLwuh2%k',
            ]);

        $response->assertSuccessful();

        $meta_array = (array) json_decode($response->content());

        $this->assertArrayHasKey('token', $meta_array);
    }

    /**
     * @return void
     *
     * @test
     */
    public function candidateCanLoginUsingSocialLoginGoogle()
    {
        Candidate::factory()->registeredWithSocialApp()->create([
            'social_driver_id' => $driver_id = Str::uuid()->toString(),
            'social_driver_name' => $driver_name = CandidateSocialAppEnum::Google->value,
        ]);

        $response = $this->post('api/social-login', [
            'driver_name' => $driver_name,
            'driver_id' => $driver_id,
        ]);

        $response->assertSuccessful();

        $meta_array = (array) json_decode($response->content());

        $this->assertArrayHasKey('token', $meta_array);
    }

    /**
     * @return void
     *
     * @test
     */
    public function candidateShouldLogout()
    {
        $candidate = Candidate::factory()->create([
            'email' => 'foo@gmail.com',
            'password' => 'u2ArHI&mxLwuh2%k',
        ]);

        $response = $this->actingAs($candidate)
            ->post('/api/logout');

        $response->assertSuccessful();
    }
}
