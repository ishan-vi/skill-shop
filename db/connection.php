<?php

class Database
{

    private static $connection;
    public static function getConnection()
    {
        if (!isset(self::$connection)) {
            self::$connection = new mysqli("localhost", "root", "", "skill-shop", 3306);

            if (self::$connection->connect_error) {
                die("Connection failed:  " . self::$connection->connect_error);
            }
        }
        return self::$connection;
    }

    public static function iud($query, $types, $params)
    {
        $conn = self::getConnection();
        $statement = $conn->prepare($query);
        $statement->bind_param($types, ...$params);
        $result = $statement->execute();
        $statement->close();
        return $result;
    }

    public static function search($query, $types = null, $params = [])
    {
        $conn = self::getConnection();
        $statement = $conn->prepare($query);

        if ($statement === false) {
            die("Prepare failed");
        }

        if ($types != null && !empty($params)) {
            $statement->bind_param($types, ...$params);
        }

        $executeResult = $statement->execute();

        if ($executeResult === false) {
            die("Execute failed: " . $statement->error);
        }

        $result = $statement->get_result();
        $statement->close();
        return $result;
    }
}
?>