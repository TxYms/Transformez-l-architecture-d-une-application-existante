<?php

namespace App\Repositories\Contracts;

use App\Models\Note;
use Illuminate\Support\Collection;

interface NoteRepositoryInterface
{
    public function forUser(int $userId, ?int $tagId = null): Collection;

    public function find(int $id): ?Note;

    public function create(array $attributes): Note;

    public function update(Note $note, array $attributes): Note;

    public function delete(Note $note): void;
}
