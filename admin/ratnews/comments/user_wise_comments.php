<?php
session_start();

// Connection to Database
require '../../config/connection.php';

// Initialize errors in session for persistence across redirects
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}



try{
    $id = htmlspecialchars($_GET['id']);

    $stmt = $conn->prepare('SELECT comments_tbl.*,
                                  user_tbl.user_fullname,
                                  post_tbl.post_title
                                 FROM comments_tbl
                                 LEFT JOIN user_tbl ON comments_tbl.user_id = user_tbl.id
                                 LEFT JOIN post_tbl ON comments_tbl.post_id = post_tbl.id
                                 WHERE comments_tbl.id = :id');
                                 
    $stmt->bindParam(':id', $id);                        
    $stmt->execute();
    $comment = $stmt->fetch();

}catch(Exception $e){
    $_SESSION['errors'][] = 'Error in fetch comments ' . $e->getMessage();
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
                    <li class="breadcrumb-item active" aria-current="page">User Wise Comment</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-header py-3">
            <h6 class="mb-0">User Wise Comment</h6>
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
            <div class="col-12 col-lg-8">
                <div class="card border shadow-none w-100">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">User Name</label>
                            <input type="text" class="form-control" readonly disabled
                                value="<?= htmlspecialchars($comment['user_fullname']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Post</label>
                            <input type="text" class="form-control" readonly disabled
                                value="<?= htmlspecialchars($comment['post_title']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Comment</label>
                            <textarea class="form-control" rows="3" readonly
                                disabled><?= htmlspecialchars($comment['comment']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <?php if($comment['comment_status'] == '0'): ?>
                            <span class="badge rounded-pill text-bg-dark fs-5 m-3">Pending</span>
                            <?php else: ?>
                            <span class="badge rounded-pill text-bg-success fs-5 m-3">Approved</span>
                            <?php endif; ?>
                        </div>\
                        <a href="all_comments.php" class="btn btn-primary">Back</a>
                    </div>
                </div>
            </div>
            <!--end row-->
        </div>
    </div>

</main>

<!--end page main-->
<?php require '../layout/footer.php' ?>
<?php $conn = null; ?>