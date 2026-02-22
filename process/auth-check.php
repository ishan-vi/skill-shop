<?php

if (isset($_COOKIE["skillshop_remember"]) && isset($_COOKIE["skillshop_user_id"])) {
    
    $rememberToken = $_COOKIE["skillshop_remember"];
    $userID = $_COOKIE["skillshop_user_id"];

    $tokenResult = Database::search("SELECT * FROM `remember_tokens` WHERE `user_id`=? AND `token_hash`=?", "is", [$userID, $rememberToken]);

    if ($tokenResult && $tokenResult->num_rows > 0) {
        $tokenRecord = $tokenResult->fetch_assoc();

        if (password_verify($rememberToken, $tokenRecord["token_hash"])) {

            $userResult = Database::search(
                "SELECT u.`id`, u.`fname`, u.`lname`, u.`email`, u.`active_account_type_id`, at.`name` 
                 FROM `user` u 
                 JOIN `account_type` at ON u.`active_account_type_id` = at.`id` 
                 WHERE u.`id`=?", 
                "i", 
                [$userID]
            );

            if ($userResult && $user = $userResult->fetch_assoc()) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["fname"] . " " . $user["lname"];
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["account_type_id"] = $user["active_account_type_id"];
                $_SESSION["active_account_type"] = $user["name"];
                $_SESSION["logged_in"] = true;

                Database::iud(
                    "DELETE FROM `remember_tokens` WHERE `user_id`=? AND `token_hash` != ?", 
                    "is", 
                    [$userID, $tokenRecord["token_hash"]]
                );

                return true;
            }
        }
    }

    setcookie("skillshop_remember", "", time() - 3600, "/");
    setcookie("skillshop_user_id", "", time() - 3600, "/");
    setcookie("skillshop_user_email", "", time() - 3600, "/");
}

?>