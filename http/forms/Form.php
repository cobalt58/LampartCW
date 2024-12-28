<?php

namespace http\forms;

use core\validation\ValidationException;

class Form
{
    protected array $attributes;
    protected array $errors = [];

    public function __construct($attributes)
    {
        $this->attributes = $attributes;
        //do validation here
    }

    public static function validate($attributes)
    {
        $instance = new static($attributes);
        if ($instance->failed()) $instance->throw();

        return $instance;
    }

    public function throw()
    {
        ValidationException::throw($this->errors, $this->attributes);
    }

    public function failed(): bool
    {
        return (bool)count($this->errors);
    }

    public function error($error, $message)
    {
        $this->errors[$error] = $message;

        return $this;
    }

    public function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    public function appendErrors(array $errors)
    {
        $this->errors = array_merge($this->errors, $errors);
        return $this;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}