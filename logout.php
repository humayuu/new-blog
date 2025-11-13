<?php
session_start();

require 'admin/config/connection.php';

// Check if userId exists in session before using it
if (isset($_SESSION['userId'])) {
    $stmt = $conn->prepare('UPDATE user_tbl SET user_status = "0" WHERE id = :id');
    $stmt->bindParam(':id', $_SESSION['userId'], PDO::PARAM_INT);
    $stmt->execute();
}

session_unset();
session_destroy();

header('Location: login.php');
exit();