<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

final class TagService
{
    public function list(): Collection
    {
        return Tag::all();
    }
}
