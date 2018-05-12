<?php

namespace Core;


use PDO;
use stdClass;

class Paginator extends ModelBase
{
    public static function getPages($limit)
    {
        $pages = [];
        try {
            // Connect to database
            $dbh = static::getDB();

            $users = $dbh->query('SELECT ID FROM T_USERS');

            $allRecords = count($users->fetchAll(PDO::FETCH_ASSOC));
            $count = ceil($allRecords / $limit);

            for($i = 1; $i <= $count; $i++){
                $pages[$i] = true;
            }

            return $pages;

        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }
}