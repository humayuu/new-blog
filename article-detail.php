<?php
session_start();

// Initialize message in session for persistence across redirects
if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = [];
}

// Generate CSRF Token
if (!isset($_SESSION['__csrf'])) {
    $_SESSION['__csrf'] = bin2hex(random_bytes(32));
}
// Connection to Database
require 'admin/config/connection.php';

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        $_SESSION['message'][] = 'Invalid post ID';
        header('Location: index.php');
        exit;
    }
    $id = htmlspecialchars(trim($_GET['id']));

    $stmt = $conn->prepare("SELECT 
                                post_tbl.*,
                                category_tbl.*,
                                admin_user_tbl.*
                           FROM post_tbl
                           LEFT JOIN category_tbl ON post_tbl.post_category = category_tbl.id  
                           LEFT JOIN admin_user_tbl ON post_tbl.user_id = admin_user_tbl.id
                           WHERE post_status = 'published' AND post_tbl.id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $post = $stmt->fetch();

    if (!$post) {
        throw new Exception('Post not found');
    }
} catch (Exception $e) {
    $_SESSION['message'][] = 'No Record Found! ' . $e->getMessage();
    header('Location: index.php');
    exit;
}


// Add Comments

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Verify CSRF Token
    if (!hash_equals($_SESSION['__csrf'], $_POST['__csrf'])) {
        header('Location: ' . basename(__FILE__));
        exit;
    }

    $userId = $_SESSION['userId'];
    $postId = $id;
    $comments = filter_var(trim($_POST['comment']), FILTER_SANITIZE_SPECIAL_CHARS);
    $commentsStatus = '0';


    try {

        $query = $conn->prepare('INSERT INTO comments_tbl (user_id, post_id, comment, comment_status) VALUES (:userid, :postId, :userComment, :commentStatus)');
        $query->bindParam(':userid', $userId);
        $query->bindParam(':postId', $postId);
        $query->bindParam(':userComment', $comments);
        $query->bindParam(':commentStatus', $commentsStatus);
        $result = $query->execute();

        if ($result) {
            $_SESSION['message'][] = 'Comment submitted successfully. Please wait for admin approval.';
            header('Location: article-detail.php?id=' . $id);
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['message'][] = 'error in Add comments' . $e->getMessage();
        header('Location: ' . basename(__FILE__));
        exit;
    }
}

// Fetch Approved Comments
try {
    $sq = $conn->prepare('SELECT comments_tbl.*, user_tbl.user_fullname as user_name 
                          FROM comments_tbl 
                          LEFT JOIN user_tbl ON comments_tbl.user_id = user_tbl.id
                          WHERE comments_tbl.post_id = :postId AND comments_tbl.comment_status = "1"
                          ORDER BY comments_tbl.created_at DESC');
    $sq->bindParam(':postId', $id);
    $sq->execute();
    $comments = $sq->fetchAll();
} catch (Exception $e) {
    $_SESSION['message'][] = 'Error in fetching comments: ' . $e->getMessage();
    $comments = []; 
}

// Store Error in Variable
$message = $_SESSION['message'] ?? [];
$_SESSION['message'] = [];

require 'header.php';
?>

<section class="py-5">
    <div class="container">
        <?php if (!empty($message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php foreach ($message as $msg): ?>
            <p class="mb-0"><?= htmlspecialchars($msg) ?></p>
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
                    <h3 class="comments-title mb-4">
                        <?= count($comments) ?> Comment<?= count($comments) != 1 ? 's' : '' ?>
                    </h3>

                    <?php if (!empty($comments)): ?>
                    <ol class="comment-list list-unstyled">
                        <?php foreach($comments as $comment): ?>
                        <li class="comment mb-4 pb-4 border-bottom">
                            <div class="comment-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <div class="comment-meta mb-2">
                                            <h6 class="mb-0">
                                                <strong><?= htmlspecialchars($comment['user_name']) ?></strong>
                                                <small class="text-muted ml-2">
                                                    <?= date('M d, Y', strtotime($comment['created_at'])) ?>
                                                </small>
                                            </h6>
                                        </div>
                                        <div class="comment-content mb-2">
                                            <p class="mb-0"><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                    <?php else: ?>
                    <p class="text-muted">No comments yet. Be the first to comment!</p>
                    <?php endif; ?>


                    <?php if (isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'] !== false): ?>
                    <!-- Comment Form -->
                    <div class="comment-respond mt-5">
                        <h3 class="comment-reply-title mb-4">Leave Your Comment</h3>

                        <form method="post" action="article-detail.php?id=<?= $id ?>"> <input type="hidden"
                                name="__csrf" value="<?= htmlspecialchars($_SESSION['__csrf']) ?>">

                            <div class="form-group">
                                <label for="comment">Comment <span class="text-danger">*</span></label>
                                <textarea name="comment" class="form-control" rows="5"></textarea>
                            </div>

                            <div class="form-group">
                                <button type="submit" name="submit" class="btn btn-primary mt-2 px-4 py-2">
                                    Post Comment
                                </button>
                            </div>
                        </form>
                    </div>
                    <?php else: ?>
                    <a href="login.php" class="btn btn-dark m-5">Login / Register</a>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</section>

<?php require 'footer.php' ?>