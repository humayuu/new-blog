<?php
session_start();

// Initialize errors in session for persistence across redirects
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

// Connection to Database
require 'admin/config/connection.php';

try {
    $id = htmlspecialchars(trim($_GET['id']));

    $stmt = $conn->prepare("SELECT 
                                post_tbl.*,
                                category_tbl.*,
                                admin_user_tbl.*
                           FROM post_tbl
                           LEFT JOIN category_tbl ON post_tbl.post_category = category_tbl.id  
                           LEFT JOIN admin_user_tbl ON post_tbl.user_id = admin_user_tbl.id
                           WHERE post_status = 'published' AND post_tbl.id = :id");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    
    if (!$post) {
        throw new Exception('Post not found');
    }
} catch (Exception $e) {
    $_SESSION['errors'][] = 'No Record Found! ' . $e->getMessage();
    header('Location: index.php');
    exit;
}

// Store Error in Variable
$errors = $_SESSION['errors'] ?? [];
$_SESSION['errors'] = [];

require 'header.php';
?>

<section class="py-5">
    <div class="container">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php foreach ($errors as $error): ?>
            <p class="mb-0"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8 col-md-12 mx-auto">
                <!-- Article Detail -->
                <article class="wrap__article-detail">
                    <!-- Article Header -->
                    <header class="wrap__article-detail-title mb-4">
                        <h1 class="mb-3">
                            <?= htmlspecialchars($post['post_title']) ?>
                        </h1>
                        <p class="lead text-muted">
                            <?= htmlspecialchars(substr(strip_tags($post['post_content']), 0, 150)) ?>...
                        </p>
                    </header>

                    <hr class="my-4">

                    <!-- Article Meta Info -->
                    <div class="wrap__article-detail-info mb-4">
                        <div class="d-flex align-items-center flex-wrap">
                            <figure class="mb-0 mr-3">
                                <img src="admin/ratnews/admin_users/<?= $post['user_image'] ?>"
                                    alt="<?= htmlspecialchars($post['user_name']) ?>" class="rounded-circle"
                                    style="width: 50px; height: 50px; object-fit: cover;">
                            </figure>

                            <div class="article-meta">
                                <p class="mb-1">
                                    <span class="text-muted">By</span>
                                    <a href="#" class="text-dark font-weight-bold">
                                        <?= htmlspecialchars($post['user_name']) ?>
                                    </a>
                                </p>
                                <p class="mb-0 text-muted small">
                                    <span><?= date('M d, Y', strtotime($post['created_at'])) ?></span>
                                    <span class="mx-2">•</span>
                                    <span>in</span>
                                    <a href="#" class="text-primary">
                                        <?= htmlspecialchars($post['category_name']) ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="wrap__article-detail-image mb-4">
                        <figure class="mb-0">
                            <img src="admin/ratnews/posts/<?= htmlspecialchars($post['post_image']) ?>"
                                class="img-fluid rounded w-100"
                                alt="<?= htmlspecialchars($post['post_title'] ?? 'Post image') ?>"
                                style="max-height: 500px; object-fit: cover;">
                        </figure>
                    </div>

                    <!-- Article Content -->
                    <div class="wrap__article-detail-content">
                        <div class="article-body" style="font-size: 1.1rem; line-height: 1.8;">
                            <?= htmlspecialchars(strip_tags($post['post_content'])) ?>
                        </div>
                    </div>

                    <!-- Article Footer -->
                    <div class="article-footer mt-5 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="article-tags">
                                <span class="badge badge-secondary p-2">
                                    <?= htmlspecialchars($post['category_name']) ?>
                                </span>
                            </div>
                            <div class="article-share">
                                <span class="text-muted mr-2">Share:</span>
                                <a href="#" class="btn btn-sm btn-outline-primary mr-1">
                                    <i class="fa fa-facebook"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-info mr-1">
                                    <i class="fa fa-twitter"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-danger">
                                    <i class="fa fa-pinterest"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Comments Section -->
                <div id="comments" class="comments-area mt-5 pt-4">
                    <h3 class="comments-title mb-4">2 Comments</h3>

                    <ol class="comment-list list-unstyled">
                        <li class="comment mb-4 pb-4 border-bottom">
                            <div class="comment-body">
                                <div class="d-flex">
                                    <img src="images/placeholder/80x80.jpg" class="avatar rounded-circle mr-3"
                                        alt="Commenter" style="width: 60px; height: 60px; object-fit: cover;">

                                    <div class="flex-grow-1">
                                        <div class="comment-meta mb-2">
                                            <h6 class="mb-0">
                                                <strong>Sinmun</strong>
                                                <small class="text-muted ml-2">April 24, 2019 at 10:59 am</small>
                                            </h6>
                                        </div>

                                        <div class="comment-content mb-2">
                                            <p class="mb-0">Lorem Ipsum has been the industry's standard dummy text ever
                                                since the 1500s, when an unknown printer took a galley of type and
                                                scrambled it to make a type specimen book.</p>
                                        </div>

                                        <div class="reply">
                                            <a href="#" class="btn btn-sm btn-link text-primary p-0">Reply</a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nested Comment -->
                                <ol class="children list-unstyled ml-5 mt-3">
                                    <li class="comment">
                                        <div class="comment-body">
                                            <div class="d-flex">
                                                <img src="images/placeholder/80x80.jpg"
                                                    class="avatar rounded-circle mr-3" alt="Commenter"
                                                    style="width: 50px; height: 50px; object-fit: cover;">

                                                <div class="flex-grow-1">
                                                    <div class="comment-meta mb-2">
                                                        <h6 class="mb-0">
                                                            <strong>Sinmun</strong>
                                                            <small class="text-muted ml-2">April 24, 2019 at 10:59
                                                                am</small>
                                                        </h6>
                                                    </div>

                                                    <div class="comment-content mb-2">
                                                        <p class="mb-0">Lorem Ipsum has been the industry's standard
                                                            dummy text ever since the 1500s, when an unknown printer
                                                            took a galley of type and scrambled it to make a type
                                                            specimen book.</p>
                                                    </div>

                                                    <div class="reply">
                                                        <a href="#"
                                                            class="btn btn-sm btn-link text-primary p-0">Reply</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ol>
                            </div>
                        </li>
                    </ol>

                    <!-- Comment Form -->
                    <div class="comment-respond mt-5">
                        <h3 class="comment-reply-title mb-4">Leave a Reply</h3>

                        <form class="comment-form" method="POST" action="">
                            <p class="text-muted mb-4">
                                <small>Your email address will not be published. Required fields are marked <span
                                        class="text-danger">*</span></small>
                            </p>

                            <div class="form-group">
                                <label for="comment">Comment <span class="text-danger">*</span></label>
                                <textarea name="comment" id="comment" class="form-control" rows="5" maxlength="65525"
                                    required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="author">Name <span class="text-danger">*</span></label>
                                        <input type="text" id="author" name="name" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <input type="email" id="email" name="email" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="url">Website</label>
                                <input type="url" id="url" name="url" class="form-control">
                            </div>

                            <div class="form-group">
                                <button type="submit" name="submit" class="btn btn-primary px-4 py-2">
                                    Post Comment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require 'footer.php' ?>