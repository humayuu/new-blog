    <?php
    session_start();

    // Initialize errors in session for persistence across redirects
    if (!isset($_SESSION['errors'])) {
        $_SESSION['errors'] = [];
    }

    // Connection to Database
    require 'admin/config/connection.php';

     // Page Limit
    $limit = 5;

    if(isset($_GET['page'])){
        $pageNo = $_GET['page'];
    }else{
        $pageNo = 1;
    }

    $offSet = ($pageNo - 1) * $limit;

    try {
        $id = htmlspecialchars(trim($_GET['auth_id']));

        $stmt = $conn->prepare("SELECT 
                                    post_tbl.*,
                                    category_tbl.category_name,
                                    admin_user_tbl.user_name
                            FROM post_tbl
                            LEFT JOIN category_tbl ON post_tbl.post_category = category_tbl.id  
                            LEFT JOIN admin_user_tbl ON post_tbl.user_id = admin_user_tbl.id
                            WHERE post_status = 'published' AND post_tbl.user_id = :id LIMIT :offset, :ulimit");
        $stmt->bindParam(':offset', $offSet, PDO::PARAM_INT);
        $stmt->bindParam(':ulimit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll();

        if (empty($posts)) {
            throw new Exception('No posts found for this Author');
        }
    } catch (Exception $e) {
        $_SESSION['errors'][] = 'No Record Found! ' . $e->getMessage();
        header('Location: index.php');
        exit;
    }

    $categoryName = $posts[0]['category_name'];


   

    
    // Store Error in Variable
    $errors = $_SESSION['errors'] ?? [];
    $_SESSION['errors'] = [];

    require 'header.php';
    ?>

    <section>
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
                <div class="col-md-12">
                    <aside class="wrapper__list__article ">
                        <h4 class="border_section"><?= htmlspecialchars($categoryName) ?></h4>

                        <div class="row ">
                            <?php foreach($posts as $post): ?>
                            <div class="col-md-7 mx-auto">
                                <!-- Post Article -->
                                <div class="article__entry">
                                    <div class="article__image">
                                        <img src="admin/ratnews/posts/<?= htmlspecialchars($post['post_image']) ?>"
                                            class="img-fluid"
                                            alt="<?= htmlspecialchars($post['post_title'] ?? 'Post image') ?>">
                                    </div>
                                    <div class="article__content">
                                        <div class="article__category">
                                            <?= $categoryName ?>
                                        </div>
                                        <ul class="list-inline">
                                            <li class="list-inline-item">
                                                <span class="text-primary">
                                                    by <?= htmlentities($post['user_name']) ?>

                                                </span>
                                            </li>
                                            <li class="list-inline-item">
                                                <span class="text-dark text-capitalize">
                                                    <?= date('M d, Y', strtotime($post['created_at'])); ?>
                                                </span>
                                            </li>

                                        </ul>
                                        <h5>
                                            <a href="article-detail.php?id=<?= $post['id']; ?>">
                                                <?= htmlspecialchars(substr(strip_tags($post['post_content']), 0, 50)) ?>...
                                            </a>
                                        </h5>
                                        <p>
                                            <?= htmlspecialchars(substr(strip_tags($post['post_content']), 0, 200)) ?>...
                                        </p>
                                        <a href="article-detail.php?id=<?= $post['id']; ?>"
                                            class="btn btn-outline-primary mb-4 text-capitalize"> read more</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                    </aside>

                </div>
            </div>

            <?php 
            $sql = $conn->prepare("SELECT COUNT(*) AS total FROM post_tbl WHERE user_id = :id");
            $sql->bindParam(':id', $id);
            $sql->execute();
            $totalRows = $sql->fetch()['total'];
            $totalPages = ceil($totalRows / $limit);

            ?>

            <!-- Pagination -->
            <div class="pagination-area">
                <div class="pagination wow fadeIn animated" data-wow-duration="2s" data-wow-delay="0.5s"
                    style="visibility: visible; animation-duration: 2s; animation-delay: 0.5s; animation-name: fadeIn;">
                    <?php if($pageNo > 1): ?>
                    <a href="?auth_id=<?= $id ?>&page=<?= $pageNo - 1 ?>">
                        «
                    </a>
                    <?php endif; ?>

                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <a class="<?= ( $i == $pageNo ) ? 'active' : '' ?>" href="?auth_id=<?= $id ?>&page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>

                    <?php if($pageNo < $totalPages): ?>
                    <a href="?auth_id=<?= $id ?>&page=<?= $pageNo + 1 ?>">
                        »
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php require 'footer.php' ?>