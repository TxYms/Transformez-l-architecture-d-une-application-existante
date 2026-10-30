<?php

namespace App\Exceptions;

use RuntimeException;

class LoginThrottledException extends RuntimeException
{
    public function __construct(public readonly int $retryAfter)
    {
        parent::__construct(__('auth.throttle', [
            'seconds' => $retryAfter,
            'minutes' => (int) ceil($retryAfter / 60),
        ]));
    }
}
