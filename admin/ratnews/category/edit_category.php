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
        header('Location: ' . basename(__FILE__));
        exit;
    }

    $id = filter_var(trim($_POST['id']), FILTER_VALIDATE_INT);
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($name)) {
        $_SESSION['errors'][] = 'Category name is Required!';
        header('Location: ' . basename(__FILE__));
        exit;
    }

    // Rename the slug 
    $categorySlug = strtolower(str_replace(' ', '-', $name));

    // Add Category to database
    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare('UPDATE category_tbl SET category_name = :categoryName, category_slug = :categorySlug WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':categoryName', $name);
        $stmt->bindParam(':categorySlug', $categorySlug);
        $result = $stmt->execute();

        if ($result) {
            $conn->commit();
            // Redirect to manage Category Page
            $_SESSION['errors'][] = 'Category Successfully Update.';
            header('Location: all_category.php');
            exit;
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['errors'][] = 'Error in Insert to database ' . $e->getMessage();
        header('Location: ' . basename(__FILE__));
        exit;
    }



}


// Store Error in Variable
$errors = $_SESSION['errors'] ?? [];
$_SESSION['errors'] = [];



// Fetch Data for Specific Id for Edit
if(isset($_GET['id'])){
    $id = filter_var(trim($_GET['id']), FILTER_VALIDATE_INT);

    $sql = $conn->prepare('SELECT * FROM category_tbl WHERE id = :id');
    $sql->bindParam(':id', $id);
    $sql->execute();
    $category = $sql->fetch();
}



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
                    <li class="breadcrumb-item active" aria-current="page">Categories</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-header py-3">
            <h6 class="mb-0">Edit Post Category</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-4 d-flex">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <form method="post" action="<?= htmlspecialchars(basename(__FILE__)) ?>" class="row g-3">
                                <input type="hidden" name="__csrf" value="<?= htmlspecialchars($_SESSION['__csrf']) ?>">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($category['id']) ?>">
                                <div class="col-12">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" placeholder="Category name"
                                        value="<?= htmlspecialchars($category['category_name']) ?>">
                                </div>
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button name="issSubmitted" class="btn btn-secondary">Update Category</button>
                                        <a href="all_category.php" class="btn btn-outline-danger mt-3">Cancel</a>
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