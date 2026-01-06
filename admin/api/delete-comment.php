<?php
/**
 * ADMIN/API/DELETE-COMMENT.PHP - XÓA BÌNH LUẬN
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

requireLogin();

$comment_id = (int)getPost('id', 0);

if ($comment_id <= 0) {
    jsonResponse('error', 'ID bình luận không hợp lệ');
}

// Lấy thông tin bình luận
$result = Database::getInstance()->getConnection()->query(
    "SELECT author_id FROM comments WHERE id = $comment_id"
);
$comment = $result->fetch_assoc();

if (!$comment) {
    jsonResponse('error', 'Bình luận không tồn tại');
}

// Kiểm tra quyền (admin hoặc tác giả)
if ($comment['author_id'] !== getCurrentUserId() && !isAdmin()) {
    jsonResponse('error', 'Bạn không có quyền xóa bình luận này');
}

if (deleteComment($comment_id)) {
    logAction(getCurrentUserId(), 'Xóa bình luận', "Bình luận #$comment_id");
    jsonResponse('success', 'Bình luận đã được xóa');
} else {
    jsonResponse('error', 'Lỗi xóa bình luận');
}

?>
