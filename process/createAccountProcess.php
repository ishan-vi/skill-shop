<?php

require "../db/connection.php";

$fname = $_POST["fname"];
$lname = $_POST["lname"];
$email = $_POST["email"];
$password = $_POST["password"];
$passConfirm = $_POST["pass_confirm"];
$accountType = $_POST["account_type"];
$termsCondition = $_POST["termsCondition"];

if (empty($fname)) {
    echo "Please enter your First Name";
} else if (empty($lname)) {
    echo "Please enter your Last Name";
} else if (empty($email)) {
    echo "Please enter your Email";
} else if (strlen($email) >= 150) {
    echo "Please must be less than 150 characters";
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid Email Address";
} else if (empty($password)) {
    echo "Please enter your Password";
} else if ($password != $passConfirm) {
    echo "Password do not match";
} else if (strlen($password) < 8 || strlen($password) > 20) {
    echo "Please length shuld be between 8 to 20";
} else if (!$termsCondition) {
    echo "Please read and check I agree to the Terms & Conditions";
} else if (empty($accountType)) {
    echo "Please select account type";
} else {
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    //CHECK ACCOUNT TYPE FROM DB
    $result = Database::search("SELECT `id` FROM `account_type` WHERE `name`=?", "s", [$accountType]);
    if ($result && $row = $result->fetch_assoc()) {
        $accountType = $row["id"];
    } else {
        echo "Invalid account type";
        exit;
    }

    //CHECK EMAIL
    $check = Database::search("SELECT `id` FROM `user` WHERE `email`=?", "s", [$email]);
    if ($check && $check->num_rows > 0) {
        echo "Email is Alredy Registerd.";
    } else {
        //INSERT USER IN DB
        $insertUser = Database::iud(
            "INSERT INTO `user` (`fname`,`lname`,`email`,`password_hash`,`active_account_type_id`) VALUES (?,?,?,?,?)",
            "ssssi",
            [$fname, $lname, $email, $passwordHash, $accountType]
        );

        if ($insertUser) {
            //GET NEW USER ID
            $user_id = Database::getConnection()->insert_id;

            $insertRole = Database::iud(
                "INSERT INTO `user_has_account_type` (`user_id`,`account_type_id`) VALUES (?,?)",
                "ii",
                [$user_id, $accountType]
            );
            echo ($insertRole ? "success" : "Error assigning account type");
        } else {
            echo "Error Creating user account";
        }
    }
}
