<?php
session_start();

// Connection to Database
require 'admin/config/connection.php';


// Initialize messages in session for persistence across redirects
if (!isset($_SESSION['messages'])) {
    $_SESSION['messages'] = [];
}

// Generate CSRF Token
if(empty($_SESSION['__csrf'])){
    $_SESSION['__csrf'] = bin2hex(random_bytes(32));
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['issSubmitted'])){
    // Verify CSRF Token
    if(!hash_equals($_SESSION['__csrf'], $_POST['__csrf'])){
        $_SESSION['messages'][] = 'Invalid CSRF Token';
        header('Location: ' . basename(__FILE__));
        exit;
    }


    $firstName = filter_var(trim($_POST['fname']), FILTER_SANITIZE_SPECIAL_CHARS);
    $lastName = filter_var(trim($_POST['lname']), FILTER_SANITIZE_SPECIAL_CHARS);
    $fullName = $firstName . ' ' . $lastName;
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $gender = filter_var(trim($_POST['gender']), FILTER_SANITIZE_SPECIAL_CHARS);
    $city = filter_var(trim($_POST['city']), FILTER_SANITIZE_SPECIAL_CHARS);
    $country = filter_var(trim($_POST['country']), FILTER_SANITIZE_SPECIAL_CHARS);
    $createPassword = htmlspecialchars(trim($_POST['create_password']));
    $confirmPassword = htmlspecialchars(trim($_POST['confirm_password']));
    $userAdminStatus = 'Active';
    $userStatus = '0';

    // Validation
    if(empty($fullName) || empty($email) || empty($createPassword) || empty($confirmPassword)){
        $_SESSION['messages'][] = 'All Fields are Required';
        header('Location: ' . basename(__FILE__));
        exit;
    }

    if($createPassword !== $confirmPassword){
        $_SESSION['messages'][] = 'Create Password and Confirm Password must be matched';
        header('Location: ' . basename(__FILE__));
        exit;
    }

    // Check if User is already Exists

    // Hash Password
    $hashPassword = password_hash($confirmPassword, PASSWORD_DEFAULT);

    try{
        $conn->beginTransaction();

        $stmt = $conn->prepare('INSERT INTO user_tbl (user_fullname, user_email, user_gender, user_city, user_country, user_password, user_status, user_admin_status)
                                                     VALUES (:ufullname, :uemail, :ugender, :ucity, :ucountry, :upassword, :ustatus, :uadminstatus)');
        $stmt->bindParam(':ufullname', $fullName);                                             
        $stmt->bindParam(':uemail', $email);                                             
        $stmt->bindParam(':ugender', $gender);                                             
        $stmt->bindParam(':ucity', $city);                                             
        $stmt->bindParam(':ucountry', $country);                                             
        $stmt->bindParam(':upassword', $hashPassword);                                             
        $stmt->bindParam(':ustatus', $userStatus);                                             
        $stmt->bindParam(':uadminstatus', $userAdminStatus);      
        
        $result = $stmt->execute();

        if($result){
            $conn->commit();
            
            $_SESSION['messages'][] = 'Successfully! Register Please Login Your Account';
            // Redirect to login Page
            header('Location: login.php');
            exit;
        }

    }catch(Exception $e){
        $conn->rollBack();
        $_SESSION['messages'][] = 'Registration Error ' . $e->getMessage();
        header('Location: ' . basename(__FILE__));
        exit;
    }

}


// Store Error in Variable
$messages = $_SESSION['messages'] ?? [];
$_SESSION['messages'] = [];

require 'header.php';

?>
<!-- register -->
<section class="wrap__section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- register -->
                <!-- Form Register -->

                <div class="card mx-auto" style="max-width:520px;">
                    <article class="card-body">
                        <header class="mb-4">
                            <h4 class="card-title">Sign up</h4>
                        </header>
                        <form method="post" action="<?= htmlspecialchars(basename(__FILE__))  ?>">
                            <input type="hidden" name="__csrf" value="<?= htmlspecialchars($_SESSION['__csrf']) ?>">
                            <div class="form-row">
                                <div class="col form-group">
                                    <label>First name</label>
                                    <input type="text" name="fname" class="form-control" placeholder="" autofocus>
                                </div> <!-- form-group end.// -->
                                <div class="col form-group">
                                    <label>Last name</label>
                                    <input type="text" name="lname" class="form-control" placeholder="">
                                </div> <!-- form-group end.// -->
                            </div> <!-- form-row end.// -->
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" placeholder="">
                                <small class="form-text text-muted">We'll never share your email with anyone
                                    else.</small>
                            </div> <!-- form-group end.// -->
                            <div class="form-group">
                                <label class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" checked="" type="radio" name="gender"
                                        value="male">
                                    <span class="custom-control-label"> Male </span>
                                </label>
                                <label class="custom-control custom-radio custom-control-inline">
                                    <input class="custom-control-input" type="radio" name="gender" value="female">
                                    <span class="custom-control-label"> Female </span>
                                </label>
                            </div> <!-- form-group end.// -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>City</label>
                                    <input type="text" class="form-control" name="city">
                                </div> <!-- form-group end.// -->
                                <div class="form-group col-md-6">
                                    <label>Country</label>
                                    <select id="inputState" name="country" class="form-control">
                                        <option selected disabled> Choose...</option>
                                        <option value="Uzbekistan">Uzbekistan</option>
                                        <option value="Russia">Russia</option>
                                        <option value="USA">United States</option>
                                        <option value="India">India</option>
                                        <option value="Pakistan">Pakistan</option>
                                        <option value="Afganistan">Afganistan</option>
                                    </select>
                                </div> <!-- form-group end.// -->
                            </div> <!-- form-row.// -->
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Create password</label>
                                    <input class="form-control" type="password" name="create_password">
                                </div> <!-- form-group end.// -->
                                <div class="form-group col-md-6">
                                    <label>Repeat password</label>
                                    <input class="form-control" type="password" name="confirm_password">
                                </div> <!-- form-group end.// -->
                            </div>
                            <div class="form-group">
                                <button type="submit" name="issSubmitted" class="btn btn-primary btn-block"> Register
                                </button>
                            </div> <!-- form-group// -->
                        </form>
                        <!-- Display messages -->
                        <?php if (!empty($messages)): ?>
                        <div class="row mt-3 justify-content-center">
                            <div class="col-md-12 col-lg-12">
                                <?php foreach ($messages as $msg): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <span><?= htmlspecialchars($msg) ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </article><!-- card-body.// -->
                </div>
                <!-- end register -->
            </div>
        </div>
    </div>
</section>
<!-- end register -->

<?php require 'footer.php'; ?>
<?php $conn = null;  ?>