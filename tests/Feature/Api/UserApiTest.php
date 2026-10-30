<?php

use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests cannot access the profile', function () {
    $this->getJson('/api/v1/user')
        ->assertUnauthorized()
        ->assertExactJson(['status' => 'error', 'message' => 'Unauthenticated.', 'data' => null]);
});

test('the authenticated user can see their profile', function () {
    $user = Sanctum::actingAs(User::factory()->create());

    $this->getJson('/api/v1/user')
        ->assertOk()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonMissingPath('data.password')
        ->assertJsonMissingPath('data.remember_token');
});

test('the profile can be updated and a new email is no longer verified', function () {
    $user = Sanctum::actingAs(User::factory()->create());

    $this->patchJson('/api/v1/user', ['name' => 'New Name', 'email' => 'new@example.com'])
        ->assertOk()
        ->assertJsonPath('data.name', 'New Name');

    $user->refresh();
    expect($user->email)->toBe('new@example.com')
        ->and($user->email_verified_at)->toBeNull();
});

test('the email must stay unique', function () {
    User::factory()->create(['email' => 'taken@example.com']);
    Sanctum::actingAs(User::factory()->create());

    $this->patchJson('/api/v1/user', ['email' => 'taken@example.com'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email'], 'data.errors');
});

test('the password can be changed with the current password', function () {
    $user = Sanctum::actingAs(User::factory()->create());

    $this->putJson('/api/v1/user/password', [
        'current_password' => 'password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertOk();

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('a wrong current password is rejected', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->putJson('/api/v1/user/password', [
        'current_password' => 'wrong-password',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertStatus(422)->assertJsonValidationErrors(['current_password'], 'data.errors');
});

test('the account can be deleted with its notes and tokens', function () {
    $user = User::factory()->create();
    $user->createToken('api');
    Note::factory()->for($user)->create();
    Sanctum::actingAs($user);

    $this->deleteJson('/api/v1/user', ['password' => 'password'])
        ->assertOk()
        ->assertJsonPath('data', null);

    expect(User::find($user->id))->toBeNull()
        ->and(Note::where('user_id', $user->id)->count())->toBe(0)
        ->and($user->tokens()->count())->toBe(0);
});

test('deleting the account requires the password', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->deleteJson('/api/v1/user', ['password' => 'wrong-password'])
        ->assertStatus(422);
});
