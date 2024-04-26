<?php

namespace Tests\Feature\App\Admin\InvitationManagement;

use Tests\TestCase;
use Domain\Users\Models\User;
use App\Mail\InterviewInvitation;
use Illuminate\Support\Facades\Mail;
use Database\Seeders\SintAdminsSeeder;
use Domain\Invitation\Models\Invitation;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;


class SendInvitationControllerTest extends TestCase
{
    use RefreshDatabase,WithFaker;

    public User $sintUser;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->migrateFreshUsing();

        $this->seed(SintAdminsSeeder::class);

        $this->sintUser = User::query()->first();

        $this->actingAs($this->sintUser, 'admin');
    }

    /** @test  */
    public function itShouldSentInterviewInvitationMail()
    {
        $invitation = Invitation::factory()->for($this->sintUser, 'creator')->createOne([
            'last_invited_at' => null,
        ]);

        $this->post(route('admin.invitations.send-email', $invitation))
            ->assertSuccessful()
            ->assertJson(function (AssertableJson $json) {
                return $json->first(fn (AssertableJson $data) => $data->where('is_sent', true)->etc());
            });

        Mail::assertSent(InterviewInvitation::class);
    }
}
