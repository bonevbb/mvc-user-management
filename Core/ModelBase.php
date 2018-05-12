<?php

namespace Core;

use App\Config;
use PDO;

/**
 * Base Model
 */
abstract class ModelBase
{
    /**
     * Get the PDO database connection
     *
     * @return mixed
     */
    protected static function getDB()
    {
        static $db = null;

        if ($db === null) {
                $dsn = 'firebird:dbname=' . Config::DB_HOST . ':' . Config::DB_NAME . ';charset=utf8;';
                // Connect to database
                $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);

                //Throw an Exception when an error occurs
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                return $db;
        }
    }
}