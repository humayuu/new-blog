<?php
session_start();

// Connection to Database
require '../../config/connection.php';


// Initialize errors in session for persistence across redirects
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

$id = filter_var(trim($_GET['id']), FILTER_VALIDATE_INT);
// Fetch Old User Status
$sql = $conn->prepare('SELECT * FROM admin_user_tbl WHERE id = :id');
$sql->bindParam(':id', $id);
$sql->execute();
$user = $sql->fetch();
$OldStatus = $user['user_status'];

$newStatus = ($OldStatus == 'Active') ? 'Inactive' : 'Active';


try {
    $conn->beginTransaction();

    $stmt = $conn->prepare('UPDATE admin_user_tbl SET user_status = :ustatus WHERE id = :userid');
    $stmt->bindParam(':userid', $id);
    $stmt->bindParam(':ustatus', $newStatus);
    $result = $stmt->execute();

    if ($result) {
        $conn->commit();

        // Redirected to all Admin Page
        $_SESSION['errors'][] = "Admin User Status Successfully Update";
        header("Location: all_admin_user.php");
        exit;
    }
} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['errors'][] = "Error in Update Status " . $e->getMessage();
    header("Location: all_admin_user.php");
    exit;
}