<?php
session_start();

// Connection to Database
require '../../config/connection.php';


// Initialize errors in session for persistence across redirects
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

// Generate CSRF Token
if (empty($_SESSION['__csrf'])) {
    $_SESSION['__csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issSubmitted'])) {
    // Verify CSRF Token
    if (!hash_equals($_SESSION['__csrf'], $_POST['__csrf'])) {
        $_SESSION['errors'][] = 'Invalid CSRF Token';
        header("Location: " . basename(__FILE__));
        exit;
    }

    $id = filter_var(trim($_POST['id']), FILTER_VALIDATE_INT);
    $username = filter_var(trim($_POST['user_name']), FILTER_SANITIZE_SPECIAL_CHARS);
    $role = filter_var(trim($_POST['user_role']), FILTER_SANITIZE_SPECIAL_CHARS);

    // Fields Validations
    if (empty($username) || $role == 'Select Admin User Role') {
        $_SESSION['errors'][] = 'All Fields are Required';
        header("Location: " . basename(__FILE__));
        exit;
    }elseif(strlen($username) > 20 || strlen($username) < 5){
        $_SESSION['errors'][] = "Username must be between 5 and 20 characters.";
        header("Location: " . basename(__FILE__));
        exit;
    }elseif(!preg_match("/^[a-zA-Z0-9_]+$/", $username)){
        $_SESSION['errors'][] = "Username can only contain letters, numbers, and underscores.";
        header("Location: " . basename(__FILE__));
        exit;
    }
    
    // Update Admin User data into the Database
    try{
        $conn->beginTransaction();

        $stmt = $conn->prepare('UPDATE admin_user_tbl 
                                         SET user_name = :uname,
                                             user_role = :urole
                                         WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':uname', $username);
        $stmt->bindParam(':urole', $role);
        $result = $stmt->execute();

        if($result){
            $conn->commit();

            // Redirected to all Admin Page
              $_SESSION['errors'][] = "Admin User Successfully Update";
              header("Location: all_admin_user.php");
              exit;
        }
    }catch(Exception $e){
        $conn->rollBack();
              $_SESSION['errors'][] = "Error in Update " . $e->getMessage();
              header("Location: " . basename(__FILE__));
              exit;
    }
}





// Fetch Admin User with Specific Id
if(isset($_GET['id'])){
    $id = filter_var(trim($_GET['id']), FILTER_VALIDATE_INT);
    $sql = $conn->prepare('SELECT * FROM admin_user_tbl WHERE id = :id');
    $sql->bindParam(':id', $id);
    $sql->execute();
    $user = $sql->fetch();
}

// Store Error in Variable
$errors = $_SESSION['errors'] ?? [];
$_SESSION['errors'] = [];


require '../layout/header.php'

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

    <div class="card">
        <div class="card-header py-3">
            <h6 class="mb-0">Add Admin User</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-5 d-flex">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <form method="post" action="<?= htmlspecialchars(basename(__FILE__)) ?>" class="row g-3">
                                <input type="hidden" name="__csrf" value="<?= htmlspecialchars($_SESSION['__csrf']) ?>">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
                                <div class="col-12">
                                    <label class="form-label">Admin username</label>
                                    <input type="text" class="form-control" name="user_name"
                                        placeholder="Admin User name"
                                        value="<?= htmlspecialchars($user['user_name']) ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Admin User Role</label>
                                    <select class="form-select" name="user_role">
                                        <option disabled selected>Select Admin User Role</option>
                                        <option <?= ($user['user_role'] == 'author') ? 'selected' : '' ?>
                                            value="author">Author</option>
                                        <option <?= ($user['user_role'] == 'admin') ? 'selected' : '' ?> value="admin">
                                            Admin</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button name="issSubmitted" class="btn btn-dark">Update User</button>
                                        <a href="all_admin_user.php" class="mt-2 btn btn-outline-dark">Cancel</a>
                                    </div>
                                </div>
                            </form>
                            <!-- Display Errors -->
                            <?php if (!empty($errors)): ?>
                            <div class="row mt-3 justify-content-center">
                                <div class="col-md-12 col-lg-12">
                                    <?php foreach ($errors as $error): ?>
                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                        <span><?= htmlspecialchars($error) ?></span>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->
        </div>
    </div>
    </div>

</main>
<!--end page main-->
<?php require '../layout/footer.php' ?>
<?php $conn = null;  ?>