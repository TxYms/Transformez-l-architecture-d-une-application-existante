<?php

namespace App\Services;

use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\LoginThrottledException;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    private const MAX_LOGIN_ATTEMPTS = 5;

    private const TOKEN_NAME = 'api';

    public function __construct(private readonly UserRepositoryInterface $users) {}

    public function register(array $data): AuthResult
    {
        $user = $this->users->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($user));

        return new AuthResult($user, $this->issueToken($user));
    }

    public function login(string $email, string $password, string $ip): AuthResult
    {
        $throttleKey = Str::transliterate(Str::lower($email).'|'.$ip);

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_LOGIN_ATTEMPTS)) {
            event(new Lockout(request()));

            throw new LoginThrottledException(RateLimiter::availableIn($throttleKey));
        }

        $user = $this->users->findByEmail($email);

        if ($user === null || ! Hash::check($password, $user->password)) {
            RateLimiter::hit($throttleKey);

            throw new InvalidCredentialsException;
        }

        RateLimiter::clear($throttleKey);

        return new AuthResult($user, $this->issueToken($user));
    }

    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }

    public function sendResetLink(string $email): void
    {
        Password::sendResetLink(['email' => $email]);
    }

    public function resetPassword(array $data): void
    {
        $status = Password::reset(
            $data,
            function (User $user) use ($data) {
                $this->users->update($user, [
                    'password' => Hash::make($data['password']),
                    'remember_token' => Str::random(60),
                ]);

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }
    }

    private function issueToken(User $user): string
    {
        return $user->createToken(self::TOKEN_NAME)->plainTextToken;
    }
}
