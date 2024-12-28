<?php

namespace core\exceptions;

use Exception;

class ServerException extends Exception
{
    public static function throw($message)
    {
        throw new static($message, 505);
    }
}