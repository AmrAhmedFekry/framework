<?php
namespace bianky\Http;

class Response {


    /**
     * Return the response data in json
     * 
     * @param mixed $data
     * @return mixed 
     */
    public static function json($data) {
        return json_encode($data);
    }

    /**
     * Output data
     * 
     * @param mixed $data
     */
    public function output($data) 
    {
        if (! $data) {return ;}
        if (is_array($data)) {
            return static::json($data);
        }
        
    }
}