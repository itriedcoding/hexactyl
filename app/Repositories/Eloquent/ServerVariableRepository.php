<?php

namespace Hexactyl\\Repositories\Eloquent;

use Hexactyl\\Models\ServerVariable;
use Hexactyl\\Contracts\Repository\ServerVariableRepositoryInterface;

class ServerVariableRepository extends EloquentRepository implements ServerVariableRepositoryInterface
{
    /**
     * Return the model backing this repository.
     */
    public function model(): string
    {
        return ServerVariable::class;
    }
}
