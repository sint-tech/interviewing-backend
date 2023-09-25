<?php

namespace Tests\Feature\Traits;

use Illuminate\Support\Facades\Artisan;

trait AuthenticationInstallation
{
    protected function installPassport()
    {
        Artisan::call('passport:install');

        Artisan::call('passport:client', [
            '--password' => 1,
            '--name' => 'Laravel Password Grant Client FOR CANDIDATE',
            '--provider' => 'candidates',
        ]);

        Artisan::call('passport:client', [
           '--password' => 1,
           '--name' => 'Laravel Password Grant Client FOR ORGANIZATION EMPLOYEE',
            '--provider' => 'organization'
        ]);

    }

    protected function seedSuperAdminAccounts()
    {
        Artisan::call('db:seed', [
            '--class' => 'SintAdminsSeeder',
        ]);
    }
}
