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
    
    $stmt = $conn->prepare('DELETE FROM comments_tbl WHERE id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $_SESSION['errors'][] = 'Comment Delete Update Successfully';
    header('Location: all_comments.php' );
    exit;

}catch(Exception $e){
    $_SESSION['errors'][] = 'Error in delete comment status ' . $e->getMessage();
    header('Location: all_comments.php' );
    exit;
}