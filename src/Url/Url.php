<?php
namespace bianky\Url;

use bianky\Http\Request;
use bianky\Http\Server;

class Url 
{
    /**
     * Get full path
     * 
     * @param string $path
     * @return  string $path
     */
    public static function path($path)
    {
        return Request::baseUrl() .'/' .trim($path, '/');
    }

    /**
     * Get previous url 
     * 
     * @return string
     */
    public static function previous()
    {
        return Server::get('HTTP_REFERER');
    }

    /**
     * Redirect into page
     * 
     * @param string $path 
     * @return void
     */
    public static function redirect($path)
    {
        header('location: ' .$path);
        exit();
    }
}