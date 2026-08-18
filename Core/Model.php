<?php

namespace Core;

use PDO;
use App\Config;

/**
 * Base model
 *
 * PHP version 8.2.12
 */
abstract class Model
{
    public $user_id;
    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB()
    {
        static $db = null;

        if ($db === null) {

            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' .
                Config::DB_NAME . ';charset=utf8mb4';
            $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);
            // Throw an Exception when an Error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $db;
    }

    /**
     * Generate unique keys
     * 
     */
    public function randString($length = 8)
    {
        $characters = '0123456789';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
}
