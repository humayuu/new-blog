<?php
require '../../config/connection.php';
require '../layout/header.php';

// Initialize errors in session for persistence across redirects
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

// Fetch all Data for Specific ID
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if (!$id) {
        $_SESSION['errors'][] = 'Invalid ID';
        header('Location: view_all_post.php');
        exit;
    }

    $sql = $conn->prepare('SELECT * FROM post_tbl WHERE id = :id');
    $sql->bindParam(':id', $id, PDO::PARAM_INT);
    $sql->execute();
    $post = $sql->fetch();

    // Check if post exists
    if (!$post) {
        $_SESSION['errors'][] = 'Post not found';
        header('Location: view_all_post.php');
        exit;
    }
} else {
    $_SESSION['errors'][] = 'No post ID provided';
    header('Location: view_all_post.php');
    exit;
}

$errors = $_SESSION['errors'] ?? [];
$_SESSION['errors'] = [];
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
            <h6 class="mb-0">Edit Post</h6>
        </div>

        <div class="card-body">
            <!-- Display Errors -->
            <?php if (!empty($errors)): ?>
            <div class="row mt-3 mb-3">
                <div class="col-12">
                    <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <span><?= htmlspecialchars($error) ?></span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <form class="row g-3" method="POST" action="update_post.php" enctype="multipart/form-data" id="postForm">
                <input type="hidden" name="__csrf" value="<?= htmlspecialchars($_SESSION['__csrf']) ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars($post['id']) ?>">
                <input type="hidden" name="old_image" value="<?= htmlspecialchars($post['post_image']) ?>">
                <div class="col-12 col-lg-8">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" name="title" placeholder="Post Title"
                                    value="<?= htmlspecialchars($post['post_title']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <div id="editor" style="min-height: 300px;"></div>
                                <textarea name="content" style="display: none;" id="content-input"
                                    required><?= htmlspecialchars($post['post_content']) ?></textarea>

                            </div>

                            <div class="mb-3">
                                <label class="form-label">Excerpt (Summary)</label>
                                <textarea class="form-control" name="excerpt" rows="3"
                                    placeholder="Short description for post preview"
                                    required><?= htmlspecialchars($post['post_excerpt']) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card border shadow-none w-100">
                        <div class="card-body">
                            <?php
                            // Fetch All Category
                            $sql = $conn->prepare('SELECT * FROM category_tbl ORDER BY category_name');
                            $sql->execute();
                            $categories = $sql->fetchAll();
                            ?>
                            <div class="mb-3">
                                <label class="form-label">Select Post Category</label>
                                <select class="form-select" name="category" required>
                                    <option value="" disabled>Select Post Category</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option <?= ($category['id'] == $post['post_category']) ? 'selected' : '' ?>
                                        value="<?= htmlspecialchars($category['id']) ?>">
                                        <?= htmlspecialchars($category['category_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Featured Image</label>
                                <input type="file" class="form-control" name="post_image" accept="image/*">
                                <?php if (!empty($post['post_image']) && file_exists(__DIR__ . '/' . $post['post_image'])): ?>
                                <img class="img-thumbnail mt-2" width="200"
                                    src="<?= htmlspecialchars($post['post_image']) ?>" alt="Current post image">
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tags</label>
                                <input type="text" class="form-control" name="tags"
                                    placeholder="technology, news, breaking"
                                    value="<?= htmlspecialchars($post['post_tags']) ?>" required>
                                <small class="text-muted">Separate tags with commas</small>
                            </div>


                            <div class="mb-3">
                                <label class="form-label">Meta Description (SEO)</label>
                                <textarea class="form-control" name="meta_description" rows="3"
                                    placeholder="SEO description for search engines"><?= htmlspecialchars($post['post_meta_description']) ?></textarea>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured"
                                        value="1" <?= ($post['featured_post'] == '1') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="isFeatured">
                                        Featured Post
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="allow_comments"
                                        id="allowComments" value="1"
                                        <?= ($post['allow_comments'] == '1') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="allowComments">
                                        Allow Comments
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="isSubmitted" class="btn btn-dark">Update Post</button>
                                <a href="view_all_post.php" class="mt-2 btn btn-outline-dark">Cancel</a>

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

//  Load existing content into Quill
const existingContent = <?= json_encode($post['post_content'] ?? '') ?>;
quill.root.innerHTML = existingContent;

//  Sync Quill content to hidden textarea before form submission
const form = document.getElementById('postForm');
form.addEventListener('submit', function(e) {
    const contentInput = document.getElementById('content-input');
    contentInput.value = quill.root.innerHTML;
});
</script>


<?php require '../layout/footer.php' ?>
<?php $conn = null; ?>