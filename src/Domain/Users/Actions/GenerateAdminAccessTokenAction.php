<?php

namespace Domain\Users\Actions;

use Domain\Users\Models\User;

class GenerateAdminAccessTokenAction
{
    public const CLIENT_NAME = 'adminToken';

    public function __construct(
        protected User $admin
    ) {

    }

    public function execute(): string
    {
        return $this->admin->createToken(self::CLIENT_NAME)->plainTextToken;
    }
}
