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

    $username = filter_var(trim($_POST['user_name']), FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_var(trim($_POST['password']));
    $confirmPassword = filter_var(trim($_POST['confirm_password']));
    $role = filter_var(trim($_POST['user_role']), FILTER_SANITIZE_SPECIAL_CHARS);
    $status = 'Active';
    $image = null;
    $allowedExtension = ['jpg', 'png', 'jpeg'];
    $MaxFileSize = 2 * 1024 * 1024; // 2MB
    $UploadDir = __DIR__ . '/uploads/admin_user/';

    // Create a Directory if its not Exists
    if (!is_dir($UploadDir)) {
        mkdir($UploadDir, 0755, true);
    }

    // Fields Validations
    if (empty($username) || empty($password) || empty($confirmPassword) || $role == 'Select Admin User Role') {
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
    }elseif(strlen($password) < 8){
        $_SESSION['errors'][] = "Password must be in 8 Character";
        header("Location: " . basename(__FILE__));
        exit;
    }elseif($password !== $confirmPassword){
        $_SESSION['errors'][] = "Password and Confirm Password must be Matched";
        header("Location: " . basename(__FILE__));
        exit;
    }

    // Image Upload
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['image']['size'];
        $tmpName = $_FILES['image']['tmp_name'];

        if(!in_array($ext, $allowedExtension)){
              $_SESSION['errors'][] = "Extension is not allowed";
              header("Location: " . basename(__FILE__));
              exit;
        }

        if($size > $MaxFileSize){
              $_SESSION['errors'][] = "Max File size is 2 MB";
              header("Location: " . basename(__FILE__));
              exit;
        }

        $newName = uniqid('user_') . time() . '_' . '.' . $ext;

        if(!move_uploaded_file($tmpName, $UploadDir . $newName)){
              $_SESSION['errors'][] = "User Image Upload Error";
              header("Location: " . basename(__FILE__));
              exit;
        }
        
        $image = 'uploads/admin_user/' . $newName;
    }

    // Hash Admin User Password
    $hashPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert Admin User data into the Database
    try{
        $conn->beginTransaction();

        $stmt = $conn->prepare('INSERT INTO admin_user_tbl (user_name, user_password, user_image, user_status, user_role) VALUES (:uname, :upassword, :uimage, :ustatus, :urole)');
        $stmt->bindParam(':uname', $username);
        $stmt->bindParam(':upassword', $hashPassword);
        $stmt->bindParam(':uimage', $image);
        $stmt->bindParam(':ustatus', $status);
        $stmt->bindParam(':urole', $role);
        $result = $stmt->execute();

        if($result){
            $conn->commit();

            // Redirected to all Admin Page
              $_SESSION['errors'][] = "Admin User Successfully Add";
              header("Location: " . basename(__FILE__));
              exit;
        }
    }catch(Exception $e){
        $conn->rollBack();
              $_SESSION['errors'][] = "Error in insert " . $e->getMessage();
              header("Location: " . basename(__FILE__));
              exit;
    }
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
                <div class="col-12 col-lg-4 d-flex">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <form method="post" action="<?= htmlspecialchars(basename(__FILE__)) ?>"
                                enctype="multipart/form-data" class="row g-3">
                                <input type="hidden" name="__csrf" value="<?= htmlspecialchars($_SESSION['__csrf']) ?>">
                                <div class="col-12">
                                    <label class="form-label">Admin username</label>
                                    <input type="text" class="form-control" name="user_name"
                                        placeholder="Admin User name">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password"
                                        placeholder="Admin User Password">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="confirm_password"
                                        placeholder="Admin User Confirm Password">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Admin User Role</label>
                                    <select class="form-select" name="user_role" id="">
                                        <option disabled selected>Select Admin User Role</option>
                                        <option value="author">Author</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Admin User Image</label>
                                    <input type="file" class="form-control" name="image" placeholder="Admin User Image">
                                </div>
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button name="issSubmitted" class="btn btn-primary">Add User</button>
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
                <?php 
                // Fetch All Data
                $sl = 1;
                $sql = $conn->prepare('SELECT * FROM admin_user_tbl ORDER BY user_name DESC');
                $sql->execute();
                $adminUsers = $sql->fetchAll();
                ?>
                <div class="col-12 col-lg-8 d-flex">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php if($adminUsers): ?>
                                <table class="table align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Username</th>
                                            <th>User Image</th>
                                            <th>User Status</th>
                                            <th>User Role</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($adminUsers as $user): ?>
                                        <tr>
                                            <td><?= $sl++ ?></td>
                                            <td><?= htmlspecialchars($user['user_name']) ?></td>
                                            <td><img class="img-thumbnail" width="100"
                                                    src="<?= htmlspecialchars($user['user_image']) ?>" alt=""></td>
                                            <td><?= htmlspecialchars($user['user_status']) ?></td>
                                            <td><?= htmlspecialchars($user['user_role']) ?></td>
                                            <?php if($user['user_role'] == 'author'): ?>
                                            <td>
                                                <?php 
                                               $class =  ($user['user_status'] == 'Active') ? 'primary' : 'dark';
                                               $icon =  ($user['user_status'] == 'Active') ? 'thumbs-up' : 'thumbs-down';
                                                ?>
                                                <div class="d-flex align-items-center gap-3 fs-5">
                                                    <a href="status_admin_user.php?id=<?= $user['id'] ?>"
                                                        class="text-<?= $class ?>" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title=""
                                                        data-bs-original-title="Edit info" aria-label="Edit">
                                                        <i class="bi bi-hand-<?= $icon ?>-fill"></i>

                                                    </a>

                                                    <a href="edit_admin_user.php?id=<?= htmlspecialchars($user['id']) ?>"
                                                        class="text-secondary" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title=""
                                                        data-bs-original-title="Edit info" aria-label="Edit"><i
                                                            class="bi bi-pencil-fill"></i></a>

                                                    <a href="delete_admin_user.php?id=<?= htmlspecialchars($user['id']) ?>"
                                                        class="text-danger" onclick="return confirm('Are you Sure?')"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom" title=""
                                                        data-bs-original-title="Delete" aria-label="Delete"><i
                                                            class="bi bi-trash-fill"></i></a>
                                                </div>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <nav class="float-end mt-0" aria-label="Page navigation">
                                <ul class="pagination">
                                    <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                </ul>
                            </nav>
                            <?php else: ?>
                            <div class="alert alert-danger fade show" role="alert">
                                <span>No Category Found!</span>
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