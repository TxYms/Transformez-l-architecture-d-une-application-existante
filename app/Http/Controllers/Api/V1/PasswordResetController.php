<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends Controller
{
    public function __construct(private readonly AuthService $auth) {}

    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        $this->auth->sendResetLink($request->validated('email'));

        return ApiResponse::success(null, 'If the account exists, a reset link has been sent.');
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $this->auth->resetPassword($request->validated());

        return ApiResponse::success(null, 'Password has been reset.');
    }
}
