<?php
namespace bianky\ErrorHandller;

class Whoops {
    
    /**
     * Whoops constructor
     * 
     */
    private function __construct (){}
    
    /**
     * Handle the whoops errors
     * 
     * @return void
     */
    public function handle()
    {
        $whoops = new \Whoops\Run;
        $whoops->prependHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->register();
    }
}