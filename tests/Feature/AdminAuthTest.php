<?php

namespace Tests\Feature;

use Domain\Users\Actions\GenerateAdminAccessTokenAction;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        Artisan::call('passport:install');

        Artisan::call('db:seed', [
            '--class' => 'SintAdminsSeeder',
        ]);

    }

    public function testAdminShouldLogin(): void
    {
        $response = $this->post('/admin-api/login', [
            'email' => 'ahmed.badawy@sint.com',
            'password' => 'password',
        ]);

        $response->assertSuccessful();
    }

    public function testAdminShouldLogout(): void
    {
        $auth_admin = User::query()->first();
        $response = $this
            ->withToken((new GenerateAdminAccessTokenAction($auth_admin))->execute())
            ->get('/admin-api/logout');

        $response->assertOk();
    }
}
