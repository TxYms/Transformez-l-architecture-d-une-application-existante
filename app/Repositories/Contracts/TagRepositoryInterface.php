<?php

namespace App\Repositories\Contracts;

use App\Models\Tag;
use Illuminate\Support\Collection;

interface TagRepositoryInterface
{
    public function all(): Collection;

    public function create(array $attributes): Tag;
}
