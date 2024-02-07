<?php

namespace Tests\Feature\App\Admin\Candidate;

use Database\Seeders\SintAdminsSeeder;
use Domain\Candidate\Models\Candidate;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CandidateControllerTest extends TestCase
{
    use DatabaseMigrations,WithFaker;

    protected User $sintUser;

    public function setup(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->seed(SintAdminsSeeder::class);

        $this->sintUser = User::query()->first();

        $this->actingAs($this->sintUser, 'admin');
    }

    /** @test  */
    public function itShouldShowAllCandidates()
    {
        $response = $this->get(route('admin.candidates.index'));
        $response->assertSuccessful();

        Candidate::factory(40)->create();

        $response = $this->get(route('admin.candidates.index'));

        $response->assertSuccessful();
    }

    /** @test */
    public function itShouldShowSingleCandidate()
    {
        Candidate::factory()->createOne();

        $response = $this->get(route('admin.candidates.show', $candidate = Candidate::query()->first()));

        $response->assertSuccessful();

        $response->assertJsonFragment([
            'id' => $candidate->getKey(),
            'email' => $candidate->email,
            'mobile_dial_code' => $candidate->mobile_number->dialCode,
            'mobile_number' => (int) $candidate->mobile_number->number,
        ]);
    }
}
