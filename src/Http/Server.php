<?php
namespace bianky\Http;

class Server
{
    /**
     * Check if server have the key
     * 
     * @return bool
     */
    public static function has($key)
    {
        return isset($_SERVER[$key]);
    }

    /**
     * Get the value of given key
     * 
     * @param string $key 
     * @return mixed
     */
    public static function get($key)
    {
        return static::has($key) ? $_SERVER[$key] : null;
    }
    
    /**
     * Get all server data 
     * 
     * @return array
     */
    public static function all()
    {
        return $_SERVER;
    }

    /**
     * Get path info for given path  
     * 
     * @param string $path
     * @return array 
     */
    public static function pathInfo($path)
    {
        return pathinfo($path);
    }
}