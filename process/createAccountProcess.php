<?php 

    require "../db/connection.php";

    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passConfirm = $_POST["pass_confirm"];
    $accountType = $_POST["account_type"];
    $termsConditions = $_POST["termsConditions"];

    if(empty($fname)){
        echo  "Please enter the First Name.";
    } elseif (empty($lname)){
        echo "Please enter the Last Name";
    }elseif (empty($email)){
        echo "Please enter the Email Address";
    }else if(strlen($email) >= 150){
        echo "Email must be less than 150 characters";
    }else if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        echo "Invalid Email Address";
    }else if(empty($password)){
        echo "Please enter the Password";
    }else if($password != $passConfirm){
        echo "Password do not match";
    }else if(strlen($password) <8 || strlen($password) > 20){
        echo "Password length should be between 8 and 20.";
    }else if(!$termsConditions){
        echo "Please read and check I agre to the Terms & Conditions";
    }else if(empty($accountType)){
        echo "Please select account type.";
    }else{

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        //Check Account type from Database
        $result = Database::search("SELECT `id` FROM `account_type` WHERE `name` = ?","s",[$accountType]);
        if($result && $row = $result->fetch_assoc()){
            $accountTypeId = $row["id"];
        }else{
            echo "Invalid account type.";
            exit;
        }

        //Check Email Already Exixts
        $check = Database::search("SELECT `id` FROM `user` WHERE `email` =?","s",[$email]);
        if($check && $check->num_rows >0){
            echo "Email is Already Registered.";
            exit;
        }else{
            //Insert User into Database user Table
            $insertUser = Database::iud(
                "INSERT INTO `user`(`fname`,`lname`,`email`,`password_hash`,`active_account_type_id`)VALUE (?,?,?,?,?)",
                "ssssi",
                [$fname,$lname,$email,$passwordHash,$accountTypeId]
            );

            if($insertUser){
                $user_id = Database::getConnection()->insert_id;
                $insertRole = Database::iud(
                    "INSERT INTO `user_has_account_type` (`user_id`,`account_type_id`) VALUES (?,?)",
                    "ii",
                    [$user_id,$accountTypeId]
                );
                echo ($insertRole ? "success" : "Error assigning account type.");
            }else{
                echo "Error creating user account";
            }
        }

    }


?>