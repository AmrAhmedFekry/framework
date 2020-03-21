<?php
namespace bianky\Router;

use BadMethodCallException;
use bianky\Http\Request;
use bianky\View\View;

class Route {
    
    /**
     * Route container
     * 
     * @var array $routes 
     */
    private static $routes = [];

    /**
     * Middleware
     * 
     * @var string $middleware 
     */
    private static $middleware;

    /**
     * Prefix 
     * 
     * @var string $prefix
     */
    private static $prefix;

    /**
     * Add route 
     * 
     * @param string $methods
     * @param string $uri
     * @param object|callback $callback
     * @return void
     */
    private static function addRoute($methods, $uri, $callback) 
    {
        $uri = trim($uri, '/');
        $uri = rtrim(static::$prefix .'/' .$uri, '/');
        $uri = $uri ?: '/';

        foreach (explode('|', $methods) as $method) {
            static::$routes [] = [
                'uri' => $uri, 
                'callback' => $callback,
                'method' => $method,
                'middleware' => static::$middleware
            ];
        }
    }

    /**
     * Add new new get route
     * 
     * @param string $uri
     * @param object|callback $callback
     * @return void
     */
    public static function get($uri, $callback) 
    {
        static::addRoute('GET', $uri, $callback);
    }

    /**
     * Add new new post route
     * 
     * @param string $uri
     * @param object|callback $callback
     * @return void
     */
    public static function post($uri, $callback) 
    {
        static::addRoute('POST', $uri, $callback);
    }

    /**
     * Add new new put route
     * 
     * @param string $uri
     * @param object|callback $callback
     * @return void
     */
    public static function put($uri, $callback) 
    {
        static::addRoute('PUT', $uri, $callback);
    }

    /**
     * Add new new delete route
     * 
     * @param string $uri
     * @param object|callback $callback
     * @return void
     */
    public static function delete($uri, $callback) 
    {
        static::addRoute('DELETE', $uri, $callback);
    }

    /**
     * Add new new any route
     * 
     * @param string $uri
     * @param object|callback $callback
     * @return void
     */
    public static function any($uri, $callback) 
    {
        static::addRoute('GET|POST', $uri, $callback);
    }

    /**
     * Get all routes
     * 
     * @return array $routes
     */
    public static function allRoutes()
    {
        return static::$routes;
    }

    /**
     * Set prefix for routing
     * 
     * @param string $prefix
     * @param string $callback
     * @return void
     */
    public static function prefix($prefix, $callback)
    {
        $parentPrefix = static::$prefix;
        static::$prefix .= '/'. trim($prefix, '/');
        
        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new \BadMethodCallException("This not a valid callback function");
        }

        static::$prefix = $parentPrefix;
    }  

    /**
     * Set middleware for routing
     * 
     * @param string $middleware
     * @param string $callback
     * @return void
     */
    public static function middleware($middleware, $callback)
    {
        $parentMiddleware = static::$middleware;
        static::$middleware .= '|'. trim($middleware, '|');
        
        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new \BadMethodCallException("This not a valid callback function");
        }

        static::$middleware = $parentMiddleware;
    }

    /**
     * Handle the user request
     * 
     * @return mixed
     */
    public static function handle()
    {
        $uri =  Request::url();

        foreach(static::$routes as $route) {            
            $matched = true;

            $route['uri'] = preg_replace('/\/{(.*?)}/', '/(.*?)', $route['uri']);
            $route['uri'] = '#^' .$route['uri'] .'$#';
            
            if (preg_match($route['uri'], $uri, $matches)) {
                array_shift($matches);

                $params = array_values($matches);
                foreach ($params as $param) {
                    if (strpos($param, '/')) {
                        $matched = false;
                    }
                }
                
                if ($route['method'] != Request::method()) {
                    $matched = false;
                }

                if ($matched == true) {
                    return static::invoke($route, $params);
                }
            }
        }
        return View::render('errors.404');
    }

    /**
     * Invoke the route
     * 
     * @param string $routeName
     * @param string $params
     * @return object|callback $callback
     */
    private static function invoke($route, $params = []) 
    {
        static::executeMiddleware($route);
        $callback = $route['callback'];
        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        } elseif (strpos($callback, '@') !== false) {
            list($controller, $method) = explode('@', $callback);
            $controller = 'App\Controllers\\' .$controller;
            if (class_exists($controller)) {
                $object = new $controller;
                if (method_exists($object, $method)) {
                    return call_user_func_array([$object, $method], $params);
                } else {
                    throw new \BadFunctionCallException("The method" .$method ." is not available");
                }
            } else {
                throw new \ReflectionException("The " .$controller. " is not available");
            }
        } else {
            throw new \InvalidArgumentException("Please provide valid callback function");
        }
    }

    /**
     * Execute middleware of route
     * 
     * @param string $route
     */
    private static function executeMiddleware($route) 
    {
        foreach (explode('|', $route['middleware']) as $middleware) {
            if ($middleware != '') {
                $middleware = 'App\Middleware\\' .$middleware;
                if (class_exists($middleware)) {
                    $object = new $middleware;
                    $nextClosure = function (){
                        return Request::url();
                    };
                    $result = call_user_func_array([$object, 'handle'], [new Request,$nextClosure]);
                    var_dump($result);
                    die();
                } else {
                    throw new \ReflectionException("Class " .$middleware ." is not found");
                }
            }
        }
    } 
}