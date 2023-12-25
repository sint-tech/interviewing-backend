<?php

namespace Tests\Feature\App\Admin\Candidate;

use Domain\Candidate\Models\Candidate;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class CandidateControllerTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation,WithFaker;

    protected User $sintUser;

    public function setup(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();

        Artisan::call('db:seed', [
            '--class' => 'SintAdminsSeeder',
        ]);

        $this->sintUser = User::query()->first();

        $this->actingAs($this->sintUser, 'api');
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
            'mobile_dial_code' => $candidate->mobile_number->dialCode,
            'mobile_number' => (int) $candidate->mobile_number->number,
        ]);
    }
}
