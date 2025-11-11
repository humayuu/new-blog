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
$sql = $conn->prepare('SELECT * FROM post_tbl WHERE id = :id');
$sql->bindParam(':id', $id);
$sql->execute();
$post = $sql->fetch();
$image = $post['post_image'];

try {

    $conn->beginTransaction();

    $stmt = $conn->prepare('DELETE FROM post_tbl WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $result = $stmt->execute();

    if ($result) {
        $conn->commit();

        if (!empty($image) && file_exists($image)) {
            unlink($image);
        }

        // Redirected to All Post page
        header('Location: view_all_post.php');
        $_SESSION['errors'][] = 'Admin User Successfully Deleted';
        exit;
    }
} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['errors'][] = "Error in Delete " . $e->getMessage();
    header('Location: view_all_post.php');
    exit;
}