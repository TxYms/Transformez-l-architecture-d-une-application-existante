<?php

namespace App\Services;

use App\Models\User;

final class AuthResult
{
    public function __construct(
        public readonly User $user,
        public readonly string $token,
    ) {}
}
