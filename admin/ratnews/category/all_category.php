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

        $stmt = $conn->prepare('INSERT INTO category_tbl (category_name, category_slug) VALUES (:categoryName, :categorySlug)');
        $stmt->bindParam(':categoryName', $name);
        $stmt->bindParam(':categorySlug', $categorySlug);
        $result = $stmt->execute();

        if ($result) {
            $conn->commit();
            // Redirect to manage Category Page
            $_SESSION['errors'][] = 'Category Successfully Add.';
            header('Location: ' . basename(__FILE__));
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


// Fetch all Data for Show category
$sql = $conn->prepare('SELECT * FROM category_tbl ORDER BY category_name DESC');
$sql->execute();
$categories = $sql->fetchAll();

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
            <h6 class="mb-0">Add Post Category</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-4 d-flex">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <form method="post" action="<?= htmlspecialchars(basename(__FILE__)) ?>" class="row g-3">
                                <input type="hidden" name="__csrf" value="<?= htmlspecialchars($_SESSION['__csrf']) ?>">
                                <div class="col-12">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" placeholder="Category name">
                                </div>
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button name="issSubmitted" class="btn btn-primary">Add Category</button>
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
                <div class="col-12 col-lg-8 d-flex">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php $sl = 1; if($categories): ?>
                                <table class="table align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Slug</th>
                                            <th>No of Post</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($categories as $category): ?>
                                        <tr>
                                            <td><?= $sl++ ?></td>
                                            <td><?= htmlspecialchars($category['category_name']) ?></td>
                                            <td><?= htmlspecialchars($category['category_slug']) ?></td>
                                            <td><?= htmlspecialchars($category['no_of_post']) ?></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3 fs-6">
                                                    <a href="edit_category.php?id=<?= htmlspecialchars($category['id']) ?>"
                                                        class="text-warning" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title=""
                                                        data-bs-original-title="Edit info" aria-label="Edit"><i
                                                            class="bi bi-pencil-fill"></i></a>
                                                    <a href="delete_category.php?id=<?= htmlspecialchars($category['id']) ?>"
                                                        class="text-danger" onclick="return confirm('Are you Sure?')"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom" title=""
                                                        data-bs-original-title="Delete" aria-label="Delete"><i
                                                            class="bi bi-trash-fill"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <div class="alert alert-danger fade show" role="alert">
                                    <span>No Category Found!</span>
                                </div>
                                <?php endif; ?>
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