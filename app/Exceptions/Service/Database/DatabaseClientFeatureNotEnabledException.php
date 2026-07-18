<?php

namespace Hexactyl\\Exceptions\Service\Database;

use Hexactyl\\Exceptions\HexactylException;

class DatabaseClientFeatureNotEnabledException extends HexactylException
{
    public function __construct()
    {
        parent::__construct('Client database creation is not enabled in this Panel.');
    }
}
