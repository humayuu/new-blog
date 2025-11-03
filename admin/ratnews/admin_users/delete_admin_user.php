<?php
session_start();

// Connection to Database
require '../../config/connection.php';

// Initialize errors in session for persistence across redirects
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

$id = filter_var(trim($_GET['id']), FILTER_VALIDATE_INT);

// First Fetch image for unlink
$sql = $conn->prepare('SELECT * FROM admin_user_tbl WHERE id = :id');
$sql->bindParam(':id', $id);
$sql->execute();
$user = $sql->fetch();
$image = $user['user_image'];

try {

    $conn->beginTransaction();

    $stmt = $conn->prepare('DELETE FROM admin_user_tbl WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $result = $stmt->execute();

    if ($result) {
        $conn->commit();

        if (!empty($image) && file_exists($image)) {
            unlink($image);
        }

        // Redirected to All Admin User page
        header('Location: all_admin_user.php');
        $_SESSION['errors'][] = 'Admin User Successfully Deleted';
        exit;
    }
} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['errors'][] = "Error in insert " . $e->getMessage();
    header('Location: all_admin_user.php');
    exit;
}