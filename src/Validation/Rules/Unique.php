<?php
namespace bianky\Validation\Rules;

use bianky\Database\Database;
use Rakit\Validation\Rule;

class UniqueRule extends Rule
{
    protected $message = ":attribute :value has been used";
    
    protected $fillableParams = ['table', 'column', 'except'];
        
    public function check($value): bool
    {
        // make sure required parameters exists
        $this->requireParameters(['table', 'column']);
    
        // getting parameters
        $column = $this->parameter('column');
        $table = $this->parameter('table');
        $except = $this->parameter('except');
	
        if ($except AND $except == $value) {
            return true;
        }
	
        // do query
        $stmt = $this->pdo->prepare("select count(*) as count from `{$table}` where `{$column}` = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $data = Database::table($table)->where($column, '=', $value)->first();

        // true for valid, false for invalid
        return $data ? false : true;
    }
}