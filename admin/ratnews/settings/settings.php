<?php
session_start();

// Connection to Database
require '../../config/connection.php';

// Initialize errors in session for persistence across redirects
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['isSubmitted'])){
    // Verify CSRF Token
    if(!hash_equals($_SESSION['__csrf'], $_POST['__csrf'])){
        $_SESSION['errors'][] = 'Invalid CSRF Token';
        header('Location: ' . basename(__FILE__));
        exit;
    }


    $title = filter_var(trim($_POST['web_title']), FILTER_SANITIZE_SPECIAL_CHARS);
    $facebook = filter_var(trim($_POST['facebook']) , FILTER_SANITIZE_SPECIAL_CHARS);
    $twitter = filter_var(trim($_POST['twitter']) , FILTER_SANITIZE_SPECIAL_CHARS);
    $instagram = filter_var(trim($_POST['instagram']), FILTER_SANITIZE_SPECIAL_CHARS);
    $id = 1;

    if(empty($title) || empty($facebook) || empty($twitter) || empty($instagram)){
        $_SESSION['errors'][] = 'All Fields are required';
        header('Location: ' . basename(__FILE__));
        exit;
    }

   try{

    $stmt = $conn->prepare("UPDATE settings_tbl 
                                SET web_title = :wtitle,
                                    facebook = :fb,
                                    twitter = :twitter,
                                    instagram = :insta
                                WHERE id = :id");

        $stmt->bindParam(':wtitle', $title);
        $stmt->bindParam(':fb', $facebook);
        $stmt->bindParam(':twitter', $twitter);
        $stmt->bindParam(':insta', $instagram);
        $stmt->bindParam(':id', $id);
    $stmt->execute();
      $_SESSION['errors'][] = 'Successfully Update';
        header('Location: ' . basename(__FILE__));
        exit;

}catch(Exception $e){
       $_SESSION['errors'][] = 'Update Error ' . $e->getMessage();
        header('Location: ' . basename(__FILE__));
        exit;
}


}






// Fetch Settings Data
try{

    $sql = $conn->prepare("SELECT * FROM settings_tbl");
    $sql->execute();
    $settings = $sql->fetch();

}catch(Exception $e){
       $_SESSION['errors'][] = 'Error in Fetch all Settings from database ' . $e->getMessage();
        header('Location: ' . basename(__FILE__));
        exit;
}


$errors = $_SESSION['errors'] ?? [];
$_SESSION['errors'] = [];
require '../layout/header.php';

?>

<!-- Include Quill stylesheet -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">

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
                    <li class="breadcrumb-item active" aria-current="page">Manage Settings</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-header py-3">
            <h6 class="mb-0">Add Post</h6>
        </div>
        <!-- Display Errors -->
        <?php if (!empty($errors)): ?>
        <div class="row mt-3 justify-content-center">
            <div class="col-md-9 col-lg-9">
                <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <span><?= htmlspecialchars($error) ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <div class="card-body">
            <form class="row g-3" method="post" action="<?= htmlspecialchars(basename(__FILE__)) ?>">
                <input type="hidden" name="__csrf" value="<?= htmlspecialchars($_SESSION['__csrf']) ?>">
                <div class="col-12 col-lg-8">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Web Title</label>
                                <input type="text" class="form-control" name="web_title" placeholder="Web Title"
                                    value="<?= htmlspecialchars($settings['web_title']) ?>" autofocus>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Facebook</label>
                                <input type="text" class="form-control" name="facebook" placeholder="Facebook"
                                    value="<?= htmlspecialchars($settings['facebook']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Twitter</label>
                                <input type="text" class="form-control" name="twitter" placeholder="Twitter"
                                    value="<?= htmlspecialchars($settings['twitter']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Instagram</label>
                                <input type="text" class="form-control" name="instagram" placeholder="Instagram"
                                    value="<?= htmlspecialchars($settings['instagram']) ?>">
                            </div>
                            <div class="">
                                <button type="submit" name="isSubmitted" class="btn btn-dark">Update
                                    Setttings</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!--end row-->
        </div>
    </div>

</main>

<!--end page main-->

<?php require '../layout/footer.php' ?>