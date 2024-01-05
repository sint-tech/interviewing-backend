<?php

namespace Tests\Unit\Mail;

use App\Mail\InterviewInvitation;
use Domain\Invitation\Models\Invitation;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InterviewInvitationTest extends TestCase
{
    use RefreshDatabase;

    protected Invitation $invitation;

    protected InterviewInvitation $mail;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->invitation = Invitation::factory()->createOne([
            'creator_type' => User::class,
            'creator_id' => User::factory()->createOne()->getKey(),
        ]);

        $this->mail = new InterviewInvitation($this->invitation);
    }

    /** @test  */
    public function itShouldBeQueued()
    {
        Mail::send($this->mail);

        Mail::assertSent($this->mail::class);
    }

    /** @test  */
    public function mailBodyShouldContainInvitationData()
    {
        $this->mail->assertSeeInOrderInHtml([
            $this->invitation->name,
            $this->invitation->url,
        ]);
    }

    /** @test  */
    public function subjectShouldContainVacancyTitle()
    {
        $this->mail->assertHasSubject("{$this->invitation->vacancy->title} Interview");
    }

    /** @test  */
    public function mailShouldBeSentToInvitationEmail()
    {
        $this->mail->assertTo($this->invitation->email);
    }
}
