<?php
session_start();
require "../db/connection.php";

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

setcookie("skillshop_remember", "", time() - 3600, "/");
setcookie("skillshop_user_id", "", time() - 3600, "/");
setcookie("skillshop_user_email", "", time() - 3600, "/");

header("Location: ../index.php");
exit();
?>