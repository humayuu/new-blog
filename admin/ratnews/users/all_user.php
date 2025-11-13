<?php
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

        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-12 d-flex">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <div class="table-responsive">
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
                                        <tr class="text-center fs-6">
                                            <td>1</td>
                                            <td>Humayun</td>
                                            <td>humayun@Gmail.com</td>
                                            <td>male</td>
                                            <td>Karachi</td>
                                            <td>Pakistan</td>
                                            <td class="text-primary fs-5">Online</td>
                                            <td class="badge text-bg-dark mt-3 fs-6">Active</td>
                                            <td>
                                                <div class="m-2 fs-5">
                                                    <a href="#" class="text-primary me-3" data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom" title=""
                                                        data-bs-original-title="Status" aria-label="Status"><i
                                                            class="bi bi-hand-thumbs-up-fill"></i></a>
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
            </div>
            <!--end row-->
        </div>

    </div>
    </div>

</main>
<!--end page main-->
<?php require '../layout/footer.php' ?>