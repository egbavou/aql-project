<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

final class TagService
{
    /**
     * @return Collection<int, Tag>
     */
    public function list(): Collection
    {
        return Tag::all();
    }
}
