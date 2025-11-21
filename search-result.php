<?php
session_start();

// Initialize errors in session for persistence across redirects
if (! isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

// Connection to Database
require 'admin/config/connection.php';

// Page Limit 
$limit = 5;

if (isset($_GET['page'])) {
    $pageNo = $_GET['page'];
} else {
    $pageNo = 1;
}

$offSet = ($pageNo - 1) * $limit;

$search = '';
$posts = [];
$searchQuery = '';


try {

    if (isset($_GET['search'])) {
        $search      = htmlspecialchars($_GET['search']);
        $searchQuery = "%$search%";
        $stmt = $conn->prepare("SELECT post_tbl.*,
                        category_tbl.category_name,
                        admin_user_tbl.user_name 
                        FROM post_tbl
                        LEFT JOIN category_tbl ON post_tbl.post_category = category_tbl.id
                        LEFT JOIN admin_user_tbl ON post_tbl.user_id = admin_user_tbl.id
                        WHERE post_tbl.post_status = 'published' 
                        AND post_tbl.post_title LIKE :search 
                        LIMIT :offset, :limit");

        $stmt->bindParam(':search', $searchQuery, PDO::PARAM_STR);
        $stmt->bindParam(':offset', $offSet, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $_SESSION['errors'][] = 'No Search Found ' . $e->getMessage();
}

// Store Errors in Variable
$errors             = $_SESSION['errors'] ?? [];
$_SESSION['errors'] = [];

require 'header.php';
?>

<!-- search -->
<section>
    <div class="container">
        <div class="row">
            <?php if (!empty($errors)): ?>
            <div class="row mt-3 justify-content-center">
                <div class="col-md-12 col-lg-12">
                    <?php foreach ($errors as $error): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <span><?= htmlspecialchars($error) ?></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="col-md-8 mx-auto">
                <!-- Search result -->
                <div class="wrap__search-result">
                    <div class="wrap__search-result-keyword">
                        <h5>Search results for keyword: <span class="text-primary">
                                "<?php echo htmlspecialchars($search) ?>"
                            </span> found in <?php echo count($posts) ?>
                            posts. </h5>
                    </div>

                    <!-- Post Article List -->
                    <?php foreach ($posts as $post): ?>
                    <div class="card__post card__post-list card__post__transition mt-30">
                        <div class="row ">
                            <div class="col-md-5">
                                <div class="card__post__transition">
                                    <a href="article-detail.php?id=<?= htmlspecialchars($post['id']) ?>">
                                        <img src="admin/ratnews/posts/<?php echo htmlspecialchars($post['post_image']) ?>"
                                            class="img-fluid"
                                            alt="<?php echo htmlspecialchars($post['post_title'] ?? 'Post image') ?>">
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-7 my-auto pl-0">
                                <div class="card__post__body ">
                                    <div class="card__post__content  ">
                                        <div class="card__post__category ">
                                            <?= htmlspecialchars($post['category_name']) ?>
                                        </div>
                                        <div class="card__post__author-info mb-2">
                                            <ul class="list-inline">
                                                <li class="list-inline-item">
                                                    <span class="text-primary">
                                                        by <?= htmlspecialchars($post['user_name']) ?>
                                                    </span>
                                                </li>
                                                <li class="list-inline-item">
                                                    <span class="text-dark text-capitalize">
                                                        <?= date('M d, Y', strtotime($post['created_at'])); ?>
                                                    </span>
                                                </li>

                                            </ul>
                                        </div>
                                        <div class="card__post__title">
                                            <h5>
                                                <a href="article-detail.php?id=<?= htmlspecialchars($post['id']) ?>">
                                                    <?= htmlspecialchars($post['post_title']) ?>
                                                </a>
                                            </h5>
                                            <p class="d-none d-lg-block d-xl-block mb-0">
                                                <?= htmlspecialchars(substr(strip_tags($post['post_content']), 0, 100)) ?>...

                                            </p>

                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php
                $sql = $conn->prepare("SELECT COUNT(*) AS total
                       FROM post_tbl
                       LEFT JOIN category_tbl ON post_tbl.post_category = category_tbl.id
                       LEFT JOIN admin_user_tbl ON post_tbl.user_id = admin_user_tbl.id
                       WHERE post_tbl.post_status = 'published' 
                       AND post_tbl.post_title LIKE :search");
                $sql->bindParam(':search', $searchQuery, PDO::PARAM_STR);
                $sql->execute();
                $totalRows = $sql->fetch()['total'];
                $totalPages = ceil($totalRows / $limit);
                ?>

                <!-- pagination -->
                <div class="mt-4">
                    <div class="pagination-area">
                        <div class="pagination wow fadeIn animated">
                            <?php if ($pageNo > 1): ?>
                            <a href="?page=<?= $pageNo - 1 ?>&search=<?= urlencode($search) ?>">«</a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a class="<?= ($i == $pageNo) ? 'active' : '' ?>"
                                href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">
                                <?= $i ?>
                            </a>
                            <?php endfor; ?>

                            <?php if ($pageNo < $totalPages): ?>
                            <a href="?page=<?= $pageNo + 1 ?>&search=<?= urlencode($search) ?>">»</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>
<!-- end search -->
<?php require 'footer.php'; ?>