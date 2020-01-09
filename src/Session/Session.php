<?php
namespace bianky\Session;

class Session {

    /**
     * Session start
     * 
     * @return void
     */
    public static function start()
    {
        if (! session_id()) {
            ini_set('session.use_only_cookies', 1);
            session_start();
        }
    }

    /**
     * Set new session
     * 
     * @param string $key
     * @param mixed $value
     * @return void 
     */
    public static function set($key, $value) 
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Check if session have key
     * 
     * @param string $key
     * @return bool 
     */
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Get session value by given value
     * 
     * @param string $key
     * @return mixed 
     */
    public static function get($key)
    {
        return static::has($key) ? $_SESSION[$key]:null;
    }

    /**
     * Remove session by given key
     * 
     * @param string $key 
     * @return void
     */
    public static function remove($key)
    {
        unset($_SESSION[$key]);
    }
    
    /**
     * Return all sessions
     * 
     * @return array 
     */
    public static function all()
    {
        return $_SESSION;
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

    /**
     * Get flash message
     * 
     * @param string $key
     * @return mixed $value  
     */
    public static function flash($key)
    {
        $value = null;
        if (static::has($key)){ 
            $value = static::get($key);
            static::remove($key);
        }
        return $value;
    }
}