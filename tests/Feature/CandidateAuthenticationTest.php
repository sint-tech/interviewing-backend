<?php

namespace Tests\Feature;

use Domain\Candidate\Actions\GenerateCandidateAccessTokenAction;
use Domain\Candidate\Models\Candidate;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CandidateAuthenticationTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        Artisan::call("passport:install");

        Artisan::call("passport:client",[
            "--password"    => 1,
            "--name"        =>  "Laravel Password Grant Client FOR CANDIDATE",
            "--provider"    => "candidates"
        ]);
    }

    /**
     * A basic feature test example.
     * @test
     */
    public function candidate_should_be_auth_after_register(): void
    {
        $response = $this
            ->post('/api/register', [
                "first_name"    => "Ahmed",
                "last_name"     => "Badawy",
                "email"         => "foo@gmail.com",
                "password"      => "u2ArHI&mxLwuh2%k",
                "mobile"        => [
                    "country"   => "EG",
                    "number"    => "1114470249"
                ]
            ]);

        $response->assertSuccessful();

        $this->assertModelExists(Candidate::query()->where("email","foo@gmail.com")->first());

        $response->assertJsonStructure([
            "data"  => [
                "first_name",
                "last_name",
                "full_name",
                "email",
                "mobile" => [
                    "country",
                    "number"
                ]
            ],
            "meta"  => [
                "token"
            ]
        ]);
    }

    /**
     * @return void
     * @test
     */
    public function registered_candidate_can_login()
    {
        Candidate::factory()->create([
            "email"         => "foo@gmail.com",
            "password"      => Hash::make("u2ArHI&mxLwuh2%k"),
        ]);

        $response = $this
            ->post('/api/login', [
                "email"         => "foo@gmail.com",
                "password"      => "u2ArHI&mxLwuh2%k",
            ]);

        $response->assertSuccessful();

        $meta_array = (array) json_decode($response->content());

        $this->assertArrayHasKey("token",$meta_array);
    }

    /**
     * @return void
     * @test
     */
    public function candidate_should_logout()
    {
        $candidate = Candidate::factory()->create([
            "email"         => "foo@gmail.com",
            "password"      => "u2ArHI&mxLwuh2%k",
        ]);

        $token = (new GenerateCandidateAccessTokenAction($candidate))->execute();

        $response = $this->actingAs($candidate)
            ->withToken($token)
            ->post('/api/logout');

        $response->assertSuccessful();
    }
}
