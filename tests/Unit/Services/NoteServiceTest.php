<?php

use App\Models\Note;
use App\Models\User;
use App\Repositories\Contracts\NoteRepositoryInterface;
use App\Services\NoteService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

function fakeNoteRepository(): NoteRepositoryInterface
{
    return new class implements NoteRepositoryInterface
    {
        public array $created = [];

        public function forUser(int $userId, ?int $tagId = null): Collection
        {
            return collect();
        }

        public function find(int $id): ?Note
        {
            return null;
        }

        public function create(array $attributes): Note
        {
            $this->created[] = $attributes;

            return new Note($attributes);
        }

        public function update(Note $note, array $attributes): Note
        {
            return $note->fill($attributes);
        }

        public function delete(Note $note): void {}
    };
}

test('a note is always created for the given user', function () {
    $repository = fakeNoteRepository();
    $user = new User;
    $user->id = 7;

    (new NoteService($repository))->createFor($user, [
        'text' => 'Bonjour',
        'tag_id' => 2,
        'user_id' => 99,
    ]);

    expect($repository->created[0])->toBe(['user_id' => 7, 'tag_id' => 2, 'text' => 'Bonjour']);
});

test('finding a missing note throws a not found exception', function () {
    (new NoteService(fakeNoteRepository()))->find(42);
})->throws(ModelNotFoundException::class);
