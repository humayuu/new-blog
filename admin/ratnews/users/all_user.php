<?php
require '../layout/header.php';

// Connection to Database
require '../../config/connection.php';


// Initialize errors
if (isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}


$serialNo = 1;
$limit = 5;
if(isset($_GET['page'])){
    $pageNo = $_GET['page'];

}else{
    $pageNo = 1;
}

$offSet = ($pageNo - 1) * $limit;


try{

    $stmt = $conn->prepare("SELECT * FROM user_tbl ORDER BY user_fullname DESC LIMIT $offSet, $limit");
    $stmt->execute();
    $users = $stmt->fetchAll();

}catch(Exception $e){
       $_SESSION['errors'][] = 'Error in Fetch all User from database ' . $e->getMessage();
        header('Location: ' . basename(__FILE__));
        exit;
}




// Store Error in Variable
$errors = $_SESSION['errors'] ?? [];
$_SESSION['errors'] = [];

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
                    <li class="breadcrumb-item active" aria-current="page">All Users</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-header py-3">
            <h6 class="mb-0">All Users</h6>
        </div>
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

        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-12 d-flex">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php if($users): ?>
                                <table class="table align-middle">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>Fullname</th>
                                            <th>Email</th>
                                            <th>Gender</th>
                                            <th>City</th>
                                            <th>Country</th>
                                            <th>Status</th>
                                            <th>Admin Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($users as $user): ?>
                                        <tr class="text-center fs-6">
                                            <td><?= $serialNo++ ?></td>
                                            <td><?= htmlspecialchars($user['user_fullname']) ?></td>
                                            <td><?= htmlspecialchars($user['user_email']) ?></td>
                                            <td><?= htmlspecialchars($user['user_gender']) ?></td>
                                            <td><?= htmlspecialchars($user['user_city']) ?></td>
                                            <td><?= htmlspecialchars($user['user_country']) ?></td>
                                            <?php if(htmlspecialchars($user['user_status']) == '1'): ?>
                                            <td class="text-primary fs-5">Online</td>
                                            <?php else: ?>
                                            <td class="text-dark fs-5">Offline</td>
                                            <?php endif; ?>

                                            <?php if(htmlspecialchars($user['user_admin_status']) == 'Active'): ?>
                                            <td class="badge text-bg-success mt-3 fs-6">Active</td>
                                            <?php else: ?>
                                            <td class="badge text-bg-dark mt-3 fs-6">Inactive</td>
                                            <?php endif; ?>

                                            <td>
                                                <?php 
                                               $class =  ($user['user_admin_status'] == 'Inactive') ? 'dark' : 'primary';
                                               $icon =  ($user['user_admin_status'] == 'Active') ? 'thumbs-up' : 'thumbs-down';
                                                ?>
                                                <div class="m-2 fs-5">
                                                    <a href="user_status.php?id=<?= htmlspecialchars($user['id']) ?>"
                                                        class="text-<?= $class ?> me-3" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title=""
                                                        data-bs-original-title="Status" aria-label="Status"><i
                                                            class="bi bi-hand-<?= $icon ?>-fill"></i></a>
                                                    <a href="user_delete.php?id=<?= htmlspecialchars($user['id']) ?>"
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
                                <?php
                                $sql1 = $conn->prepare('SELECT COUNT(*) AS total FROM user_tbl');
                                $sql1->execute();
                                $totalRows = $sql1->fetch()['total'];
                                $totalPages = ceil($totalRows / $limit);
                                
                                ?>

                                <nav class="float-end mt-0" aria-label="Page navigation">
                                    <ul class="pagination">
                                        <li class="page-item <?= ($pageNo <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=<?= $pageNo - 1 ?>">Previous</a>
                                        </li>
                                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= ($i == $pageNo) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                        <?php endfor; ?>


                                        <li class="page-item <?= ($pageNo >= $totalPages) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=<?= $pageNo + 1 ?>">Next</a>
                                        </li>
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
            </div>
            <!--end row-->
        </div>

    </div>
    </div>

</main>
<!--end page main-->
<?php require '../layout/footer.php' ?>