<?php

use App\Models\Note;
use App\Models\Tag;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = Sanctum::actingAs(User::factory()->create());
    $this->tag = Tag::factory()->create(['name' => 'travail']);
});

test('guests get 401 on notes', function () {
    $this->app['auth']->forgetGuards();

    $this->withHeader('Authorization', 'Bearer invalid')
        ->getJson('/api/v1/notes')
        ->assertUnauthorized();
});

test('a user only lists their own notes, newest first', function () {
    $older = Note::factory()->for($this->user)->for($this->tag)->create(['created_at' => now()->subDay()]);
    $newer = Note::factory()->for($this->user)->for($this->tag)->create();
    Note::factory()->create();

    $this->getJson('/api/v1/notes')
        ->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $newer->id)
        ->assertJsonPath('data.1.id', $older->id)
        ->assertJsonPath('data.0.tag.name', 'travail');
});

test('notes can be filtered by tag', function () {
    $other = Tag::factory()->create();
    Note::factory()->for($this->user)->for($this->tag)->create();
    Note::factory()->for($this->user)->for($other)->create();

    $this->getJson('/api/v1/notes?tag_id='.$other->id)
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.tag.id', $other->id);
});

test('an unknown tag filter returns 422', function () {
    $this->getJson('/api/v1/notes?tag_id=999')
        ->assertStatus(422)
        ->assertJsonValidationErrors(['tag_id'], 'data.errors');
});

test('a note can be created', function () {
    $this->postJson('/api/v1/notes', ['text' => 'Acheter du café', 'tag_id' => $this->tag->id])
        ->assertCreated()
        ->assertJsonPath('message', 'Note created.')
        ->assertJsonPath('data.text', 'Acheter du café')
        ->assertJsonPath('data.tag.id', $this->tag->id);

    expect($this->user->notes()->count())->toBe(1);
});

test('creating a note is validated', function () {
    $this->postJson('/api/v1/notes', ['text' => '', 'tag_id' => 999])
        ->assertStatus(422)
        ->assertJsonPath('status', 'error')
        ->assertJsonValidationErrors(['text', 'tag_id'], 'data.errors');
});

test('a note can be shown by its owner', function () {
    $note = Note::factory()->for($this->user)->for($this->tag)->create();

    $this->getJson("/api/v1/notes/{$note->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $note->id);
});

test('a missing note returns 404', function () {
    $this->getJson('/api/v1/notes/999')
        ->assertNotFound()
        ->assertExactJson(['status' => 'error', 'message' => 'Note not found.', 'data' => null]);
});

test("another user's note returns 403", function () {
    $note = Note::factory()->create();

    $this->getJson("/api/v1/notes/{$note->id}")->assertForbidden()->assertJsonPath('status', 'error');
    $this->putJson("/api/v1/notes/{$note->id}", ['text' => 'x', 'tag_id' => $this->tag->id])->assertForbidden();
    $this->deleteJson("/api/v1/notes/{$note->id}")->assertForbidden();

    expect(Note::find($note->id))->not->toBeNull();
});

test('a note can be updated', function () {
    $note = Note::factory()->for($this->user)->for($this->tag)->create();
    $other = Tag::factory()->create();

    $this->putJson("/api/v1/notes/{$note->id}", ['text' => 'Nouveau texte', 'tag_id' => $other->id])
        ->assertOk()
        ->assertJsonPath('data.text', 'Nouveau texte')
        ->assertJsonPath('data.tag.id', $other->id);
});

test('a note can be deleted', function () {
    $note = Note::factory()->for($this->user)->for($this->tag)->create();

    $this->deleteJson("/api/v1/notes/{$note->id}")
        ->assertOk()
        ->assertExactJson(['status' => 'success', 'message' => 'Note deleted.', 'data' => null]);

    expect(Note::find($note->id))->toBeNull();
});
