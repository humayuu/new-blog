<?php
session_start();

// Connection to Database
require '../../config/connection.php';


// Generate CSRF Token
if (empty($_SESSION['__csrf'])) {
    $_SESSION['__csrf'] = bin2hex(random_bytes(32));
}

// Initialize errors in session for persistence across redirects
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}



// Fetch Admin User
if (isset($_SESSION['adminId'])) {
    $userid = $_SESSION['adminId'];
    $sql = $conn->prepare('SELECT * FROM admin_user_tbl WHERE id = :id');
    $sql->bindParam(':id', $userid);
    $sql->execute();
    $user = $sql->fetch();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issSubmitted'])) {
    // Verify CSRF Token
    if (!hash_equals($_SESSION['__csrf'], $_POST['__csrf'])) {
        $_SESSION['errors'][] = 'Invalid CSRF Token';
        header('Location: ' . basename(__FILE__));
        exit;
    }


    $id = filter_var(trim($_POST['id']), FILTER_VALIDATE_INT);
    $userName = filter_var(trim($_POST['user_name']), FILTER_SANITIZE_SPECIAL_CHARS);
    $currentPassword = filter_var(trim($_POST['current_password']));
    $password = filter_var(trim($_POST['password']));
    $confirmPassword = filter_var(trim($_POST['confirm_password']));
    $oldImage = $user['user_image'];


    $newPassword = null;
    $newImage = null;
    $allowedExtension = ['jpeg', 'jpg', 'png'];
    $MaxFileSize = 2 * 1024 * 1024;
    $UploadDir = __DIR__ . '/uploads/admin_user/';

    if (!is_dir($UploadDir)) {
        mkdir($UploadDir, 0755, true);
    }

    // Validation
    if (empty($userName)) {
        $_SESSION['errors'][] = 'Username is Required';
        header('Location: ' . basename(__FILE__));
        exit;
    }

    // If user wants to change password
    if (!empty($currentPassword)) {
        // Verify current password first
        if (!password_verify($currentPassword, $user['user_password'])) {
            $_SESSION['errors'][] = 'Current Password is incorrect';
            header('Location: ' . basename(__FILE__));
            exit;
        }

        // Check if new passwords match
        if ($password !== $confirmPassword) {
            $_SESSION['errors'][] = 'Password and Confirm Password Must Match';
            header('Location: ' . basename(__FILE__));
            exit;
        }

        // Check if new password is not empty
        if (empty($password)) {
            $_SESSION['errors'][] = 'New Password cannot be empty';
            header('Location: ' . basename(__FILE__));
            exit;
        }

        // Hash the new password
        $newPassword = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // Keep existing password if not changing
        $newPassword = $user['user_password'];
    }

    // If New Image
    if (isset($_FILES['image']) && $_FILES['image']['error'] ===  UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['image']['size'];
        $tmpName = $_FILES['image']['tmp_name'];

        if (!in_array($ext, $allowedExtension)) {
            $_SESSION['errors'][] = 'Invalid image extension';
            header('Location: ' . basename(__FILE__));
            exit;
        }

        if ($size > $MaxFileSize) {
            $_SESSION['errors'][] = 'Max file size is 2 MB';
            header('Location: ' . basename(__FILE__));
            exit;
        }

        $img = uniqid('user_') . time() . '.' . $ext;

        if (!move_uploaded_file($tmpName, $UploadDir . $img)) {
            $_SESSION['errors'][] = 'User image Upload Error';
            header('Location: ' . basename(__FILE__));
            exit;
        }

        $newUserImage = 'uploads/admin_user/' . $img;
    }

    if (!empty($newUserImage)) {
        $newImage = $newUserImage;
    } else {
        $newImage = $user['user_image'];
    }

    // Update Data into the Database
    try {

        $conn->beginTransaction();

        $stmt = $conn->prepare('UPDATE admin_user_tbl
                                        SET   user_name = :uname,
                                              user_password = :upassword,
                                              user_image  = :uimage
                                        WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':uname', $userName);
        $stmt->bindParam(':upassword', $newPassword);
        $stmt->bindParam(':uimage', $newImage);
        $result = $stmt->execute();

        if ($result) {
            // Unlink Old Image
            if($newImage !== $oldImage && file_exists($oldImage)){
                unlink($oldImage);
            }
            $conn->commit();

        // Redirected to the Same Page
        $_SESSION['errors'][] = 'Profile Update Successfully';
        header('Location: ' . basename(__FILE__));
        exit;
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['errors'][] = 'User Data Update Error ' . $e->getMessage();
        header('Location: ' . basename(__FILE__));
        exit;
    }
}


// Store Error in Variable
$errors = $_SESSION['errors'] ?? [];
$_SESSION['errors'] = [];




require '../layout/header.php';

?>
<!--start content-->
<main class="page-content">

    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Ratnews</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Admin User</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row justify-content-center">
        <div class="col-12 col-lg-6 col-xl-5">
            <div class="card">
                <div class="card-header py-3">
                    <h6 class="mb-0 text-center fs-2">Admin User Profile</h6>
                </div>
                <!-- Display Errors -->
                <?php if (!empty($errors)): ?>
                <div class="mt-3">
                    <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bx bx-error-circle me-2"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <form method="post" action="<?= htmlspecialchars(basename(__FILE__)) ?>"
                        enctype="multipart/form-data" class="row g-3">
                        <input type="hidden" name="__csrf" value="<?= htmlspecialchars($_SESSION['__csrf']) ?>">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">

                        <div class="col-12">
                            <label class="form-label">Admin Username</label>
                            <input type="text" class="form-control" name="user_name" placeholder="Enter username"
                                value="<?= htmlspecialchars($user['user_name']) ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password"
                                placeholder="Enter Current password">
                        </div>

                        <div class="col-12">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="password"
                                placeholder="Enter New password">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password"
                                placeholder="Enter Confirm password">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Admin User Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <img class="img-thumbnail" src="<?= htmlspecialchars($user['user_image']) ?>" width="100"
                                alt="">
                        </div>

                        <div class="col-12">
                            <div class="d-grid">
                                <button name="issSubmitted" class="btn btn-primary">Save Changes</button>
                                <a href="../category/dashboard.php" class="mt-2 btn btn-outline-dark">Cancel</a>

                            </div>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
    </div>

</main>
<!--end page main-->
<?php require '../layout/footer.php' ?>
<?php $conn = null;  ?>