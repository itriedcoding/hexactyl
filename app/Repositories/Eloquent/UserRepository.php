<?php

namespace Hexactyl\\Repositories\Eloquent;

use Hexactyl\\Models\User;
use Hexactyl\\Contracts\Repository\UserRepositoryInterface;

class UserRepository extends EloquentRepository implements UserRepositoryInterface
{
    /**
     * Return the model backing this repository.
     */
    public function model(): string
    {
        return User::class;
    }
}
