<?php
namespace core\exceptions;

use Exception;

class NotFoundException extends Exception
{
    public static function throw($message = 'Page not found')
    {
        throw new static($message, 404);
    }
}