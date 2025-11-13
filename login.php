<?php
session_start();

// Connection to Database
require 'admin/config/connection.php';

// Generate CSRF Token
if (empty($_SESSION['__csrf'])) {
    $_SESSION['__csrf'] = bin2hex(random_bytes(32));
}

// Initialize messages in session for persistence across redirects
if (!isset($_SESSION['messages'])) {
    $_SESSION['messages'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issSubmitted'])) {
    // Verify CSRF Token
    if (!hash_equals($_SESSION['__csrf'], $_POST['__csrf'])) {
        $_SESSION['messages'][] = 'Invalid CSRF Token';
        header('Location: ' . basename(__FILE__));
        exit;
    }

    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = htmlspecialchars(trim($_POST['password']));


    if (empty($email) || empty($password)) {
        $_SESSION['messages'][] = 'All Fields Are Required';
        header('Location: ' . basename(__FILE__));
        exit;
    }
    try {

        // Fetch all user 
        $sql = $conn->prepare('SELECT * FROM user_tbl WHERE user_email = :uemail AND user_admin_status = "Active"');
        $sql->bindParam(':uemail', $email);
        $sql->execute();
        $user = $sql->fetch();

        if (!$user) {
            $_SESSION['messages'][] = 'Invalid email OR Password';
            header('Location: ' . basename(__FILE__));
            exit;
        }

        if (!password_verify($password, $user['user_password'])) {
            $_SESSION['messages'][] = 'Invalid email OR Password';
            header('Location: ' . basename(__FILE__));
            exit;
        }

        // Store User data into session variable
        $_SESSION['LoggedIn']      = true;
        $_SESSION['fullName']      = $user['user_fullname'];    
        $_SESSION['userId']        = $user['id'];

        // Update User Status
        $stmt = $conn->prepare('UPDATE user_tbl SET user_status = "1" WHERE id = :id');
        $stmt->bindParam(':id', $user['id']);
        $result = $stmt->execute();

        if ($result) {
            // Redirected to Admin Dashboard 
            header('Location: index.php');
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['messages'][] = 'User Login Error ' . $e->getMessage();
        header('Location: ' . basename(__FILE__));
        exit;
    }
}


// Store Error in Variable
$messages = $_SESSION['messages'] ?? [];
$_SESSION['messages'] = [];

require 'header.php';

?>

<!-- login -->
<section class="wrap__section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Form Login -->
                <div class="card mx-auto" style="max-width: 380px;">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Sign in</h4>
                        <form method="post" action="<?= htmlspecialchars(basename(__FILE__)) ?>">
                            <input type="hidden" name="__csrf" value="<?= htmlspecialchars($_SESSION['__csrf']) ?>">
                            <a href="#" class="btn btn-facebook btn-block mb-2 text-white"> <i
                                    class="fa fa-facebook"></i> &nbsp; Sign
                                in
                                with
                                Facebook</a>
                            <a href="#" class="btn btn-primary btn-block mb-4"> <i class="fa fa-google"></i> &nbsp;
                                Sign in with
                                Google</a>
                            <div class="form-group">
                                <input class="form-control" name="email" placeholder="Email" type="email">
                            </div> <!-- form-group// -->
                            <div class="form-group">
                                <input class="form-control" name="password" placeholder="Password" type="password">
                            </div> <!-- form-group// -->

                            <div class="form-group">
                                <a href="#" class="float-right">Forgot password?</a>
                                <label class="float-left custom-control custom-checkbox"> <input type="checkbox"
                                        class="custom-control-input" checked="">
                                    <span class="custom-control-label"> Remember </span>
                                </label>
                            </div> <!-- form-group form-check .// -->
                            <div class="form-group">
                                <button type="submit" name="issSubmitted" class="btn btn-primary btn-block"> Login
                                </button>
                            </div> <!-- form-group// -->
                        </form>
                    </div> <!-- card-body.// -->
                </div> <!-- card .// -->

                <p class="text-center mt-4">Don't have account? <a href="register.php">Sign up</a></p>
                <!-- Display messages -->
                <?php if (!empty($messages)): ?>
                <div class="row mt-3 justify-content-center">
                    <div class="col-md-12 col-lg-12">
                        <?php foreach ($messages as $msg): ?>
                        <div class=" text-center alert alert-success alert-dismissible fade show" role="alert">
                            <span><?= htmlspecialchars($msg) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<!-- end login -->

<?php require 'footer.php'; ?>
<?php $conn = null;  ?>