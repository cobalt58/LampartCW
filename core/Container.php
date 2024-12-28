<?php

namespace core;

use Exception;

class Container
{
    protected array $bindings = [];
    protected array $singletons = [];
    /**
     * @param string $key
     * @param $resolver
     * @return void
     */
    public function bind(string $key, $resolver)
    {
        $this->bindings[$key] = $resolver;
    }


    /**
     * @param string $key
     * @param $resolver
     * @return void
     */
    public function singleton(string $key, $resolver)
    {
        $this->singletons[$key] = $resolver;
    }

    /**
     * @throws Exception
     */
    public function resolve($key)
    {
        if (!array_key_exists($key, $this->bindings) && !array_key_exists($key, $this->singletons)) {
            throw new Exception("No binding resolver for {$key}");
        }

        if (array_key_exists($key, $this->singletons)) {
            if (is_callable($this->singletons[$key])) {
                $this->singletons[$key] = call_user_func($this->singletons[$key]);
            }
            return $this->singletons[$key];
        }

        $resolver = $this->bindings[$key];
        return call_user_func($resolver);
    }
}