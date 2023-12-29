<?php

namespace Tests\Feature\App\Admin\InvitationManagement;

use Domain\Users\Models\User;
use Domain\Vacancy\Models\Vacancy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Traits\AuthenticationInstallation;
use Tests\TestCase;

class InvitationControllerTest extends TestCase
{
    use DatabaseMigrations,AuthenticationInstallation,WithFaker;

    public User $sintUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->installPassport();

        Artisan::call('db:seed', [
            '--class' => 'SintAdminsSeeder',
        ]);

        $this->sintUser = User::query()->first();
    }

    /** @test  */
    public function itShouldStoreInvitation(): void
    {
        $request_data = [
            'name' => 'ahmed badawy',
            'email' => 'ahmedbadawy.fcai@gmail.com',
            'mobile_country_code' => '+20',
            'vacancy_id' => Vacancy::factory()->createOne()->getKey(),
            'mobile_number' => '1234567890',
            'should_be_invited_at' => now()->addDays(2)->format('Y-m-d H:i'),
            'expired_at' => now()->addDays(5)->format('Y-m-d H:i'),
        ];

        $this->actingAs($this->sintUser, 'api')
            ->post(route('admin.invitations.store'), $request_data)
            ->assertSuccessful()
            ->assertJson(function (AssertableJson $json) {
                return $json->first(function (AssertableJson $data) {
                    return $data->hasAll([
                        'id', 'name', 'email',
                        'mobile_number', 'mobile_country_code',
                        'batch',
                    ])->etc();
                });
            });
    }
}
