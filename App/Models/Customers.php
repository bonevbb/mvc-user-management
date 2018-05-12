<?php


namespace App\Models;

use Core\ModelBase;
use PDO;
use PDOException;

class Customers extends ModelBase
{
    public static function getAll()
    {
        try {
            // Connect to database
            $dbh = static::getDB();

            // Execute query
            $query = $dbh->query('SELECT ID, C_NAME FROM T_CUSTOMERS ORDER BY ID');

            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            return $results;

        } catch (PDOException $e) {
            echo $e->getMessage();
        }

    }
}