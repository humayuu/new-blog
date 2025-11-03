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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issSubmitted'])) {
    // Verify CSRF Token
    if (!hash_equals($_SESSION['__csrf'], $_POST['__csrf'])) {
        $_SESSION['errors'][] = 'Invalid CSRF Token';
        header('Location: ' . basename(__FILE__));
        exit;
    }
}















// Fetch Admin User
if(isset($_SESSION['adminId'])){
$userid = $_SESSION['adminId'];
$sql = $conn->prepare('SELECT * FROM admin_user_tbl WHERE id = :id');
$sql->bindParam(':id', $userid);
$sql->execute();
$user = $sql->fetch();
}

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
                <div class="card-body">
                    <form method="post" action="<?= htmlspecialchars(basename(__FILE__)) ?>"
                        enctype="multipart/form-data" class="row g-3">
                        <input type="hidden" name="__csrf" value="<?= htmlspecialchars($_SESSION['__csrf']) ?>">

                        <div class="col-12">
                            <label class="form-label">Admin Username</label>
                            <input type="text" class="form-control" name="user_name" placeholder="Enter username"
                                value="<?= htmlspecialchars($user['user_name']) ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password"
                                placeholder="Enter Current password" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Enter New password"
                                required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password"
                                placeholder="Enter Confirm password" required>
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
                </div>
            </div>
        </div>
    </div>
    </div>

</main>
<!--end page main-->
<?php require '../layout/footer.php' ?>
<?php $conn = null;  ?>