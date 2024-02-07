<?php

namespace Tests\Feature;

use Database\Seeders\SintAdminsSeeder;
use Domain\Users\Actions\GenerateAdminAccessTokenAction;
use Domain\Users\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrateFreshUsing();

        $this->seed(SintAdminsSeeder::class);

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
