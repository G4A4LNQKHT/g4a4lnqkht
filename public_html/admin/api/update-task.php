<?php
/**
 * ADMIN/API/UPDATE-TASK.PHP - CẬP NHẬT TRẠNG THÁI CÔNG VIỆC
 */

require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/db.php';

requireAdmin();

$task_id = (int)getPost('id', 0);
$status = getPost('status', '');

if ($task_id <= 0) {
    jsonResponse(false, 'ID công việc không hợp lệ');
}

$allowed_status = ['pending', 'in_progress', 'completed', 'cancelled'];
if (!in_array($status, $allowed_status)) {
    jsonResponse(false, 'Trạng thái không hợp lệ');
}

if (updateTaskStatus($task_id, $status)) {
    logAction(getCurrentUserId(), 'Cập nhật công việc', "Công việc #$task_id → $status");
    jsonResponse(true, 'Trạng thái đã được cập nhật');
} else {
    jsonResponse(false, 'Lỗi cập nhật công việc');
}

?>
