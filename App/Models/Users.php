<?php

namespace App\Models;

use Core\ModelBase;
use PDO;

class Users extends ModelBase
{
    private static $pageLimit;

    /**
     * Get all users
     *
     * @return \stdClass
     */
    public static function getAllWithPagination()
    {
        try {
            // Connect to database
            $dbh = static::getDB();

            self::$pageLimit = 5;
            $page = (isset($_GET['page'])) ? $_GET['page'] : 0;

            $skip = $page !== 0 ? ($page -1) : 0;

            $query = $dbh->query("SELECT FIRST " . self::$pageLimit . " SKIP " . ($skip * self::$pageLimit) . " ID, USER_NAME, USER_REALNAME, USER_EMAIL, USER_PHONE FROM T_USERS ORDER BY ID DESC");

            $results = $query->fetchAll(PDO::FETCH_ASSOC);

            return $results;

        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Create new user
     *
     * @param array $post Information from insert form
     */
    public static function create($post)
    {
        try {
            $dbh = static::getDB();

            $newUserId = self::generateUserID($dbh);

            $statement = $dbh->prepare("INSERT INTO T_USERS (ID, USER_NAME, USER_REALNAME, USER_EMAIL, USER_PHONE, ROW_NO, USER_PASS, IS_ADMIN, IS_DOPINFO, IS_CUST) VALUES (:id, :username, :realName, :email, :phone, :numRow, :userPass, :isAdmin, :isDopInfo, :isCust);");
            $statement->execute(array(
                "id" => $newUserId,
                "username" => $post['username'],
                "realName" => $post['name'],
                "email" => $post['email'],
                "phone" => $post['phone'],
                "numRow" => 0,
                "userPass" => md5($post['password']),
                "isAdmin" => isset($post['is_admin']) ? 1 : 0,
                "isDopInfo" => isset($post['is_dopInfo']) ? 1 : 0,
                "isCust" => isset($post['is_cust']) ? 1 : 0,
            ));

            if (isset($post['is_cust'])) {
                self::addUserToCustomers($dbh, $newUserId, $post['customer']);
            }
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }

    }

    /**
     * Get new id for user
     *
     * @param PDO $dbh Database connection
     * @return object Return user id for autoincrement
     */
    public static function generateUserID($dbh)
    {
        $sql = 'SELECT GEN_ID(GEN_USERS,1) AS NEW_ID FROM RDB$DATABASE ';

        $query = $dbh->query($sql);
        $user = $query->fetchObject();

        return $user->NEW_ID;
    }

    /**
     * Add user to customers
     *
     * @param PDO $dbh
     * @param int $newUserId The user id
     * @param int $customerId The selected customer id
     */
    private static function addUserToCustomers($dbh, $newUserId, $customerId)
    {
        $statement = $dbh->prepare("INSERT INTO T_USER_TO_CUSTOMERS (ID_USER, ID_CUSTOMER) VALUES (:idUser, :idCustomer);");
        $statement->execute(array(
            "idUser" => $newUserId,
            "idCustomer" => $customerId,
        ));
    }

    /**
     * Delete user by ID
     * @param int $userID The user Id
     */
    public static function delete($userID)
    {
        try {
            $dbh = static::getDB();

            self::deleteUserToCustomer($dbh, $userID);

            $statement = $dbh->prepare("DELETE FROM  T_USERS WHERE ID = :idUser;");
            $statement->execute(array(
                "idUser" => $userID,
            ));
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Delete user form table T_USER_TO_CUSTOMERS
     *
     * @param PDO $dbh
     * @param int $userID
     */
    private static function deleteUserToCustomer($dbh, $userID)
    {
        $statement = $dbh->prepare("DELETE FROM  T_USER_TO_CUSTOMERS WHERE ID_USER = :idUser;");
        $statement->execute(array(
            "idUser" => $userID,
        ));
    }

    public static function getUserById($userId)
    {
        try {
            $dbh = static::getDB();

            $query = $dbh->query('SELECT ID, USER_NAME, USER_REALNAME, USER_EMAIL, USER_PHONE, IS_ADMIN, IS_DOPINFO, IS_CUST FROM T_USERS WHERE ID = ' . $userId);

            $result = $query->fetchObject();

            if ($result->IS_CUST === "1") {
                $customer = self::getCustomer($userId, $dbh);
                $result->ID_CUSTOMER = $customer->ID_CUSTOMER;
            }

            return $result;

        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Get customer ID
     *
     * @param $userId
     * @param $dbh
     * @return mixed
     */
    public static function getCustomer($userId, $dbh)
    {
        $queryCustomer = $dbh->query('SELECT ID_CUSTOMER FROM T_USER_TO_CUSTOMERS WHERE ID_USER = ' . $userId);
        $customer = $queryCustomer->fetchObject();

        return $customer;
    }

    /**
     * Create new user
     *
     * @param array $post Information from insert form
     */
    public static function update($post)
    {
        try {
            $dbh = static::getDB();

            $statement = $dbh->prepare("UPDATE T_USERS SET USER_NAME = :username, USER_REALNAME = :realName, USER_EMAIL = :email, USER_PHONE = :phone, IS_ADMIN = :isAdmin, IS_DOPINFO = :isDopInfo, IS_CUST = :isCust WHERE ID = :userId");

            $statement->execute(array(
                "username" => $post['username'],
                "realName" => $post['name'],
                "email" => $post['email'],
                "phone" => $post['phone'],
                "isAdmin" => isset($post['is_admin']) ? 1 : 0,
                "isDopInfo" => isset($post['is_dopInfo']) ? 1 : 0,
                "isCust" => isset($post['is_cust']) ? 1 : 0,
                "userId" => $post['user_id']
            ));

            if (isset($post['is_cust'])) {
                self::updateOrCreateUserToCustomers($dbh, $post['user_id'], $post['customer']);
            } else {
                self::removeUserFromCustomers($dbh, $post['user_id']);
            }

        } catch (\PDOException $e) {
            echo $e->getMessage();
        }

    }

    /**
     * Update or add new record with user to customers
     *
     * @param $dbh
     * @param $userId
     * @param $customerId
     */
    private static function updateOrCreateUserToCustomers($dbh, $userId, $customerId)
    {
        $customer = self::getCustomer($userId, $dbh);

        if ($customer) {
            $statement = $dbh->prepare("UPDATE T_USER_TO_CUSTOMERS SET ID_CUSTOMER = :idCustomer WHERE ID_USER = :idUser;");
            $statement->execute(array(
                "idCustomer" => $customerId,
                "idUser" => $userId,
            ));
        } else {
            self::addUserToCustomers($dbh, $userId, $customerId);
        }
    }

    /**
     * If exists record, deletes it.
     *
     * @param PDO $dbh
     * @param string $userId
     * @return bool
     */
    private static function removeUserFromCustomers($dbh, $userId)
    {
        $customer = self::getCustomer($userId, $dbh);

        if (!$customer) {
            return false;
        }

        self::deleteUserToCustomer($dbh, $userId);
    }

    public static function getPageLimit()
    {
        return self::$pageLimit;
    }
}