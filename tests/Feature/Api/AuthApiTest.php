<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('a user can register and receives a token', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Alex Martin',
        'email' => 'alex@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertCreated()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.token_type', 'Bearer')
        ->assertJsonPath('data.user.email', 'alex@example.com')
        ->assertJsonMissingPath('data.user.password');

    expect(User::where('email', 'alex@example.com')->exists())->toBeTrue();
});

test('registration is validated', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->postJson('/api/v1/auth/register', [
        'name' => '',
        'email' => 'taken@example.com',
        'password' => 'password',
        'password_confirmation' => 'other',
    ])->assertStatus(422)
        ->assertJsonPath('status', 'error')
        ->assertJsonValidationErrors(['name', 'email', 'password'], 'data.errors');
});

test('a user can log in with valid credentials', function () {
    $user = User::factory()->create();

    $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'password'])
        ->assertOk()
        ->assertJsonStructure(['status', 'message', 'data' => ['token', 'token_type', 'user' => ['id', 'name', 'email']]]);
});

test('invalid credentials return 401', function () {
    $user = User::factory()->create();

    $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'wrong-password'])
        ->assertUnauthorized()
        ->assertExactJson(['status' => 'error', 'message' => __('auth.failed'), 'data' => null]);
});

test('login is blocked after 5 failed attempts', function () {
    $user = User::factory()->create();

    foreach (range(1, 5) as $attempt) {
        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'wrong-password']);
    }

    $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'password'])
        ->assertStatus(429)
        ->assertHeader('Retry-After')
        ->assertJsonPath('status', 'error');
});

test('logout revokes the current token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('api')->plainTextToken;

    $this->withToken($token)->postJson('/api/v1/auth/logout')
        ->assertOk()
        ->assertJsonPath('message', 'Logged out.');

    expect($user->tokens()->count())->toBe(0);
});

test('a reset link can be requested without revealing if the account exists', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->postJson('/api/v1/auth/forgot-password', ['email' => $user->email])->assertOk();
    $this->postJson('/api/v1/auth/forgot-password', ['email' => 'nobody@example.com'])
        ->assertOk()
        ->assertJsonPath('message', 'If the account exists, a reset link has been sent.');

    Notification::assertSentTo($user, ResetPassword::class);
});

test('the password can be reset with a valid token', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    $this->postJson('/api/v1/auth/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertOk();

    $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'new-password'])->assertOk();
});

test('an invalid reset token returns 422', function () {
    $user = User::factory()->create();

    $this->postJson('/api/v1/auth/reset-password', [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertStatus(422)->assertJsonValidationErrors(['email'], 'data.errors');
});
