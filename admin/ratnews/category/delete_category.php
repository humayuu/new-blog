<?php
session_start();

// Connection to Database
require '../../config/connection.php';


// Initialize errors in session for persistence across redirects
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

$id = filter_var(trim($_GET['id']), FILTER_VALIDATE_INT);

// Delete Category Data from database
try {
    $conn->beginTransaction();

    $stmt = $conn->prepare('DELETE FROM category_tbl WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $result = $stmt->execute();

    if ($result) {
        $conn->commit();
        // Redirect to manage Category Page
        $_SESSION['errors'][] = 'Category Successfully Delete.';
        header('Location: all_category.php');
        exit;
    }
} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['errors'][] = 'Error in Delete Category to database ' . $e->getMessage();
    header('Location: all_category.php');
    exit;
}