<?php
namespace bianky\File;

class File {
    
    /**
     * Root path 
     * 
     * @return string 
     */
    public static function root()
    {
        return ROOT;
    }

    /**
     * Directory separator
     * 
     * @return string 
     */
    public static function ds()
    {
        return DS;
    }

    /**
     * Get file path.
     * 
     * @param string $path
     * @return string $path
     */
    public static function path($path)
    {
        $path = static::root() .static::ds() .trim($path, '/');
        return str_replace(['/', '\\'], static::ds(), $path);
    }

    /**
     * Check if the file path is exists 
     * 
     * @param string $path
     * @return bool
     */
    public static function exist($path)
    {
        return file_exists(static::path($path));
    }

    /**
     * Require file
     * 
     * @param string $path
     * @return mixed 
     */
    public static function requireFile($path)
    {
        if (static::exist($path)) {
            return require_once static::path($path);
        }
    }
        
    /**
     * Include file
     * 
     * @param string $path
     * @return mixed 
     */
    public static function includeFile($path)
    {
        if (static::exist($path)) {
            return include static::path($path);
        }
    }

    /**
     * Require directory
     * 
     * @param string $path
     * @return mixed
     */
    public static function requireDirectory($path)
    {   
        $files = array_diff(scandir(static::path($path)) ,['.', '..']);
        
        foreach ($files as $file)
        {
            $filePath = $path .static::ds(). $file;
            static::requireFile($filePath);
        }
    }
}