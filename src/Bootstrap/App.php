<?php
namespace bianky\Bootstrap;

use App\Http\Response;
use bianky\ErrorHandller\Whoops;
use bianky\Http\Request;
use bianky\Session\Session;
use bianky\File\File;
use bianky\Router\Route;

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
        die($data);
        Response::output($data);
    }
}