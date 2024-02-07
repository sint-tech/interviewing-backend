<?php

namespace Tests\Feature\App\Candidate\Invitation;

use Database\Seeders\SintAdminsSeeder;
use Domain\Candidate\Models\Candidate;
use Domain\Invitation\Models\Invitation;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MyInvitationsControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected Candidate $authCandidate;

    public function setup(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->seed(SintAdminsSeeder::class);

        $this->authCandidate = Candidate::factory()->createOne();

        $this->actingAs($this->authCandidate, 'candidate');
    }

    /** @test  */
    public function itShouldShowAllAuthCandidateInvitations()
    {
        Invitation::factory(25)->for(User::query()->first(), 'creator')
            ->create([
                'candidate_id' => $this->authCandidate,
                'email' => $this->authCandidate->email,
            ]);

        Invitation::factory(100)->for(User::query()->first(), 'creator')->create();
        Invitation::factory(100)->for(User::query()->first(), 'creator')->create([
            'candidate_id' => Candidate::factory()->createOne()->getKey(),
        ]);

        $response = $this->get(route('candidate.invitations.my-invitations'));

        $response->assertSuccessful();
        $response->assertJsonCount(25, 'data');

        $response = $this->get(route('candidate.invitations.my-invitations', ['per_page' => 100]));

        $response->assertSuccessful();
        $response->assertJsonCount(25, 'data');
    }
}
