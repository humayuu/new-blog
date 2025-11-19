<?php
session_start();

// Initialize errors in session for persistence across redirects
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}


$limit = 5;

// Get Current Page
if(isset($_GET['page'])){
    $pageNo = $_GET['page'];
}else{
    $pageNo = 1;
}

$offSet = ($pageNo - 1) * $limit;

// Connection to Database
require 'admin/config/connection.php';

try{

       $stmt = $conn->prepare("SELECT 
                                post_tbl.*,
                                category_tbl.category_name,
                                admin_user_tbl.user_name,
                                admin_user_tbl.user_role
                           FROM post_tbl
                           LEFT JOIN category_tbl ON post_tbl.post_category = category_tbl.id  
                           LEFT JOIN admin_user_tbl ON post_tbl.user_id = admin_user_tbl.id
                           WHERE post_status = 'published' ORDER BY post_tbl.id DESC LIMIT $offSet, $limit");
    $stmt->execute();
    $posts = $stmt->fetchAll();                               

}catch(Exception $e){
    $_SESSION['errors'][] = 'No Record Found! ' . $e->getMessage();
    header('Location: ' . basename(__FILE__));
    exit;
    
}


// Store Error in Variable
$errors = $_SESSION['errors'] ?? [];
$_SESSION['errors'] = [];


require 'header.php';
?>

<!-- Tranding news  carousel-->
<section class="bg-light">
    <!-- Display Errors -->
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
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="wrapp__list__article-responsive wrapp__list__article-responsive-carousel">
                    <?php foreach($posts as $post): ?>
                    <div class="item">
                        <!-- Post Article -->
                        <div class="card__post card__post-list">
                            <div class="image-sm">
                                <a href="./card-article-detail-v1.html">
                                    <img src="admin/ratnews/posts/<?= htmlspecialchars($post['post_image']) ?>"
                                        class="img-fluid"
                                        alt="<?= htmlspecialchars($post['post_title'] ?? 'Post image') ?>">
                                </a>
                            </div>


                            <div class="card__post__body ">
                                <div class="card__post__content">

                                    <div class="card__post__author-info mb-2">
                                        <ul class="list-inline">
                                            <li class="list-inline-item">
                                                <span class="text-primary">
                                                    <?= htmlentities($post['user_name']) ?>
                                                </span>
                                            </li>
                                            <li class="list-inline-item">
                                                <span class="text-dark text-capitalize">
                                                    <?php 
                                                    echo date('M d, Y', strtotime($post['created_at']));
                                                    ?>
                                                </span>
                                            </li>

                                        </ul>
                                    </div>
                                    <div class="card__post__title">
                                        <h6>
                                            <a href="./card-article-detail-v1.html">
                                                <?= htmlspecialchars(substr(strip_tags($post['post_content']), 0, 50)) ?>...
                                            </a>
                                        </h6>

                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </div>
</section>
<!-- End Tranding news carousel -->

<!-- Popular news -->
<section>
    <!-- Popular news  header-->
    <div class="popular__news-header">
        <div class="container">
            <div class="row no-gutters">
                <div class="col-md-12 ">
                    <div class="card__post-carousel">
                        <?php foreach($posts as $post): ?>
                        <div class="item">
                            <!-- Post Article -->
                            <div class="card__post">
                                <div class="card__post__body">
                                    <a href="./card-article-detail-v1.html">
                                        <img src="admin/ratnews/posts/<?= htmlspecialchars($post['post_image']) ?>"
                                            class="img-fluid"
                                            alt="<?= htmlspecialchars($post['post_title'] ?? 'Post image') ?>"> </a>
                                    <div class="card__post__content bg__post-cover">
                                        <div class="card__post__category">
                                            <?= htmlspecialchars($post['category_name']) ?>
                                        </div>
                                        <div class="card__post__title">
                                            <h2>
                                                <a href="#">
                                                    <?= htmlspecialchars(substr(strip_tags($post['post_content']), 0, 50)) ?>...

                                                </a>
                                            </h2>
                                        </div>
                                        <div class="card__post__author-info">
                                            <ul class="list-inline">
                                                <li class="list-inline-item">
                                                    <a href="#">
                                                        <?= htmlentities($post['user_name']) ?>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <span>
                                                        <?php 
                                                    echo date('M d, Y', strtotime($post['created_at']));
                                                    ?>
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Popular news header-->
</section>
<!-- End Popular news -->

<!-- Popular news category -->
<section class="pt-0">
    <div class="popular__section-news">
        <div class="container">
            <div class="row">

                <!-- Post news carousel -->
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <aside class="wrapper__list__article">
                                <h4 class="border_section">Featured News</h4>
                            </aside>
                        </div>
                        <div class="col-md-12">

                            <div class="article__entry-carousel">
                                <?php foreach($posts as $post): ?>
                                <div class="item">
                                    <!-- Post Article -->
                                    <div class="card__post card__post-list">
                                        <div class="image-sm">
                                            <a href="./card-article-detail-v1.html">
                                                <img src="admin/ratnews/posts/<?= htmlspecialchars($post['post_image']) ?>"
                                                    class="img-fluid"
                                                    alt="<?= htmlspecialchars($post['post_title'] ?? 'Post image') ?>">
                                            </a>
                                        </div>


                                        <div class="card__post__body ">
                                            <div class="card__post__content">

                                                <div class="card__post__author-info mb-2">
                                                    <ul class="list-inline">
                                                        <li class="list-inline-item">
                                                            <span class="text-primary">
                                                                <?= htmlentities($post['user_name']) ?>
                                                            </span>
                                                        </li>
                                                        <li class="list-inline-item">
                                                            <span class="text-dark text-capitalize">
                                                                <?php 
                                                    echo date('M d, Y', strtotime($post['created_at']));
                                                    ?>
                                                            </span>
                                                        </li>

                                                    </ul>
                                                </div>
                                                <div class="card__post__title">
                                                    <h6>
                                                        <a href="./card-article-detail-v1.html">
                                                            <?= htmlspecialchars(substr(strip_tags($post['post_content']), 0, 50)) ?>...
                                                        </a>
                                                    </h6>

                                                </div>

                                            </div>


                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Popular news category -->


                <!-- Popular news category -->
                <div class="mt-4">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-8">
                                <aside class="wrapper__list__article">
                                    <h4 class="border_section">latest</h4>

                                    <div class="wrapp__list__article-responsive">
                                        <!-- Post Article List -->
                                        <div class="card__post card__post-list card__post__transition mt-30">
                                            <div class="row ">
                                                <?php foreach($posts as $post): ?>
                                                <div class="col-md-5">
                                                    <div class="card__post__transition">
                                                        <a href="#">
                                                            <img src="admin/ratnews/posts/<?= htmlspecialchars($post['post_image']) ?>"
                                                                class="img-fluid"
                                                                alt="<?= htmlspecialchars($post['post_title'] ?? 'Post image') ?>">
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-md-7 my-auto pl-0">
                                                    <div class="card__post__body ">
                                                        <div class="card__post__content  ">
                                                            <div class="card__post__category ">
                                                                <?= htmlspecialchars($post['category_name'])  ?>
                                                            </div>
                                                            <div class="card__post__author-info mb-2">
                                                                <ul class="list-inline">
                                                                    <li class="list-inline-item">
                                                                        <span class="text-primary">
                                                                            <?= htmlspecialchars($post['user_name'])  ?>
                                                                        </span>
                                                                    </li>
                                                                    <li class="list-inline-item">
                                                                        <span class="text-dark text-capitalize">
                                                                            <?=  date('M d, Y', strtotime($post['created_at'])); ?>
                                                                        </span>
                                                                    </li>

                                                                </ul>
                                                            </div>
                                                            <div class="card__post__title">
                                                                <h5>
                                                                    <a href="#">
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
                                                <?php endforeach; ?>

                                            </div>
                                        </div>
                                    </div>
                                </aside>
                            </div>

                            <div class="col-md-4">
                                <div class="sticky-top">


                                    <aside class="wrapper__list__article">
                                        <h4 class="border_section">stay conected</h4>
                                        <!-- widget Social media -->
                                        <div class="wrap__social__media">
                                            <a href="#" target="_blank">
                                                <div class="social__media__widget facebook">
                                                    <span class="social__media__widget-icon">
                                                        <i class="fa fa-facebook"></i>
                                                    </span>
                                                    <span class="social__media__widget-counter">
                                                        19,243 fans
                                                    </span>
                                                    <span class="social__media__widget-name">
                                                        like
                                                    </span>
                                                </div>
                                            </a>
                                            <a href="#" target="_blank">
                                                <div class="social__media__widget twitter">
                                                    <span class="social__media__widget-icon">
                                                        <i class="fa fa-twitter"></i>
                                                    </span>
                                                    <span class="social__media__widget-counter">
                                                        2.076 followers
                                                    </span>
                                                    <span class="social__media__widget-name">
                                                        follow
                                                    </span>
                                                </div>
                                            </a>
                                            <a href="#" target="_blank">
                                                <div class="social__media__widget youtube">
                                                    <span class="social__media__widget-icon">
                                                        <i class="fa fa-youtube"></i>
                                                    </span>
                                                    <span class="social__media__widget-counter">
                                                        15,200 followers
                                                    </span>
                                                    <span class="social__media__widget-name">
                                                        subscribe
                                                    </span>
                                                </div>
                                            </a>

                                        </div>
                                    </aside>

                                    <aside class="wrapper__list__article">
                                        <h4 class="border_section">Advertise</h4>
                                        <a href="#">
                                            <figure>
                                                <img src="images/placeholder/600x400.jpg" alt="" class="img-fluid">
                                            </figure>
                                        </a>
                                    </aside>

                                    <aside class="wrapper__list__article">
                                        <h4 class="border_section">newsletter</h4>
                                        <!-- Form Subscribe -->
                                        <div class="widget__form-subscribe bg__card-shadow">
                                            <h6>
                                                The most important world news and events of the day.
                                            </h6>
                                            <p><small>Get magzrenvi daily newsletter on your inbox.</small></p>
                                            <div class="input-group ">
                                                <input type="text" class="form-control"
                                                    placeholder="Your email address">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="button">sign up</button>
                                                </div>
                                            </div>
                                        </div>
                                    </aside>
                                </div>
                            </div>
                            <div class="mx-auto">
                                <?php 
                            $query = $conn->prepare('SELECT COUNT(*) AS total FROM post_tbl');
                            $query->execute();
                            $totalRows = $query->fetch()['total'];
                            $totalPages = ceil($totalRows / $limit);
                            ?>
                                <!-- Pagination -->
                                <div class="pagination-area">
                                    <div class="pagination wow fadeIn animated" data-wow-duration="2s"
                                        data-wow-delay="0.5s"
                                        style="visibility: visible; animation-duration: 2s; animation-delay: 0.5s; animation-name: fadeIn;">
                                        <?php if ($pageNo > 1): ?>
                                        <a href="?page=<?= $pageNo - 1 ?>"> « </a>
                                        <?php endif; ?>

                                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                        <a class="<?= ($i == $pageNo) ? 'active' : '' ; ?>" href="?page=<?= $i ?>">
                                            <?= $i ?>
                                        </a>
                                        <?php endfor; ?>

                                        <?php if ($pageNo <= 2): ?>
                                        <a href="?page=<?= $pageNo + 1 ?>"> » </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
</section>
<!-- End Popular news category -->

<?php require 'footer.php'; ?>