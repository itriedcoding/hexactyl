<?php

namespace Hexactyl\\Exceptions\Service;

use Illuminate\Http\Response;
use Hexactyl\\Exceptions\DisplayException;

class HasActiveServersException extends DisplayException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
