<?php
session_start();

// Connection to Database
require '../../config/connection.php';

// Initialize errors in session for persistence across redirects
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

// Generate CSRF Token 
if (empty($_SESSION['__csrf'])) {
    $_SESSION['__csrf'] = bin2hex(random_bytes(32));
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['isSubmitted'])) {
    // Verify CSRF Token
    if (!hash_equals($_SESSION['__csrf'], $_POST['__csrf'])) {
        $_SESSION['errors'][] = 'Invalid CSRF Token';
        header('Location: ' . basename(__FILE__));
        exit;
    }

    $title = filter_var(trim($_POST['title']), FILTER_SANITIZE_SPECIAL_CHARS);
    $content = trim($_POST['content']); // Don't sanitize HTML content from editor
    $excerpt = filter_var(trim($_POST['excerpt']), FILTER_SANITIZE_SPECIAL_CHARS);
    $status = filter_var(trim($_POST['status']), FILTER_SANITIZE_SPECIAL_CHARS);
    $category = filter_var(trim($_POST['category']), FILTER_SANITIZE_SPECIAL_CHARS);
    $tags = filter_var(trim($_POST['tags']), FILTER_SANITIZE_SPECIAL_CHARS);
    $metaDescription = filter_var(trim($_POST['meta_description']), FILTER_SANITIZE_SPECIAL_CHARS);
    $featuredPost = isset($_POST['is_featured']) ? 1 : 0;
    $allowComments = isset($_POST['allow_comments']) ? 1 : 0;
    $userId = $_SESSION['adminId'];
    $image = null;

    $allowedExtension = ['jpeg', 'jpg', 'png'];
    $MaxFileSize = 2 * 1024 * 1024; // 2Mb;
    $UploadDir = __DIR__ . '/uploads/post_image/';

    // Create Upload Directory
    if (!is_dir($UploadDir)) {
        mkdir($UploadDir, 0755, true);
    }


    // Validations
    if (empty($title) || empty($content) || empty($excerpt) || empty($status) || empty($category)  || empty($tags) || empty($allowComments)) {
        $_SESSION['errors'][] = 'All Fields are Required';
        header('Location: ' . basename(__FILE__));
        exit;
    }

    // Post Image Upload
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['post_image']['size'];
        $tmpName = $_FILES['post_image']['tmp_name'];

        if (!in_array($ext, $allowedExtension)) {
            $_SESSION['errors'][] = 'File Extension is not allowed';
            header('Location: ' . basename(__FILE__));
            exit;
        }

        if ($size > $MaxFileSize) {
            $_SESSION['errors'][] = 'Max File size is 2 MB';
            header('Location: ' . basename(__FILE__));
            exit;
        }

        $newName = uniqid('post_') . time() . '.' . $ext;

        if (!move_uploaded_file($tmpName, $UploadDir . $newName)) {
            $_SESSION['errors'][] = 'Max File size is 2 MB';
            header('Location: ' . basename(__FILE__));
            exit;
        }

        $image = 'uploads/post_image/' . $newName;
    }

    // Image Validation
    if (empty($image)) {
        $_SESSION['errors'][] = 'Post Image is Required';
        header('Location: ' . basename(__FILE__));
        exit;
    }

    // Inert Data into the Database
    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare('INSERT INTO post_tbl (post_title, post_content, post_excerpt, post_status, post_category, user_id, post_image, post_tags, post_meta_description, featured_post, allow_comments)
                                                     VALUES (:ptitle, :pcontent, :pexcerpt, :pstatus, :pcategory, :user_Id, :pimage, :ptags, :pmeta_descp, :featured_post, :allow_comments)');
        $stmt->bindParam(':ptitle', $title);
        $stmt->bindParam(':pcontent', $content);
        $stmt->bindParam(':pexcerpt', $excerpt);
        $stmt->bindParam(':pstatus', $status);
        $stmt->bindParam(':pcategory', $category);
        $stmt->bindParam(':user_Id', $userId);
        $stmt->bindParam(':pimage', $image);
        $stmt->bindParam(':ptags', $tags);
        $stmt->bindParam(':pmeta_descp', $metaDescription);
        $stmt->bindParam(':featured_post', $featuredPost);
        $stmt->bindParam(':allow_comments', $allowComments);
        $result = $stmt->execute();

        if ($result) {
            $conn->commit();

            $_SESSION['errors'][] = 'Post Successfully ' . strtoupper($status);
            // Redirected to View All Post
            header('Location: view_all_post.php');
            exit;
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['errors'][] = 'Post Insert Error ' . $e->getMessage();
        header('Location: ' . basename(__FILE__));
        exit;
    }
}



$errors = $_SESSION['errors'] ?? [];
$_SESSION['errors'] = [];

require '../layout/header.php';

?>

<!-- Include Quill stylesheet -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">

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
                    <li class="breadcrumb-item active" aria-current="page">Manage Post</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
        <div class="card-header py-3">
            <h6 class="mb-0">Add Post</h6>
        </div>
        <!-- Display Errors -->
        <?php if (!empty($errors)): ?>
        <div class="row mt-3 justify-content-center">
            <div class="col-md-9 col-lg-9">
                <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <span><?= htmlspecialchars($error) ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <div class="card-body">
            <form class="row g-3" method="POST" action="<?= htmlspecialchars(basename(__FILE__)) ?>"
                enctype="multipart/form-data" id="postForm">
                <input type="hidden" name="__csrf" value="<?= htmlspecialchars($_SESSION['__csrf']) ?>">
                <div class="col-12 col-lg-8">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" name="title" placeholder="Post Title" autofocus>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <div id="editor" style="min-height: 300px;"></div>
                                <textarea name="content" id="content-input" style="display: none;"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Excerpt (Summary)</label>
                                <textarea class="form-control" name="excerpt" rows="3"
                                    placeholder="Short description for post preview"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Select Post Status</label>
                                <select class="form-select" name="status">
                                    <option value="draft" selected>Draft</option>
                                    <option value="published">Published</option>
                                    <option value="scheduled">Scheduled</option>
                                </select>
                            </div>

                            <?php
                            // Fetch All Category
                            $sql = $conn->prepare('SELECT * FROM category_tbl ORDER BY category_name');
                            $sql->execute();
                            $categories = $sql->fetchAll();
                            ?>
                            <div class="mb-3">
                                <label class="form-label">Select Post Category</label>
                                <select class="form-select" name="category">
                                    <option disabled selected>Select Post Category</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category['id']) ?>">
                                        <?= htmlspecialchars($category['category_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Featured Image</label>
                                <input type="file" class="form-control" name="post_image" accept="image/*">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tags</label>
                                <input type="text" class="form-control" name="tags"
                                    placeholder="technology, news, breaking">
                                <small class="text-muted">Separate tags with commas</small>
                            </div>


                            <div class="mb-3">
                                <label class="form-label">Meta Description (SEO)</label>
                                <textarea class="form-control" name="meta_description" rows="3"
                                    placeholder="SEO description for search engines"></textarea>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured"
                                        value="1">
                                    <label class="form-check-label" for="isFeatured">
                                        Featured Post
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="allow_comments"
                                        id="allowComments" value="1" checked>
                                    <label class="form-check-label" for="allowComments">
                                        Allow Comments
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="isSubmitted" class="btn btn-primary">Publish Post</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!--end row-->
        </div>
    </div>

</main>

<!--end page main-->

<!-- Include Quill library -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

<!-- Initialize Quill editor -->
<script>
// Initialize Quill editor
const quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{
                'header': [1, 2, 3, 4, 5, 6, false]
            }],
            [{
                'font': []
            }],
            [{
                'size': ['small', false, 'large', 'huge']
            }],
            ['bold', 'italic', 'underline', 'strike'],
            [{
                'color': []
            }, {
                'background': []
            }],
            [{
                'script': 'sub'
            }, {
                'script': 'super'
            }],
            [{
                'list': 'ordered'
            }, {
                'list': 'bullet'
            }],
            [{
                'indent': '-1'
            }, {
                'indent': '+1'
            }],
            [{
                'align': []
            }],
            ['blockquote', 'code-block'],
            ['link', 'image', 'video'],
            ['clean']
        ]
    },
    placeholder: 'Write your post content here...'
});

// Sync Quill content to hidden textarea before form submission
const form = document.getElementById('postForm');
form.addEventListener('submit', function(e) {
    const contentInput = document.getElementById('content-input');
    contentInput.value = quill.root.innerHTML;
});
</script>

<?php require '../layout/footer.php' ?>
<?php $conn = null; ?>