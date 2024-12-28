<?php

namespace core\validation;

use Exception;

class ValidationException extends Exception
{
    protected array $errors = [];
    protected array $old = [];

    /**
     * @throws ValidationException
     */
    public static function throw($errors, $old)
    {
        $instance = new static();

        $instance->errors = $errors;
        $instance->old = $old;

        throw $instance;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function old(): array
    {
        return $this->old;
    }
}