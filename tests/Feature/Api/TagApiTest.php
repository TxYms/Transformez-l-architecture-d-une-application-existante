<?php

use App\Models\Tag;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests cannot list tags', function () {
    $this->getJson('/api/v1/tags')->assertUnauthorized();
});

test('tags are listed by name', function () {
    Sanctum::actingAs(User::factory()->create());
    Tag::factory()->create(['name' => 'travail']);
    Tag::factory()->create(['name' => 'courses']);

    $this->getJson('/api/v1/tags')
        ->assertOk()
        ->assertJsonPath('data.0.name', 'courses')
        ->assertJsonPath('data.1.name', 'travail');
});

test('a tag can be created', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->postJson('/api/v1/tags', ['name' => 'perso'])
        ->assertCreated()
        ->assertJsonPath('data.name', 'perso');
});

test('a tag name must be unique and at most 50 characters', function () {
    Sanctum::actingAs(User::factory()->create());
    Tag::factory()->create(['name' => 'perso']);

    $this->postJson('/api/v1/tags', ['name' => 'perso'])->assertStatus(422);
    $this->postJson('/api/v1/tags', ['name' => str_repeat('a', 51)])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name'], 'data.errors');
});
