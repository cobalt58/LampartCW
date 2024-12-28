<?php

namespace core\exceptions;

use Exception;

class AuthException extends Exception
{
    public static function throw($message = 'You are not allowed to view this page!')
    {
        throw new static($message, 505);
    }
}