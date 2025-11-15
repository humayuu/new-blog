<?php 
session_start();

// Connection to Database
require '../../config/connection.php';

// Generate CSRF Token
if (!isset($_SESSION['__csrf'])) {
    $_SESSION['__csrf'] = bin2hex(random_bytes(32));
}


// Initialize errors in session for persistence across redirects
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['isSubmitted'])) {
    
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
    
    // Verify CSRF Token 
    if (!hash_equals($_SESSION['__csrf'], $_POST['__csrf'])) {
        $_SESSION['errors'][] = 'Invalid CSRF Token';
        header('Location: edit_post.php?id=' . $id);
        exit;
    }

    if (!$id) {
        $_SESSION['errors'][] = 'Invalid ID';
        header('Location: view_all_post.php');
        exit;
    }

    $title = filter_var(trim($_POST['title']), FILTER_SANITIZE_SPECIAL_CHARS);
    $content = trim($_POST['content']);
    $excerpt = filter_var(trim($_POST['excerpt']), FILTER_SANITIZE_SPECIAL_CHARS);
    $category = filter_var(trim($_POST['category']), FILTER_VALIDATE_INT);
    $metaDescription = filter_var(trim($_POST['meta_description']), FILTER_SANITIZE_SPECIAL_CHARS);
    $tags = filter_var(trim($_POST['tags']), FILTER_SANITIZE_SPECIAL_CHARS);
    $oldImage = filter_var(trim($_POST['old_image']), FILTER_SANITIZE_SPECIAL_CHARS);
    $image = null;

    $featuredPost = isset($_POST['is_featured']) ? 1 : 0;
    $allowComments = isset($_POST['allow_comments']) ? 1 : 0;

    $allowedExtension = ['jpeg', 'jpg', 'png'];
    $MaxFileSize = 2 * 1024 * 1024; // 2Mb;
    $UploadDir = __DIR__ . '/uploads/post_image/';

    // Create Upload Directory
    if (!is_dir($UploadDir)) {
        mkdir($UploadDir, 0755, true);
    }


    // Validations
    if (empty($title) || empty($content) || empty($excerpt) || empty($category) || empty($tags)) {
        $_SESSION['errors'][] = 'All Fields are Required';
        header('Location: edit_post.php?id=' . $id);
        exit;
    }

    // Upload Image 
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['post_image']['size'];
        $tmpName = $_FILES['post_image']['tmp_name'];

        if (!in_array($ext, $allowedExtension)) {
            $_SESSION['errors'][] = 'Extension not allowed';
            header('Location: edit_post.php?id=' . $id);
            exit;
        }

        if ($size > $MaxFileSize) {
            $_SESSION['errors'][] = 'Max File size is 2 MB';
            header('Location: edit_post.php?id=' . $id);
            exit;
        }

        $newName = uniqid('post_') . time() . '.' . $ext;

        if (!move_uploaded_file($tmpName, $UploadDir . $newName)) {
            $_SESSION['errors'][] = 'File Upload Error';
            header('Location: edit_post.php?id=' . $id);
            exit;
        }

        $image = 'uploads/post_image/' . $newName;

        // Delete old image if new one is uploaded
        if (!empty($oldImage) && file_exists(__DIR__ . '/' . $oldImage)) {
            unlink(__DIR__ . '/' . $oldImage);
        }
    }

    $image = (!empty($image)) ? $image : $oldImage;


    // Update data into the database
    try {

        $conn->beginTransaction();

        $stmt = $conn->prepare('UPDATE post_tbl 
                                        SET   post_title            = :ptitle,
                                              post_content          = :pcontent,     
                                              post_excerpt          = :pexcerpt,     
                                              post_category         = :pcategory,     
                                              post_tags             = :ptags,     
                                              post_meta_description = :pmeta,     
                                              featured_post         = :pfpost,     
                                              allow_comments        = :pallowed_comments,
                                              post_image            = :pimage
                                        WHERE id                    = :pid');
        $stmt->bindParam(':ptitle', $title);
        $stmt->bindParam(':pcontent', $content);
        $stmt->bindParam(':pexcerpt', $excerpt);
        $stmt->bindParam(':pcategory', $category, PDO::PARAM_INT);
        $stmt->bindParam(':ptags', $tags);
        $stmt->bindParam(':pmeta', $metaDescription);
        $stmt->bindParam(':pfpost', $featuredPost, PDO::PARAM_INT);
        $stmt->bindParam(':pallowed_comments', $allowComments, PDO::PARAM_INT);
        $stmt->bindParam(':pimage', $image);
        $stmt->bindParam(':pid', $id, PDO::PARAM_INT);
        $result = $stmt->execute();
        
        if ($result) {
            $conn->commit();
            $_SESSION['success'] = 'Post Successfully Updated';
            header('Location: view_all_post.php');
            exit;
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['errors'][] = 'Update Error: ' . $e->getMessage();
        header('Location: edit_post.php?id=' . $id);
        exit;
    }
}