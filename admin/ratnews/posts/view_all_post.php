<?php
session_start();

// Connection to Database
require '../../config/connection.php';


// Initialize errors in session for persistence across redirects
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

// Store Error in Variable
$errors = $_SESSION['errors'] ?? [];
$_SESSION['errors'] = [];

require '../layout/header.php'

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
                <div class="col-12 col-lg-12 d-flex">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table  align-middle">
                                    <thead class="table-light">
                                        <tr class="text-center">
                                            <th>#</th>
                                            <th>Post Title</th>
                                            <th>Post Content</th>
                                            <th>Post Status</th>
                                            <th>Post Category</th>
                                            <th>User</th>
                                            <th>Post Image</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td>1</td>
                                            <td>title</td>
                                            <td>Lorem, ipsum dolor</td>
                                            <td>
                                                <div class="m-2">
                                                    <select class="form-select text-center" name="status">
                                                        <option value="draft" selected>Draft</option>
                                                        <option value="published">Published</option>
                                                        <option value="scheduled">Scheduled</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>World</td>
                                            <td>Ali</td>
                                            <td><img src="" alt="Post Image"></td>
                                            <td>
                                                <div class="m-2 fs-5">
                                                    <a href="#" class="text-dark me-3" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title=""
                                                        data-bs-original-title="Delete" aria-label="Delete"><i
                                                            class="bi bi-eye-fill"></i></a>
                                                    <a href="#" class="text-warning me-3" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title=""
                                                        data-bs-original-title="Edit info" aria-label="Edit"><i
                                                            class="bi bi-pencil-fill"></i></a>
                                                    <a href="#" class="text-danger"
                                                        onclick="return confirm('Are you Sure?')"
                                                        data-bs-toggle="tooltip" data-bs-placement="bottom" title=""
                                                        data-bs-original-title="Delete" aria-label="Delete"><i
                                                            class="bi bi-trash-fill"></i></a>
                                                </div>
                                            </td>


                                        </tr>
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
                            <!-- <div class="alert alert-danger fade show" role="alert">
                                <span>No Post Found</span>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->
        </div>
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
    </div>
    </div>

</main>
<!--end page main-->
<?php require '../layout/footer.php' ?>