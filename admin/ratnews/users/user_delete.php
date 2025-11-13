<?php
session_start();

// Connection to Database
require '../../config/connection.php';

// Initialize errors in session for persistence across redirects
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

$id = filter_var(trim($_GET['id']), FILTER_VALIDATE_INT);

try {

    $conn->beginTransaction();

    $stmt = $conn->prepare('DELETE FROM user_tbl WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $result = $stmt->execute();

    if ($result) {
        $conn->commit();
        // Redirected to All Admin User page
        header('Location: all_user.php');
        $_SESSION['errors'][] = 'Admin User Successfully Deleted';
        exit;
    }
} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['errors'][] = "Error in delete " . $e->getMessage();
    header('Location: all_admin_user.php');
    exit;
}