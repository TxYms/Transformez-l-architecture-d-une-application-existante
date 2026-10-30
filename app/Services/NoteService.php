<?php

namespace App\Services;

use App\Models\Note;
use App\Models\User;
use App\Repositories\Contracts\NoteRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class NoteService
{
    public function __construct(private readonly NoteRepositoryInterface $notes) {}

    public function listFor(User $user, ?int $tagId = null): Collection
    {
        return $this->notes->forUser($user->id, $tagId);
    }

    public function find(int $id): Note
    {
        return $this->notes->find($id)
            ?? throw (new ModelNotFoundException)->setModel(Note::class, [$id]);
    }

    public function createFor(User $user, array $data): Note
    {
        return $this->notes->create([
            'user_id' => $user->id,
            'tag_id' => $data['tag_id'],
            'text' => $data['text'],
        ]);
    }

    public function update(Note $note, array $data): Note
    {
        return $this->notes->update($note, [
            'tag_id' => $data['tag_id'],
            'text' => $data['text'],
        ]);
    }

    public function delete(Note $note): void
    {
        $this->notes->delete($note);
    }
}
