<?php
require '../layout/header.php';
require '../../config/connection.php';

// Initialize errors
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

$errors = $_SESSION['errors'] ?? [];
$_SESSION['errors'] = [];

$serialNo = 1;

//  Get the CURRENT logged-in user
$sql = $conn->prepare('SELECT * FROM admin_user_tbl WHERE id = :userId');
$sql->execute(['userId' => $_SESSION['adminId']]);
$user = $sql->fetch(); // Get single user

// Check if user exists and is logged in
if(!$user) {
    header('Location: ../auth/login.php');
    exit();
}

//  Fetch posts based on user role
if($user['user_role'] === 'admin'){
    // ADMINS see ALL posts (from all users)
    $stmt = $conn->prepare("SELECT 
                                post_tbl.*,
                                category_tbl.category_name,
                                admin_user_tbl.user_name,
                                admin_user_tbl.user_role
                           FROM post_tbl
                           LEFT JOIN category_tbl ON post_tbl.post_category = category_tbl.id  
                           LEFT JOIN admin_user_tbl ON post_tbl.user_id = admin_user_tbl.id
                           ORDER BY post_tbl.id DESC");
    $stmt->execute();
    $posts = $stmt->fetchAll();                               
} else {
    // AUTHORS see ONLY their own posts
    $stmt = $conn->prepare("SELECT 
                                post_tbl.*,
                                category_tbl.category_name,
                                admin_user_tbl.user_name,
                                admin_user_tbl.user_role
                           FROM post_tbl
                           LEFT JOIN category_tbl ON post_tbl.post_category = category_tbl.id  
                           LEFT JOIN admin_user_tbl ON post_tbl.user_id = admin_user_tbl.id
                           WHERE post_tbl.user_id = :userId
                           ORDER BY post_tbl.id DESC");
    $stmt->execute(['userId' => $_SESSION['adminId']]);
    $posts = $stmt->fetchAll();  
}

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
                <!-- Display Errors -->
                <?php if (!empty($errors)): ?>
                <div class="row mt-3 justify-content-center">
                    <div class="col-md-8 col-lg-8">
                        <?php foreach ($errors as $error): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <span><?= htmlspecialchars($error) ?></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="col-12 col-lg-12 d-flex">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php if($posts): ?>
                                <table class="table align-middle">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>Post Title</th>
                                            <th>Post Content</th>
                                            <th>Post Status</th>
                                            <th>Post Category</th>
                                            <?php if($user['user_role'] == 'admin'): ?>
                                            <th>User</th>
                                            <?php endif; ?>
                                            <th>Post Image</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($posts as $post): ?>
                                        <tr class="text-center">
                                            <td><?= $serialNo++ ?></td>
                                            <td><?= htmlspecialchars($post['post_title']) ?></td>
                                            <td><?= htmlspecialchars(substr(strip_tags($post['post_content']), 0, 50)) ?>...
                                            </td>
                                            <?php 
                                            $class =  ($post['post_status'] == 'published') ? 'primary' : 'dark';
                                            ?>
                                            <td class="badge text-bg-<?= $class ?> mt-3">

                                                <strong><?= strtoupper(htmlspecialchars($post['post_status'])) ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($post['category_name']) ?></td>
                                            <?php if($user['user_role'] == 'admin'): ?>
                                            <td><?= htmlspecialchars($post['user_name']) ?></td>
                                            <?php endif; ?>
                                            <td><img width="100" src="<?= htmlspecialchars($post['post_image']) ?>"
                                                    alt="Post Image"></td>
                                            <td>
                                                <?php 
                                               $icon =  ($post['post_status'] == 'published') ? 'thumbs-up' : 'thumbs-down';
                                                ?>
                                                <div class="m-2 fs-5">
                                                    <a href="post_status.php?id=<?= htmlspecialchars($post['id']) ?>"
                                                        class="text-<?= $class ?> me-3" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title=""
                                                        data-bs-original-title="Status" aria-label="Status"><i
                                                            class="bi bi-hand-<?= $icon ?>-fill"></i></a>
                                                    <a href="view_post.php?id=<?= htmlspecialchars($post['id']) ?>"
                                                        class="text-dark me-3" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title=""
                                                        data-bs-original-title="View" aria-label="View"><i
                                                            class="bi bi-eye-fill"></i></a>
                                                    <a href="edit_post.php?id=<?= htmlspecialchars($post['id']) ?>"
                                                        class="text-warning me-3" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title=""
                                                        data-bs-original-title="Edit" aria-label="Edit"><i
                                                            class="bi bi-pencil-fill"></i></a>
                                                    <a href="delete_post.php?id=<?= htmlspecialchars($post['id']) ?>"
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
                                <span>No Post Found</span>
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
<?php $conn = null; ?>