<?php

namespace App\Services;

use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Support\Collection;

class TagService
{
    public function __construct(private readonly TagRepositoryInterface $tags) {}

    public function list(): Collection
    {
        return $this->tags->all();
    }

    public function create(array $data): Tag
    {
        return $this->tags->create(['name' => $data['name']]);
    }
}
