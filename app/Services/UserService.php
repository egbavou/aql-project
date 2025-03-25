<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

final class UserService
{
    public function show(Request $request): User
    {
        return $request->user();
    }

    /**
     * @throws Throwable
     */
    public function update(int $id, UserUpdateRequest $request): User
    {
        return DB::transaction(function () use ($id, $request) {
            $user = User::where('id', $request->user()->id)
                ->findOrFail($id);

            $user->name = $request->name ?? $user->name;
            $user->email = $request->email ?? $user->email;
            $user->save();

            return $user;
        });
    }


    public function delete(int $id, Request $request): void
    {
        $user = User::where('id', $request->user()->id)
            ->findOrFail($id);
        $user->delete();
    }
}
