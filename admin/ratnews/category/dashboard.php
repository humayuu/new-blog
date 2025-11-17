<?php
session_start();
if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true) {
    $_SESSION['errors'][] = 'Please Login First';
    header('Location: ../index.php');
    exit;
}


require '../layout/header.php';


?>
<!--start content-->
<main class="page-content">

    <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-2 row-cols-xxl-4">
        <div class="col">
            <div class="card overflow-hidden radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-stretch justify-content-between overflow-hidden">
                        <div class="w-50">
                            <p>Total Orders</p>
                            <h4 class="">8,542</h4>
                        </div>
                        <div class="w-50">
                            <p class="mb-3 float-end text-success">+ 16% <i class="bi bi-arrow-up"></i></p>
                            <div id="chart1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card overflow-hidden radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-stretch justify-content-between overflow-hidden">
                        <div class="w-50">
                            <p>Total Views</p>
                            <h4 class="">12.5M</h4>
                        </div>
                        <div class="w-50">
                            <p class="mb-3 float-end text-danger">- 3.4% <i class="bi bi-arrow-down"></i></p>
                            <div id="chart2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card overflow-hidden radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-stretch justify-content-between overflow-hidden">
                        <div class="w-50">
                            <p>Revenue</p>
                            <h4 class="">$64.5K</h4>
                        </div>
                        <div class="w-50">
                            <p class="mb-3 float-end text-success">+ 24% <i class="bi bi-arrow-up"></i></p>
                            <div id="chart3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card overflow-hidden radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-stretch justify-content-between overflow-hidden">
                        <div class="w-50">
                            <p>Customers</p>
                            <h4 class="">25.8K</h4>
                        </div>
                        <div class="w-50">
                            <p class="mb-3 float-end text-success">+ 8.2% <i class="bi bi-arrow-up"></i></p>
                            <div id="chart4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
    <div class="row">
        <div class="col-12 col-lg-12 col-xl-12 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0">Recent Orders</h6>
                        <div class="fs-5 ms-auto dropdown">
                            <div class="dropdown-toggle dropdown-toggle-nocaret cursor-pointer"
                                data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></div>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#ID</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#89742</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="product-box border">
                                                <img src="assets/images/products/11.png" alt="">
                                            </div>
                                            <div class="product-info">
                                                <h6 class="product-name mb-1">Smart Mobile Phone</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>2</td>
                                    <td>$214</td>
                                    <td>Apr 8, 2021</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3 fs-6">
                                            <a href="javascript:;" class="text-primary" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="View detail"
                                                aria-label="Views"><i class="bi bi-eye-fill"></i></a>
                                            <a href="javascript:;" class="text-warning" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="Edit info"
                                                aria-label="Edit"><i class="bi bi-pencil-fill"></i></a>
                                            <a href="javascript:;" class="text-danger" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="Delete"
                                                aria-label="Delete"><i class="bi bi-trash-fill"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#68570</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="product-box border">
                                                <img src="assets/images/products/07.png" alt="">
                                            </div>
                                            <div class="product-info">
                                                <h6 class="product-name mb-1">Sports Time Watch</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>1</td>
                                    <td>$185</td>
                                    <td>Apr 9, 2021</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3 fs-6">
                                            <a href="javascript:;" class="text-primary" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="View detail"
                                                aria-label="Views"><i class="bi bi-eye-fill"></i></a>
                                            <a href="javascript:;" class="text-warning" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="Edit info"
                                                aria-label="Edit"><i class="bi bi-pencil-fill"></i></a>
                                            <a href="javascript:;" class="text-danger" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="Delete"
                                                aria-label="Delete"><i class="bi bi-trash-fill"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#38567</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="product-box border">
                                                <img src="assets/images/products/17.png" alt="">
                                            </div>
                                            <div class="product-info">
                                                <h6 class="product-name mb-1">Women Red Heals</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>3</td>
                                    <td>$356</td>
                                    <td>Apr 10, 2021</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3 fs-6">
                                            <a href="javascript:;" class="text-primary" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="View detail"
                                                aria-label="Views"><i class="bi bi-eye-fill"></i></a>
                                            <a href="javascript:;" class="text-warning" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="Edit info"
                                                aria-label="Edit"><i class="bi bi-pencil-fill"></i></a>
                                            <a href="javascript:;" class="text-danger" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="Delete"
                                                aria-label="Delete"><i class="bi bi-trash-fill"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#48572</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="product-box border">
                                                <img src="assets/images/products/04.png" alt="">
                                            </div>
                                            <div class="product-info">
                                                <h6 class="product-name mb-1">Yellow Winter Jacket</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>1</td>
                                    <td>$149</td>
                                    <td>Apr 11, 2021</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3 fs-6">
                                            <a href="javascript:;" class="text-primary" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="View detail"
                                                aria-label="Views"><i class="bi bi-eye-fill"></i></a>
                                            <a href="javascript:;" class="text-warning" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="Edit info"
                                                aria-label="Edit"><i class="bi bi-pencil-fill"></i></a>
                                            <a href="javascript:;" class="text-danger" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="Delete"
                                                aria-label="Delete"><i class="bi bi-trash-fill"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#96857</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="product-box border">
                                                <img src="assets/images/products/10.png" alt="">
                                            </div>
                                            <div class="product-info">
                                                <h6 class="product-name mb-1">Orange Micro Headphone</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>2</td>
                                    <td>$199</td>
                                    <td>Apr 15, 2021</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3 fs-6">
                                            <a href="javascript:;" class="text-primary" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="View detail"
                                                aria-label="Views"><i class="bi bi-eye-fill"></i></a>
                                            <a href="javascript:;" class="text-warning" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="Edit info"
                                                aria-label="Edit"><i class="bi bi-pencil-fill"></i></a>
                                            <a href="javascript:;" class="text-danger" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="Delete"
                                                aria-label="Delete"><i class="bi bi-trash-fill"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#96857</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="product-box border">
                                                <img src="assets/images/products/12.png" alt="">
                                            </div>
                                            <div class="product-info">
                                                <h6 class="product-name mb-1">Pro Samsung Laptop</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>1</td>
                                    <td>$699</td>
                                    <td>Apr 18, 2021</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3 fs-6">
                                            <a href="javascript:;" class="text-primary" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="View detail"
                                                aria-label="Views"><i class="bi bi-eye-fill"></i></a>
                                            <a href="javascript:;" class="text-warning" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="Edit info"
                                                aria-label="Edit"><i class="bi bi-pencil-fill"></i></a>
                                            <a href="javascript:;" class="text-danger" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="" data-bs-original-title="Delete"
                                                aria-label="Delete"><i class="bi bi-trash-fill"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!--end row-->



</main>
<!--end page main-->
<?php require '../layout/footer.php'; ?>