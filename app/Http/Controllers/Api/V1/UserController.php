<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\DeleteUserRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly UserService $users) {}

    public function show(Request $request): JsonResponse
    {
        return ApiResponse::success(UserResource::make($request->user()), 'User retrieved.');
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->users->updateProfile($request->user(), $request->validated());

        return ApiResponse::success(UserResource::make($user), 'Profile updated.');
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $this->users->updatePassword($request->user(), $request->validated('password'));

        return ApiResponse::success(null, 'Password updated.');
    }

    public function destroy(DeleteUserRequest $request): JsonResponse
    {
        $this->users->delete($request->user());

        return ApiResponse::success(null, 'Account deleted.');
    }
}
