<?php
session_start();

$serialNo = 1;

// Connection to Database
require '../../config/connection.php';

// Initialize errors
if (isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}


try{

    $stmt = $conn->prepare('SELECT comments_tbl.*,
                                  user_tbl.user_fullname,
                                  post_tbl.post_title
                                 FROM comments_tbl
                                 LEFT JOIN user_tbl ON comments_tbl.user_id = user_tbl.id
                                 LEFT JOIN post_tbl ON comments_tbl.post_id = post_tbl.id
                                 ORDER BY post_tbl.id DESC');
    $stmt->execute();
    $comments = $stmt->fetchAll();

}catch(Exception $e){
    $_SESSION['errors'][] = 'Error in fetch comments ' . $e->getMessage();
    header('Location: ' . basename(__FILE__));
    exit;
}


// Store Error in Variable
$errors = $_SESSION['errors'] ?? [];
$_SESSION['errors'] = [];

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
                    <li class="breadcrumb-item active" aria-current="page">All Comments</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-header py-3">
            <h6 class="mb-0">All Comments</h6>
        </div>

        <div class="card-body">
            <div class="row">

                <!-- Display Error Messages -->
                <?php if (!empty($errors)): ?>
                <div class="col-12">
                    <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <span><?= htmlspecialchars($error) ?></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="col-12 col-lg-12 d-flex">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php if($comments): ?>
                                <table class="table align-middle">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>User Fullname</th>
                                            <th>Comments</th>
                                            <th>Post</th>
                                            <th>Comments Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($comments as $comment): ?>
                                        <tr class="text-center">
                                            <td><?= $serialNo++ ?></td>
                                            <td><?= htmlspecialchars($comment['user_fullname']) ?></td>
                                            <td><?= htmlspecialchars(substr($comment['comment'], 0, 30)) ?></td>
                                            <td><?= htmlspecialchars($comment['post_title']) ?></td>
                                            <td>
                                                <?php if($comment['comment_status'] == '0'): ?>
                                                <span class="badge rounded-pill text-bg-dark">Pending</span>
                                                <?php else: ?>
                                                <span class="badge rounded-pill text-bg-success">Approved</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="m-2 fs-5">
                                                    <?php if($comment['comment_status'] == '0'): ?>
                                                    <a href="comment_approved.php?id=<?= htmlspecialchars($comment['id']) ?>"
                                                        class="text-dark fs-2 me-3" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title=""
                                                        data-bs-original-title="Status" aria-label="Status"><i
                                                            class="bi bi-check2-circle"></i></a>
                                                    <?php endif; ?>
                                                    <a href="user_wise_comments.php?id=<?= htmlspecialchars($comment['id']) ?>"
                                                        class="text-dark fs-4 me-3" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title=""
                                                        data-bs-original-title="View" aria-label="View"><i
                                                            class="bi bi-eye-fill"></i></a>
                                                    <a href="delete_comment.php?id=<?= htmlspecialchars($comment['id']) ?>"
                                                        class="text-danger fs-4"
                                                        onclick="return confirm('Are you Sure?')"
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
                                    <span>No Comments Found!</span>
                                </div>
                                <?php endif; ?>
                            </div>
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