<?php 

session_start();

// Connection to Database
require '../../config/connection.php';

// Initialize errors
if (isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

$id = htmlspecialchars($_GET['id']);

try{
    
    $stmt = $conn->prepare('UPDATE comments_tbl SET comment_status = "1" WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $_SESSION['errors'][] = 'Comment Status Update Successfully';
    header('Location: all_comments.php' );
    exit;

}catch(Exception $e){
    $_SESSION['errors'][] = 'Error in update comment status ' . $e->getMessage();
    header('Location: all_comments.php' );
    exit;
}