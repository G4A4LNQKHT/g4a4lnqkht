<?php
/**
 * ADMIN/API/ADD-POST.PHP - TẠỌO BÀI VIẾT
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

// Kiểm tra quyền admin
if (!isAdmin()) {
    jsonResponse('error', 'Bạn không có quyền!');
}

// Lấy dữ liệu
$title = getPost('title', '');
$content = getPost('content', '');
$category = getPost('category', 'news');
$status = getPost('status', 'draft');
$image = null;

// Validate
$errors = [];
if (empty($title)) $errors[] = 'Tiêu đề không được để trống';
if (empty($content)) $errors[] = 'Nội dung không được để trống';

if (!empty($errors)) {
    jsonResponse('error', implode(', ', $errors));
}

// Xử lý upload ảnh (tuỳ chọn)
if (!empty($_FILES['image']['name'])) {
    $file = $_FILES['image'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        jsonResponse('error', 'Chỉ cho phép ảnh (jpg, png, gif)');
    }
    
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB
        jsonResponse('error', 'Ảnh quá lớn (tối đa 5MB)');
    }
    
    $new_filename = uniqid() . '.' . $ext;
    $upload_dir = __DIR__ . '/../../public_html/uploads/';
    
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
    jsonResponse('success', 'Bài viết đã được tạo!', $result);
} else {
    jsonResponse('error', 'Lỗi tạo bài viết');
}

?>
