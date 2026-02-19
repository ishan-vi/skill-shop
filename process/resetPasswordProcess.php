<?php

require "../db/connection.php";

$password = $_POST["password"] ?? '';
$cpassword = $_POST["cpassword"] ?? '';
$email = $_POST["email"] ?? '';

if(empty($password) || empty($cpassword)){
    echo "Password fields are required.";
    exit;
}

if($password !== $cpassword){
    echo "Passwords do not match.";
    exit;
}

if(strlen($password) < 8){
    echo "Password must be at least 8 characters.";
    exit;
}

if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
    echo "Invalid email address.";
    exit;
}

/* Get user */
$userResult = Database::search(
    "SELECT `id` FROM `user` WHERE `email` = ?",
    "s",
    [$email]
);

if(!$userResult || $userResult->num_rows == 0){
    echo "User not found.";
    exit;
}

$user = $userResult->fetch_assoc();
$userId = $user["id"];

/* Get reset token */
$tokenResult = Database::search(
    "SELECT `token_hash`, `expiry` FROM `password_reset_tokens` WHERE `user_id` = ?",
    "i",
    [$userId]
);

if(!$tokenResult || $tokenResult->num_rows == 0){
    echo "Invalid or expired reset request.";
    exit;
}

$tokenData = $tokenResult->fetch_assoc();

/* Check expiry */
if(strtotime($tokenData["expiry"]) < time()){
    echo "Verification code expired.";
    exit;
}

/* Update new password */
$newPasswordHash = password_hash($password, PASSWORD_DEFAULT);

Database::iud(
    "UPDATE `user` SET `password_hash` = ? WHERE `id` = ?",
    "si",
    [$newPasswordHash, $userId]
);

/* Delete used token */
Database::iud(
    "DELETE FROM `password_reset_tokens` WHERE `user_id` = ?",
    "i",
    [$userId]
);

echo "success";