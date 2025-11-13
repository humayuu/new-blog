<?php
session_start();

// Connection to Database
require '../../config/connection.php';

// Initialize errors
if (isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}


if(isset($_GET['id'])){
    $id = htmlspecialchars($_GET['id']);
    $sql = $conn->prepare('SELECT * FROM user_tbl WHERE id = :id');
    $sql->bindParam(':id', $id);
    $sql->execute();
    $user = $sql->fetch();
    
}
$newStatus = ($user['user_admin_status'] == 'Inactive') ? 'Active' : 'Inactive';

try{

    $stmt = $conn->prepare('UPDATE user_tbl SET user_admin_status = :newStatus WHERE id = :id');
    $stmt->bindParam(':newStatus', $newStatus);
    $stmt->bindParam(':id', $id);
    $result = $stmt->execute();

    if($result){
        $_SESSION['errors'][] = 'User Status Updated Successfully';
        header('Location: all_user.php');
        exit;
    }

}catch(Exception $e){
        $_SESSION['errors'][] = 'Error in update user status ' . $e->getMessage();
        header('Location: all_user.php');
        exit;
}