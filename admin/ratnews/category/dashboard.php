<?php
    session_start();
  

    // Connection to Database
    require '../../config/connection.php';

    $serialNo = 1;

    require '../layout/header.php';

?>
<!--start content-->
<main class="page-content">

    <!-- Stats Row -->
    <div class="row g-4 mb-4">

        <!-- Total Category -->
        <div class="col-12 col-md-6 col-xxl-3">
            <div class="card shadow-sm radius-10 h-100">
                <div class="card-body text-center">
                    <?php
                        $sql = $conn->prepare('SELECT * FROM category_tbl');
                        $sql->execute();
                        $categories = $sql->fetchAll();

                    ?>
                    <p class="text-muted mb-1">Total Categories</p>
                    <h3 class="fw-bold"><?php echo count($categories)?></h3>
                </div>
            </div>
        </div>

        <!-- Total Post -->
        <div class="col-12 col-md-6 col-xxl-3">
            <div class="card shadow-sm radius-10 h-100">
                <div class="card-body text-center">
                    <?php
                        $sql1 = $conn->prepare('SELECT * FROM post_tbl');
                        $sql1->execute();
                        $row = $sql1->fetchAll();

                    ?>
                    <p class="text-muted mb-1">Total Posts</p>
                    <h3 class="fw-bold"><?php echo count($row)?></h3>
                </div>
            </div>
        </div>

        <!-- Total User -->
        <div class="col-12 col-md-6 col-xxl-3">
            <div class="card shadow-sm radius-10 h-100">
                <div class="card-body text-center">
                    <?php
                        $sql2 = $conn->prepare('SELECT * FROM user_tbl');
                        $sql2->execute();
                        $users = $sql2->fetchAll();

                    ?>
                    <p class="text-muted mb-1">Total Users</p>
                    <h3 class="fw-bold"><?php echo count($users)?></h3>
                </div>
            </div>
        </div>

        <!-- Total Admin User -->
        <div class="col-12 col-md-6 col-xxl-3">
            <div class="card shadow-sm radius-10 h-100">
                <div class="card-body text-center">
                    <?php
                        $sql3 = $conn->prepare('SELECT * FROM admin_user_tbl');
                        $sql3->execute();
                        $adminUsers = $sql3->fetchAll();

                    ?>
                    <p class="text-muted mb-1">Admin Users</p>
                    <h3 class="fw-bold"><?php echo count($adminUsers)?></h3>
                </div>
            </div>
        </div>

    </div>
    <!-- End Stats Row -->


    <!-- Posts Table -->
    <div class="row">
        <div class="col-12">
            <div class="card radius-10 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3 fw-bold">Recent Posts</h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Post Title</th>
                                    <th>Content</th>
                                    <th>Status</th>
                                    <th>Category</th>
                                    <th>Author</th>
                                </tr>
                            </thead>
                            <?php
                                $stmt = $conn->prepare(
                                    'SELECT post_tbl.*,
                                    category_tbl.category_name,
                                    admin_user_tbl.user_name
                                    FROM post_tbl
                                    LEFT JOIN category_tbl ON post_tbl.post_category = category_tbl.id
                                    LEFT JOIN admin_user_tbl ON post_tbl.user_id = admin_user_tbl.id ORDER BY post_tbl.id DESC LIMIT 10');
                                $stmt->execute();
                                $posts = $stmt->fetchAll();
                            ?>


                            <tbody>
                                <?php foreach ($posts as $post): ?>
                                <tr class="text-center">
                                    <td><?php echo $serialNo++?></td>
                                    <td><?php echo htmlspecialchars($post['post_title'])?></td>
                                    <td><?php echo htmlspecialchars(substr(strip_tags($post['post_content']), 0, 50))?>...
                                    </td>
                                    <?php
                                        $class = ($post['post_status'] == 'published') ? 'primary' : 'dark';
                                    ?>
                                    <td class="badge text-bg-<?php echo $class?> m-4">

                                        <strong><?php echo strtoupper(htmlspecialchars($post['post_status']))?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($post['category_name'])?></td>
                                    <td><?php echo htmlspecialchars($post['user_name'])?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State Example -->
                    <!--
                    <div class="alert alert-warning text-center mt-3">
                        No Posts Found
                    </div>
                    -->
                </div>
            </div>
        </div>
    </div>


</main>

<!--end page main-->
<?php require '../layout/footer.php'; ?>