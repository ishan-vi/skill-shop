<?php

class Database
{
    private static $connection;
    
    public static function getConnection()
    {
        if (!isset(self::$connection)) {
            self::$connection = new mysqli("localhost", "root", "", "skill-shop", 3306);

            if (self::$connection->connect_error) {
                die("Connection failed: " . self::$connection->connect_error);
            }
            
            // Set charset to handle UTF-8 properly
            self::$connection->set_charset("utf8mb4");
        }
        return self::$connection;
    }

    public static function iud($query, $types, $params)
    {
        $conn = self::getConnection();
        $statement = $conn->prepare($query);
        
        if ($statement === false) {
            die("Prepare failed for query: " . $query . " - Error: " . $conn->error);
        }
        
        $statement->bind_param($types, ...$params);
        $result = $statement->execute();
        
        if ($result === false) {
            die("Execute failed: " . $statement->error);
        }
        
        $statement->close();
        return $result;
    }

    public static function search($query, $types = null, $params = [])
    {
        $conn = self::getConnection();
        
        // Debug: Log the query being executed
        error_log("Executing query: " . $query);
        
        $statement = $conn->prepare($query);

        if ($statement === false) {
            // Provide detailed error information
            $error_message = "Prepare failed for query: " . $query . "\n";
            $error_message .= "MySQL Error: " . $conn->error . "\n";
            $error_message .= "Error Code: " . $conn->errno;
            
            // You can also check for specific error conditions
            if ($conn->errno == 1146) {
                $error_message .= "\nTable doesn't exist!";
            } elseif ($conn->errno == 1054) {
                $error_message .= "\nUnknown column in query!";
            } elseif ($conn->errno == 1064) {
                $error_message .= "\nSQL syntax error!";
            }
            
            die($error_message);
        }

        if ($types !== null && !empty($params)) {
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
    
    // Optional: Add a method to test the connection
    public static function testConnection()
    {
        $conn = self::getConnection();
        echo "Connected successfully to database!<br>";
        echo "Server info: " . $conn->server_info . "<br>";
        echo "Character set: " . $conn->character_set_name() . "<br>";
    }
}

?>