<?php

namespace Tests\Feature\Console\Commands;

use Tests\TestCase;
use App\Mail\InterviewInvitation;
use Illuminate\Support\Facades\Mail;
use Domain\Invitation\Models\Invitation;
use Domain\Organization\Models\Employee;
use App\Console\Commands\SendInvitationsCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;


class SendInvitationsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->migrateFreshUsing();
    }

    /** @test */
    public function itShouldSendInvitesAtScheduledTime()
    {
        $invitation = Invitation::factory()->create([
            'expired_at' => null,
            'last_invited_at' => null,
            'should_be_invited_at' => now(),
            'creator_type' => 'employee',
            'creator_id' => Employee::factory()->createOne()->id,
        ]);

        $this->artisan(SendInvitationsCommand::class)
            ->expectsOutput("invitation with id: $invitation->id sent")
            ->expectsOutput('total sent invitation is 1')
            ->assertExitCode(0);

        Mail::assertSent(InterviewInvitation::class,function (InterviewInvitation $mail) use ($invitation) {
            return $mail->invitation->id === $invitation->id;
        });

        Mail::assertSentCount(1);
    }

    /** @test */
    public function itShouldNotSendInvitesBeforeScheduledTime()
    {
        Invitation::factory()->create([
            'expired_at' => null,
            'last_invited_at' => null,
            'should_be_invited_at' => now()->addMinutes(5),
            'creator_type' => 'employee',
            'creator_id' => Employee::factory()->createOne()->id,
        ]);

        $this->artisan(SendInvitationsCommand::class)
            ->expectsOutput('total sent invitation is 0')
            ->assertExitCode(0);
    }
}
