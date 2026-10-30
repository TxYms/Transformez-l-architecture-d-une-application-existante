<?php

namespace App\Repositories\Eloquent;

use App\Models\Note;
use App\Repositories\Contracts\NoteRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentNoteRepository implements NoteRepositoryInterface
{
    public function forUser(int $userId, ?int $tagId = null): Collection
    {
        return Note::query()
            ->with('tag')
            ->where('user_id', $userId)
            ->when($tagId !== null, fn ($query) => $query->where('tag_id', $tagId))
            ->latest()
            ->orderByDesc('id')
            ->get();
    }

    public function find(int $id): ?Note
    {
        return Note::with('tag')->find($id);
    }

    public function create(array $attributes): Note
    {
        return Note::create($attributes)->load('tag');
    }

    public function update(Note $note, array $attributes): Note
    {
        $note->update($attributes);

        return $note->load('tag');
    }

    public function delete(Note $note): void
    {
        $note->delete();
    }
}
