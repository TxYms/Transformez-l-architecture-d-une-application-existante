<?php

use App\Livewire\Notes;
use App\Livewire\TagForm;
use App\Models\Note;
use App\Models\Tag;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('the existing notes screen still creates and deletes notes', function () {
    $user = User::factory()->create();
    $tag = Tag::factory()->create();
    $this->actingAs($user);

    Livewire::test(Notes::class)
        ->set('text', 'Note depuis Livewire')
        ->set('tag_id', $tag->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Note depuis Livewire');

    $note = $user->notes()->first();

    Livewire::test(Notes::class)
        ->call('delete', $note->id)
        ->assertDontSee('Note depuis Livewire');

    expect(Note::count())->toBe(0);
});

test('the existing notes screen cannot delete another user note', function () {
    $this->actingAs(User::factory()->create());
    $note = Note::factory()->create();

    Livewire::test(Notes::class)->call('delete', $note->id)->assertForbidden();

    expect(Note::find($note->id))->not->toBeNull();
});

test('the existing tag form still validates and creates tags', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(TagForm::class)
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required'])
        ->set('name', 'perso')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('tagCreated');

    expect(Tag::where('name', 'perso')->exists())->toBeTrue();
});
