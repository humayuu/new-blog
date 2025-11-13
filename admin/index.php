<?php
session_start();
if(isset($_SESSION['adminLoggedIn']) || isset($_SESSION['adminLoggedIn']) == true){
    header('Location: ratnews/category/dashboard.php');
    exit;
}


// Connection to database
require 'config/connection.php';

// Generate CSRF Token
if (empty($_SESSION['__csrf'])) {
    $_SESSION['__csrf'] = bin2hex(random_bytes(32));
}

// Initialize errors in session for persistence across redirects
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Verify CSRF Token
    if (!hash_equals($_SESSION['__csrf'], $_POST['__csrf'])) {
        $_SESSION['errors'][] = 'Invalid CSRF Token';
        header('Location: ' . basename(__FILE__));
        exit;
    }

    $userName = filter_var(trim($_POST['username']), FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_var(trim($_POST['password']));
    $userStatus = 'Active';

    if (empty($userName) || empty($password)) {
        $_SESSION['errors'][] = 'Please Enter Your Username and Password';
        header('Location: ' . basename(__FILE__));
        exit;
    }

    try {
        // Check if  Admin user is Exists or not
        $sql = $conn->prepare('SELECT * FROM admin_user_tbl WHERE user_name = :username AND user_status = :ustatus');
        $sql->bindParam(':username', $userName);
        $sql->bindParam(':ustatus', $userStatus);
        $sql->execute();
        $user = $sql->fetch();
        if (!$user) {
            $_SESSION['errors'][] = 'Invalid Username OR Password';
            header('Location: ' . basename(__FILE__));
            exit;
        }

        // Verify Password
        if (!password_verify($password, $user['user_password'])) {
            $_SESSION['errors'][] = 'Invalid Username OR Password';
            header('Location: ' . basename(__FILE__));
            exit;
        }


        // Store User data into session variable
        $_SESSION['adminLoggedIn']  = true;
        $_SESSION['adminId']        = $user['id'];
        $_SESSION['adminUsername']  = $user['user_name'];
        $_SESSION['adminUserImage'] = $user['user_image'];
        $_SESSION['adminUserRole']  = $user['user_role'];

        // Redirected to Admin Dashboard 
        header('Location: ratnews/category/dashboard.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['errors'][] = 'Admin Login Error ' . $e->getMessage();
        header('Location: ' . basename(__FILE__));
        exit;
    }
}


$errors = $_SESSION['errors'] ?? [];
$_SESSION['errors'] = [];

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ratnews Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4 p-sm-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 80px; height: 80px;">
                                <i class="bi bi-newspaper fs-1"></i>
                            </div>
                            <!-- Display Errors -->
                            <?php if (!empty($errors)): ?>
                            <div class="row justify-content-center">
                                <div class="col-md-12 col-lg-12">
                                    <?php foreach ($errors as $error): ?>
                                    <div class="alert alert-danger fade show" role="alert">
                                        <?= htmlspecialchars($error) ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            <h4 class="fw-semibold text-dark">Ratnews</h4>
                            <p class="text-muted mb-0">Sign In to Dashboard</p>
                        </div>

                        <form method="post" action="<?= htmlspecialchars(basename(__FILE__)) ?>">
                            <input type="hidden" name="__csrf" value="<?= htmlspecialchars($_SESSION['__csrf']) ?>">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-medium">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-person-fill text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="username"
                                        name="username" placeholder="Enter your username" autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-medium">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-lock-fill text-muted"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 ps-0" id="password"
                                        name="password" placeholder="Enter your password">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                                    <label class="form-check-label" for="rememberMe">
                                        Remember Me
                                    </label>
                                </div>
                                <a href="#" class="text-primary text-decoration-none small">Forgot
                                    Password?</a>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php $conn = null; ?>