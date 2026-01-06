<?php
/**
 * ADMIN/API/DELETE-POST.PHP - XÓA BÀI VIẾT
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

requireAdmin();

$post_id = (int)getPost('id', 0);

if ($post_id <= 0) {
    jsonResponse('error', 'ID bài viết không hợp lệ');
}

// Kiểm tra quyền (admin hoặc tác giả)
$post = getPostById($post_id);
if (!$post || ($post['author_id'] !== getCurrentUserId() && !isAdmin())) {
    jsonResponse('error', 'Bạn không có quyền xóa bài viết này');
}

if (deletePost($post_id)) {
    logAction(getCurrentUserId(), 'Xóa bài viết', "Bài viết #$post_id");
    jsonResponse('success', 'Bài viết đã được xóa');
} else {
    jsonResponse('error', 'Lỗi xóa bài viết');
}

?>
