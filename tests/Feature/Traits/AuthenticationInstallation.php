<?php

namespace Tests\Feature\Traits;

trait AuthenticationInstallation
{
    protected function seedSuperAdminAccounts()
    {
        $this->seed(SintAdminsSeeder::class);
    }
}
