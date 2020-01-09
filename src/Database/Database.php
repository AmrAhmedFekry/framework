<?php
namespace bianky\Database;

use bianky\File\File;
use bianky\Http\Request;
use Exception;
use PDOException;
use PDO;

class Database {

    /**
     * Database instance
     */
    protected static $instance;

    /**
     * Database connection
     */
    protected static $connection;

    /**
     * Select data
     * 
     * @var array 
     */
    protected static $select;

    /**
     * Table name 
     * 
     * @var string
     */
    protected static $table;

    /**
     * Join data
     * 
     * @var string
     */
    protected static $join;

    /**
     * Where data
     * 
     * @var string
     */
    protected static $where;

    /**
     * Binding array
     * 
     * @var array
     */
    protected static $whereBinding = [];

    /**
     * Group by data
     * 
     * @var string
     */
    protected static $groupBy;

    /**
     * Having data
     * 
     * @var string
     */
    protected static $having;

    /**
     * Having binding
     * 
     * @var array
     */
    protected static $havingBinding = [];

    /**
     * Order by
     * 
     * @var string
     */
    protected static $orderBy;
    
    /**
     * Limit
     * 
     * @var string
     */
    protected static $limit;

    /**
     * Offset
     * 
     * @var string
     */
    protected static $offset;

    /**
     * Query
     * 
     * @var string
     */
    protected static $query;

    /**
     * All bindings data
     * 
     * @var array
     */
    protected static $binding = [];

    /**
     * Setter 
     * 
     * @var string
     */
    protected static $setter;

    /**
     * Last inserted id
     * 
     * @var int
     */
    protected static $lastObjectId;

    /**
     * Database constructor
     */
    private function __construct() {}

    /**
     * Connect to database
     * 
     * @return void
     */
    public static function connect() {
        
        if (! static::$connection) {
            $databaseData = File::requireFile('config/database.php');
            extract($databaseData);
            $dsn = 'mysql:dbname=' .$database .';host=' .$host .'';
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_PERSISTENT => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "set NAMES " . $charset . " COLLATE " . $collation,
            ];
            
            try {
                static::$connection = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
    }

    /**
     * Get the instance of the class
     * 
     * @return object $instance
     */
    private static function instance() 
    {
        static::connect();

        if (! self::$instance) {
            self::$instance = new Database;
        }

        return self::$instance;
    }

    /**
     * Query function 
     * 
     * @param string $query
     * @return string  
     */
    public static function query ($query = null)
    {
        static::instance();

        if ($query == null) {
            if (! static::$table) {
                throw new Exception('Unknown table');
            }
            $query = "SELECT ";
            $query .= static::$select ?: "*";
            $query .= " FROM " .static::$table ." ";
            $query .= static::$join ." ";
            $query .= static::$where ." ";
            $query .= static::$groupBy ." ";
            $query .= static::$having ." ";
            $query .= static::$orderBy ." ";
            $query .= static::$limit ." ";
            $query .= static::$offset ." ";    
        }
        static::$query = $query;
        static::$binding = array_merge(static::$whereBinding, static::$havingBinding);
        return self::instance();
    }

    /**
     * Select data from table
     * 
     * @return object $instance 
     */
    public static function select()
    {
        $selectArgs = func_get_args();
        $select = implode(',' ,$selectArgs);

        static::$select = $select;
        return static::instance();
    }

    /**
     * Define query table
     *
     * @param string $table
     * @return object $instance
     */
    public static function table($table)
    {
        static::$table = $table;
        return static::instance();
    }

    /**
     * Join table
     * 
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $type
     *
     * @return object $type
     */
    public static function join($table, $first, $operator ,$second, $type='inner') 
    {
        static::$join .= " " .$type ." JOIN " .$table ." ON " .$first .$operator .$second ." ";
        return static::instance();
    }


    /**
     * Right join table
     * 
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * 
     * @return object $type
     */
    public static function rightJoin($table, $first, $operator ,$second) 
    {
        static::join($table, $first, $operator, $second, "RIGHT");
        return static::instance();
    }

    /**
     * Left join table
     * 
     * @param string $table
     * @param string $first
     * @param string $operator
     * @param string $second
     * 
     * @return object $type
     */
    public static function leftJoin($table, $first, $operator ,$second) 
    {
        static::join($table, $first, $operator, $second, "LEFT");
        return static::instance();
    }

    /**
     * Where data
     * 
     * @param string $column
     * @param string $operator
     * @param string $value
     * @param string $type
     * 
     * @return object $instance
     */
    public static function where($column, $value, $operator = '=',  $type = null) 
    {
        $where = '`' .$column .'`' .$operator .' ? ';

        if (! static::$where) {
            $statement = " WHERE " .$where; 
        } else {
            if ($type == null) {
                $statement = " AND " .$where;
            } else {
                $statement = " " . $type ." " .$where;
            }
        }
        static::$where .= $statement;
        static::$whereBinding [] = htmlspecialchars($value);

        return static::instance();
    }

    /**
     * Having data
     * 
     * @param string $column
     * @param string $operator
     * @param string $value
     * 
     * @return object $instance
     */
    public static function having($column, $value, $operator = '=') 
    {
        $having = '`' .$column .'`' .$operator .' ? ';

        if (! static::$having) {
            $statement = " HAVING " .$having; 
        } else {
            $statement = " AND " .$having;
        }
        static::$having .= $statement;
        static::$havingBinding [] = htmlspecialchars($value);
        return static::instance();
    }

    /**
     * orWhere
     * 
     * @param string $column
     * @param string $operator
     * @param string $value
     * @param string $type
     * 
     * @return object $instance
     */
    public static function orWhere($column, $value, $operator = '=',  $type = null) 
    {
        static::where($column, $value, $operator, "OR");
        return static::instance();
    }

    /**
     * Group by
     *
     * @return object $instance
     */
    public function groupBy()
    {
        $groupByArgs = func_get_args();
        $groupBy = " GROUP BY " .implode(',' ,$groupByArgs);
        static::$groupBy = $groupBy;
        return static::instance();
    }

    /**
     * Order by
     *
     * @param string $column
     * @param string $type
     * @return object $instance
     */
    public function orderBy($column, $type = null)
    {
        $sep = static::$orderBy ? " , " : " ORDER BY ";
        $type = strtoupper($type);
        $type = ($type != null && in_array($type, ['ASC', 'DESC'])) ? $type : "ASC";
        $statement = $sep .$column ." " .$type ." ";

        static::$orderBy .= $statement;
    }
    
    /**
     * Limit 
     * 
     * @param int $limit
     * @return object $instance
     */
    public function limit($limit) 
    {
        static::$limit = " LIMIT " .$limit ." ";
        return static::instance();
    }

    /**
     * Offset 
     * 
     * @param string $offset
     * @return object $instance
     */
    public function offset($offset) 
    {
        static::$offset = " OFFSET " .$offset ." ";
        return static::instance();
    }

    /**
     * Fetch execute 
     * 
     * @return object $data
     */
    private static function fetchExecute() 
    {
        static::query(static::$query);
        $query = trim(static::$query, ' ');

        $data = static::$connection->prepare($query);
        
        $data->execute(static::$binding);

        static::clear();
        return $data;
    }

    /**
     * Clear all properties
     * 
     * @return void
     */
    private static function clear()
    {
        static::$select = '';
        static::$join = '';
        static::$where = '';
        static::$whereBinding = [];
        static::$groupBy = '';
        static::$having = '';
        static::$havingBinding = [];
        static::$orderBy = '';
        static::$limit = '';
        static::$offset = '';
        static::$query = '';
        static::$binding = [];
        static::$instance = '';
    }

    /**
     * Get all records
     * 
     * @return object $result
     */
    public static function get()
    {
        $data = static::fetchExecute();
        $result = $data->fetchAll();
        return $result;
    }

    /**
     * Get first record
     * 
     * @return object $result 
     */
    public static function first()
    {
        $data = static::fetchExecute();
        $result = $data->fetch();
        return $result;
    }

    /**
     * Execute add/update query
     * 
     * @param array $data
     * @param string $query
     * @param bool $where
     * 
     * @return void
     */
    public static function execute(array $data, $query, $where = null)
    {
        static::instance();
        if (! static::$table) {
            throw new Exception("Unknown table"); 
        }

        foreach($data as $key => $value) {
            static::$setter .= '`' .$key .'` = ?, ';
            static::$binding [] = filter_var($value, FILTER_SANITIZE_STRING);   
        }

        static::$setter = trim(static::$setter, ', ');
        
        $query .= static::$setter;
        $query .= $where != null ? static::$where ." " : '';

        static::$binding = $where != null ? array_merge(static::$binding, static::$whereBinding) : static::$binding;

        $data = static::$connection->prepare($query);
        $data->execute(static::$binding);

        return $data;
    }

    /**
     * Insert to table
     * 
     * @param array $data
     * @return object
     */
    public static function insert(array $data)
    {
        $table = static::$table;
        $query = "INSERT INTO " .$table ." SET ";
        static::execute($data, $query);

        static::$lastObjectId = static::$connection->lastInsertId();
        return true;
    }

    /**
     * Update table record
     * 
     * @param array data
     * @return bool
     */
    public static function update($data)
    {
        $query = "UPDATE " .static::$table ." SET ";
        static::execute($data, $query, true);
        return true;
    }

    /**
     * Delete table record
     * 
     * @param array data
     * @return bool
     */
    public static function delete()
    {
        $query = "DELETE FROM " .static::$table ." ";
        static::execute([], $query, true);
        return true;
    }

    /**
     * Pagination 
     * 
     * @return mixed $result
     */
    public static function paginate($itemsPerPage = 15)
    {
        static::query(static::$query);
        $query = trim(static::$query, ' ');
        $data  = static::$connection->prepare($query);
        $data->execute();
        $pages = ceil($data->rowCount() / $itemsPerPage);

        $page = Request::get('page');
        $currentPage = (! is_numeric($page) || Request::get('page') < 1) ? "1" : $page;
        $offset = ($currentPage - 1) * $itemsPerPage;
        static::limit($itemsPerPage);
        static::offset($offset);

        static::query();
        $data = static::fetchExecute();
        $result = $data->fetchAll();
        
        return [
            'data' => $result, 
            'itemsPerPage' => $itemsPerPage, 
            'pages' => $pages, 
            'currentPage' => $currentPage,
        ];
    }
}