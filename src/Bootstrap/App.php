<?php
namespace bianky\Bootstrap;

use bianky\File\File;
use bianky\Http\Request;
use bianky\Router\Route;
use bianky\Http\Response;
use bianky\Session\Session;
use bianky\ErrorHandller\Whoops;

class App
{
    /**
     * App constructor
     * 
     * @return void
     */
    private function __construct() {}

    /**
     * Run the application
     * 
     * @return void
     */
    public static function run()
    {
        Whoops::handle();
        Session::start();
        Request::handle();

        File::requireDirectory('routes');

        $data = Route::handle();

        Response::output($data);
    }
}