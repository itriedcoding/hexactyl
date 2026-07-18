<?php

namespace Hexactyl\\Repositories\Eloquent;

use Hexactyl\\Models\RecoveryToken;

class RecoveryTokenRepository extends EloquentRepository
{
    public function model(): string
    {
        return RecoveryToken::class;
    }
}
