<?php
namespace bianky\Cookie;

class Cookie {

    /**
     * Set new cookie
     * 
     * @param string $key
     * @param mixed $value
     * @return void 
     */
    public static function set($key, $value) 
    {
        $expired = time() + (1 * 365 * 24 * 60 * 60);
        setcookie($key, $value, $expired, '/', '', false, true);
    }

    /**
     * Check if Cookie have key
     * 
     * @param string $key
     * @return bool 
     */
    public static function has($key)
    {
        return isset($_COOKIE[$key]);
    }

    /**
     * Get cookie value by given value
     * 
     * @param string $key
     * @return mixed 
     */
    public static function get($key)
    {
        return static::has($key) ? $_COOKIE[$key]:null;
    }

    /**
     * Remove cookie by given key
     * 
     * @param string $key 
     * @return void
     */
    public static function remove($key)
    {
        unset($_COOKIE[$key]);
    }
    
    /**
     * Return all sessions
     * 
     * @return array 
     */
    public static function all()
    {
        return $_COOKIE;
    }

    /**
     * Destroy all sessions
     * 
     * @return void 
     */
    public static function destroy()
    {
        foreach(static::all() as $key) {
            static::remove($key);
        }
    }
}