<?php
/**
 * ADMIN/API/ADD-TASK.PHP - TẠO CÔNG VIỆC
 */

require_once __DIR__ . '/../../../includes/config.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/db.php';

requireAdmin();

$title = getPost('title', '');
$description = getPost('description', '');
$due_date = getPost('due_date', '');
$priority = getPost('priority', 'medium');
$assigned_to = !empty(getPost('assigned_to', '')) ? (int)getPost('assigned_to', '') : null;

// Validate
$errors = [];
if (empty($title)) $errors[] = 'Tiêu đề không được để trống';
if (empty($due_date)) $errors[] = 'Hạn chót không được để trống';

if (!empty($errors)) {
    jsonResponse(false, implode(', ', $errors));
}

$result = createTask(
    $title,
    $description,
    $due_date,
    $priority,
    $assigned_to,
    getCurrentUserId()
);

if ($result['success']) {
    logAction(getCurrentUserId(), 'Tạo công việc', $title);
    jsonResponse(true, 'Công việc đã được tạo', $result);
} else {
    jsonResponse(false, 'Lỗi tạo công việc');
}

?>
