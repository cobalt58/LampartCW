<?php

namespace core;

class Session
{
    /**
     * Put value to the session with specific key
     * @param mixed $key Key for value
     * @param mixed $value Value
     * @return void
     */
    public static function put($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Returns value from the session if the key exist or default value if key does not exist
     * @param mixed $key Key for value
     * @param mixed $default Default value
     * @return mixed|null
     */
    public static function get($key, $default = null)
    {
        return $_SESSION['_flash'][$key] ?? $_SESSION[$key] ?? $default;
    }
    /**
     * Returns old form data if exists
     * @param $key
     * @param string $default
     * @return mixed
     */
    public static function old($key, string $default = '')
    {
        return static::get('old')[$key] ?? $default;
    }
    /**
     * Returns true if key exists, or false if the key does not exist in session
     * @param mixed $key Key for check
     * @return bool
     */
    public static function has($key): bool
    {
        //return (bool) static::resolve($key);
        return array_key_exists($key, $_SESSION);
    }

    /**
     * Flash value to the session with specific key
     * @param mixed $key Key for array
     * @param mixed $value Value
     * @return void
     */
    public static function flash($key, $value)
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Clear th flashed values
     * @return void
     */
    public static function unflash()
    {
        unset($_SESSION['_flash']);
    }

    /**
     * Clear the $_SESSION
     * @return void
     */
    public static function flush()
    {
        $_SESSION = [];
    }

    /**
     * Fully destroy the session
     * @return void
     */
    public static function destroy()
    {
        static::flush();
        session_destroy();
        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time()-3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
}