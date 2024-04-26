<?php

namespace Tests\Feature\App\Organization\Auth;

use Tests\TestCase;
use Domain\Organization\Models\Employee;
use Illuminate\Support\Facades\Notification;

use App\Organization\Auth\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public Employee $employeeAuth;
    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->employeeAuth = Employee::factory()->createOne();

        Notification::fake();
    }

    /** @test */
    public function itShouldSendForgotPasswordLink()
    {
        $this->postJson(route('organization.auth.password.forgot'), ['email' => $this->employeeAuth->email]);

        Notification::assertSentTo($this->employeeAuth, ResetPasswordNotification::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $url = urldecode(url(config('app.organization_website_url') . '/reset-password?token=' . $notification->token . '&email=' . $this->employeeAuth->email));
            $this->assertStringContainsString($url, $notification->toMail($this->employeeAuth)->url);

            return true;
        });

        $this->assertDatabaseHas('password_reset_tokens', ['email' => $this->employeeAuth->email]);
    }

    /** @test */
    public function itShouldResetWithValidToken()
    {
        $this->postJson(route('organization.auth.password.forgot'), ['email' => $this->employeeAuth->email]);

        $notification = Notification::sent($this->employeeAuth, ResetPasswordNotification::class)->first();

        $this->postJson(route('organization.auth.password.reset'), [
            'email' => $this->employeeAuth->email,
            'token' => $notification->token,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSuccessful();

        $this->assertTrue(Hash::check('password123', $this->employeeAuth->fresh()->password));
    }

    /** @test */
    public function itShouldFailsWithInvalidToken()
    {
        $this->postJson(route('organization.auth.password.reset'), [
            'email' => $this->employeeAuth->email,
            'token' => 'invalid-token',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(400);

        $this->assertFalse(Hash::check('password123', $this->employeeAuth->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $this->employeeAuth->email]);
    }

    /** @test */
    public function itShouldFailsWithInvalidEmail()
    {
        $this->postJson(route('organization.auth.password.forgot'), [
            'email' => 'anyemail@gmail.com',
        ])->assertStatus(400);
    }

    /** @test */
    public function itShouldFailWhenTokenExpiresAfterOneHour()
    {
        $this->postJson(route('organization.auth.password.forgot'), ['email' => $this->employeeAuth->email]);

        $notification = Notification::sent($this->employeeAuth, ResetPasswordNotification::class)->first();

        $this->travel(61)->minutes();

        $this->postJson(route('organization.auth.password.reset'), [
            'email' => $this->employeeAuth->email,
            'token' => $notification->token,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(400);

        $this->assertFalse(Hash::check('password123', $this->employeeAuth->fresh()->password));
    }

    /** @test */
    public function itShouldResetPasswordBeforeOneHour()
    {
        $this->postJson(route('organization.auth.password.forgot'), ['email' => $this->employeeAuth->email]);

        $notification = Notification::sent($this->employeeAuth, ResetPasswordNotification::class)->first();

        $this->travel(59)->minutes();

        $this->postJson(route('organization.auth.password.reset'), [
            'email' => $this->employeeAuth->email,
            'token' => $notification->token,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSuccessful();

        $this->assertTrue(Hash::check('password123', $this->employeeAuth->fresh()->password));
    }
}
