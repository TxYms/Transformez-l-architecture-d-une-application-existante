<?php

namespace App\Repositories\Eloquent;

use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentTagRepository implements TagRepositoryInterface
{
    public function all(): Collection
    {
        return Tag::query()->orderBy('name')->get();
    }

    public function create(array $attributes): Tag
    {
        return Tag::create($attributes);
    }
}
