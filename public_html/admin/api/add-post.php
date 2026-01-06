<?php
/**
 * ADMIN/API/ADD-POST.PHP - TẠỌO BÀI VIẾT
 */

require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/db.php';

// Kiểm tra quyền admin
if (!isAdmin()) {
    jsonResponse(false, 'Bạn không có quyền!');
}

// Lấy dữ liệu
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$category = $_POST['category'] ?? 'news';
$status = $_POST['status'] ?? 'draft';
$image = null;

// Validate
$errors = [];
if (empty($title)) $errors[] = 'Tiêu đề không được để trống';
if (empty($content)) $errors[] = 'Nội dung không được để trống';

if (!empty($errors)) {
    jsonResponse(false, implode(', ', $errors));
}

// Xử lý upload ảnh (tuỳ chọn)
if (!empty($_FILES['image']['name'])) {
    $file = $_FILES['image'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        jsonResponse(false, 'Chỉ cho phép ảnh (jpg, png, gif)');
    }
    
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB
        jsonResponse(false, 'Ảnh quá lớn (tối đa 5MB)');
    }
    
    $new_filename = uniqid() . '.' . $ext;
    $upload_dir = __DIR__ . '/../../uploads/';
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_filename)) {
        $image = BASE_URL . 'uploads/' . $new_filename;
    }
}

// Tạo bài viết
$result = createPost(
    getCurrentUserId(),
    $title,
    $content,
    $category,
    $image,
    $status
);

if ($result['success']) {
    jsonResponse(true, 'Bài viết đã được tạo!', $result);
} else {
    jsonResponse(false, 'Lỗi tạo bài viết');
}

?>
