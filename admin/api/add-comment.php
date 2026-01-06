<?php
/**
 * ADMIN/API/ADD-COMMENT.PHP - THÊM BÌNH LUẬN
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

requireLogin();

$post_id = (int)getPost('post_id', 0);
$content = getPost('content', '');

if ($post_id <= 0) {
    jsonResponse('error', 'Bài viết không tồn tại');
}

if (empty($content)) {
    jsonResponse('error', 'Nội dung bình luận không được để trống');
}

if (strlen($content) > 5000) {
    jsonResponse('error', 'Bình luận quá dài (tối đa 5000 ký tự)');
}

$result = createComment(
    $post_id,
    getCurrentUserId(),
    $content,
    isAdmin() // Auto-approve nếu là admin
);

if ($result['success']) {
    logAction(getCurrentUserId(), 'Thêm bình luận', "Bài viết #$post_id");
    jsonResponse('success', 'Bình luận đã được thêm');
} else {
    jsonResponse('error', 'Lỗi thêm bình luận');
}

?>
