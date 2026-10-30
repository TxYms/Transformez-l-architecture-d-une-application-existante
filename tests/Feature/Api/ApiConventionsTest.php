<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('unknown endpoints return a JSON 404', function () {
    $this->getJson('/api/v1/unknown')
        ->assertNotFound()
        ->assertExactJson(['status' => 'error', 'message' => 'Resource not found.', 'data' => null]);
});

test('a wrong HTTP verb returns 405', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->deleteJson('/api/v1/tags')
        ->assertStatus(405)
        ->assertJsonPath('status', 'error');
});

test('errors are JSON even without the Accept header', function () {
    $this->get('/api/v1/notes')
        ->assertUnauthorized()
        ->assertJsonPath('message', 'Unauthenticated.');
});
