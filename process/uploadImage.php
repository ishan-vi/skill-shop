<?php
header("Content-Type:application/json");

if (!isset($_SESSION)) {
    session_start();
}
require "../db/connection.php";

if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] != true) {
    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access!"
    ]);
    exit;
}

$response = ["success" => false, "message" => "No file uploaded"];

if (isset($_FILES["avatarFile"]) && $_FILES["avatarFile"]["error"] == 0) {
    $file = $_FILES["avatarFile"];
    $fileName = $file["name"];
    $fileTmpName = $file["tmp_name"];
    $fileSize = $file["size"];
    $fileError = $file["error"];
    $userId = $_SESSION["user_id"];

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowed = ["jpg", "jpeg", "png", "gif"];

    if (in_array($fileExt, $allowed)) {
        if ($fileSize <= 5 * 1024 * 1024) {
            $newFileName = uniqid("avatar_", true) . "." . $fileExt;

            $uploadDir = "../assets/uploads/avatars/";

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpName, $uploadPath)) {
                $avatarUrl = "assets/uploads/avatars/" . $newFileName;

                $updateProfile = Database::iud(
                    "UPDATE `user_profile` SET `avatar_url` = ? WHERE `user_id` = ?",
                    "ss",
                    [$avatarUrl, $userId]
                );

                if (!$updateProfile) {
                    $newinsert = Database::iud(
                        "INSERT INTO `user_profile` (`user_id`, `avatar_url`) VALUES (?, ?)",
                        "is",
                        [$userId, $avatarUrl]
                    );

                    if (!$newinsert) {
                        throw new Exception("Failed to create user profile!");
                    }
                }

                $response = [
                    "success" => true,
                    "message" => "File uploaded successfully",
                    "avatarUrl" => $avatarUrl
                ];
            } else {
                $response = ["success" => false, "message" => "Failed to move uploaded file"];
            }
        } else {
            $response = ["success" => false, "message" => "File size too large (max 5MB)"];
        }
    } else {
        $response = ["success" => false, "message" => "Invalid file type. Only JPG, JPEG, PNG & GIF allowed"];
    }
}

echo json_encode($response);
?>