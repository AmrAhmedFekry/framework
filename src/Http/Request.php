<?php
namespace bianky\Http;

class Request 
{
    /**
     * Base url
     * 
     * @var string
     */
    private static $baseUrl;
        
    /**
     * Full url
     * 
     * @var string
     */
    private static $fullUrl;

    /**
     * Url
     * 
     * @var string
     */
    private static $url;

    /**
     * Query string
     * 
     * @var string
     */
    private static $queryString;

    /**
     * Handle the request
     * 
     * @return void 
     */
    public static function handle() 
    {
        static::getScriptName();
        static::setBaseUrl();
        static::setUrl();
    }

    /**
     * Set base url.
     * 
     * @return void
     */
    private static function setBaseUrl()
    {  
        static::$baseUrl = static::getProtocol() .static::getHost() .static::getScriptName();
    }

    /**
     * Set Url.
     * 
     * @return void
     */
    private static function setUrl()
    {
        $requestUri = urldecode(Server::get('REQUEST_URI'));
        $requestUri = str_replace(static::getScriptName() ,' ' ,$requestUri);
        
        $requestUri = rtrim(str_replace(static::getScriptName() ,'' , $requestUri) ,'/');

        $queryString = '';
        static::$fullUrl = $requestUri;
        
        if (strpos($requestUri, '?') !== false) {
            list($requestUri, $queryString) = explode('?', $requestUri);
        }

        $requestUri = str_replace(' ','', $requestUri);
        static::$url = $requestUri?:'/';
        static::$queryString = $queryString;
    }

    /**
     * Get query string of request 
     * 
     * @return string
     */
    public static function queryString()
    {
        return static::$queryString;
    }

    /**
     * Get query string of request 
     * 
     * @return string
     */
    public static function baseUrl()
    {
        return static::$baseUrl;
    }
        
    /**
     * Get query string of request 
     * 
     * @return string
     */
    public static function fullUrl()
    {
        return static::$fullUrl;
    } 

    /**
     * Get the url
     * 
     * @return void
     */
    public static function url()
    {
        return static::$url;
    } 
    /**
     * Request protocol.
     * 
     * @return string
     */
    private static function getProtocol()
    {
        return Server::get('REQUEST_SCHEME') . '://';
    }

    /**
     * Request host.
     * 
     * @return string
     */
    private static function getHost()
    {
        return Server::get('HTTP_HOST');
    }
        
    /**
     * Script name.
     * 
     * @return string
     */
    private static function getScriptName()
    {
        return str_replace('\\', '', dirname(strtolower(Server::get('SCRIPT_NAME'))));
    }

    /**
     * Get request method
     * 
     * @return string 
     */
    public static function method()
    {
        return Server::get('REQUEST_METHOD');
    }

    /**
     * Check if the request has the key
     * 
     * @param array $type
     * @param string $key
     * @return bool
     */
    public static function has($type, $key)
    {
        return array_key_exists($key ,$type);
    }
    
    /**
     * Get the value from the request
     * 
     * @param string $key
     * @param string $type
     * @return bool
     */
    public static function value($key, array $type = null)
    {
        $type = isset($type) ? $type : $_REQUEST; 
        return static::has($type, $key) ? $type[$key] : null;
    }
    /**
     * Get value from get request
     * 
     * @param string $key
     * @return string $value 
     */
    public static function get($key) 
    {
        return static::value($key, $_GET);
    }

    /**
     * Get value from post request
     * 
     * @param string $key
     * @return string $value 
     */
    public static function post($key) 
    {
        return static::value($key, $_POST);
    }

    /**
     * Set value for request by given value
     * 
     * @param string $key
     * @param string $value
     * @return string $value
     */
    public static function set($key, $value)
    {
        $_REQUEST[$key] = $value;
        $_POST[$key] = $value;
        $_GET[$key] = $value;

        return $value;
    }
    
    /**
     * Get previous request value
     *
     * @return string
     */
    public static function previous() {
        return Server::get('HTTP_REFERER');
    }

    /**
     * Get request all
     *
     * @return array
     */
    public static function all() {
        return $_REQUEST;
    }
}