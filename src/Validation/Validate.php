<?php
namespace bianky\Validation;

use bianky\Http\Request;
use Rakit\Validation\Validator;
use bianky\Session\Session;
use bianky\Url\Url;

class Validate 
{
    /**
     * Validate request 
     * 
     * @param array $rules
     * @param bool  $json
     * 
     * @return mixed 
     */
    public static function validate(array $rules, $json)
    {
        $validator = new Validator;

        $validation = $validator->validate($_POST + $_FILES, $rules);
        $errors  = $validation->errors;
        if ($validator->fails()) {
            if ($json) {
                return ['errors' => $errors->firstOfAll()];
            } else {
                Session::set('errors', $errors);
                Session::set('old', Request::all());
                return Url::redirect(Url::previous());
            }
        }
    }
}