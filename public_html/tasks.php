<?php
/**
 * TASKS.PHP - CÔNG VIỆC, LỊCH TRÌNH VÀ THỜI KHÓA BIỂU
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$page_title = 'Công việc & Lịch trình';

// Lấy tất cả tasks
$tasks = getAllTasks();

// Lấy thời khóa biểu
$schedules = getAllSchedules();

// Phân loại tasks theo status
$tasks_by_status = [
    'pending' => [],
    'in_progress' => [],
    'completed' => [],
    'cancelled' => []
];

foreach ($tasks as $task) {
    $tasks_by_status[$task['status']][] = $task;
}

// Days of week
$days = ['Mon' => 'Thứ 2', 'Tue' => 'Thứ 3', 'Wed' => 'Thứ 4', 'Thu' => 'Thứ 5', 'Fri' => 'Thứ 6', 'Sat' => 'Thứ 7', 'Sun' => 'Chủ nhật'];
$schedules_by_day = [];
foreach ($schedules as $s) {
    $schedules_by_day[$s['day_of_week']][] = $s;
}

?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="section-header">
    <h1><i class="fas fa-tasks"></i> Công việc & Lịch trình</h1>
    <p>Quản lý công việc, lịch trình và thời khóa biểu</p>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab">
            <i class="fas fa-tasks"></i> Công việc
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab">
            <i class="fas fa-calendar"></i> Thời khóa biểu
        </button>
    </li>
</ul>

<div class="tab-content">
    <!-- TAB 1: CÔNG VIỆC -->
    <div class="tab-pane fade show active" id="tasks" role="tabpanel">
        <?php if (isLoggedIn() && isAdmin()): ?>
        <div class="mb-4">
            <a href="<?php echo BASE_URL; ?>admin/manage-tasks.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tạo công việc mới
            </a>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Pending Tasks -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-hourglass-start"></i> Chờ xử lý
                            <span class="badge bg-light text-dark ms-2"><?php echo count($tasks_by_status['pending']); ?></span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($tasks_by_status['pending'])): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($tasks_by_status['pending'] as $task): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div style="flex: 1;">
                                        <strong><?php echo escape($task['title']); ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> 
                                            <?php echo formatDate($task['due_date'], 'd/m/Y'); ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> 
                                            <?php echo $task['assigned_name'] ? escape($task['assigned_name']) : 'Chung'; ?>
                                        </small>
                                    </div>
                                    <span class="badge badge-priority-<?php echo $task['priority']; ?>">
                                        <?php 
                                        $priorities = ['high' => 'Cao', 'medium' => 'Bình', 'low' => 'Thấp'];
                                        echo $priorities[$task['priority']] ?? $task['priority'];
                                        ?>
                                    </span>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <p class="text-muted text-center py-3 mb-0">Không có công việc chờ xử lý</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- In Progress Tasks -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-spinner"></i> Đang xử lý
                            <span class="badge bg-light text-dark ms-2"><?php echo count($tasks_by_status['in_progress']); ?></span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($tasks_by_status['in_progress'])): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($tasks_by_status['in_progress'] as $task): ?>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div style="flex: 1;">
                                        <strong><?php echo escape($task['title']); ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> 
                                            <?php echo formatDate($task['due_date'], 'd/m/Y'); ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> 
                                            <?php echo $task['assigned_name'] ? escape($task['assigned_name']) : 'Chung'; ?>
                                        </small>
                                    </div>
                                    <span class="badge badge-priority-<?php echo $task['priority']; ?>">
                                        <?php 
                                        $priorities = ['high' => 'Cao', 'medium' => 'Bình', 'low' => 'Thấp'];
                                        echo $priorities[$task['priority']] ?? $task['priority'];
                                        ?>
                                    </span>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <p class="text-muted text-center py-3 mb-0">Không có công việc đang xử lý</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Completed Tasks -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-check-circle"></i> Hoàn thành
                            <span class="badge bg-light text-dark ms-2"><?php echo count($tasks_by_status['completed']); ?></span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($tasks_by_status['completed'])): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach (array_slice($tasks_by_status['completed'], 0, 5) as $task): ?>
                            <li class="list-group-item" style="opacity: 0.7;">
                                <strong>
                                    <s><?php echo escape($task['title']); ?></s>
                                </strong>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> 
                                    <?php echo $task['assigned_name'] ? escape($task['assigned_name']) : 'Chung'; ?>
                                </small>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <p class="text-muted text-center py-3 mb-0">Chưa có công việc hoàn thành</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Cancelled Tasks -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-times-circle"></i> Hủy bỏ
                            <span class="badge bg-light text-dark ms-2"><?php echo count($tasks_by_status['cancelled']); ?></span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($tasks_by_status['cancelled'])): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($tasks_by_status['cancelled'] as $task): ?>
                            <li class="list-group-item" style="opacity: 0.6;">
                                <strong>
                                    <s><?php echo escape($task['title']); ?></s>
                                </strong>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <p class="text-muted text-center py-3 mb-0">Không có công việc bị hủy</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- TAB 2: THỜI KHÓA BIỂU -->
    <div class="tab-pane fade" id="schedule" role="tabpanel">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Ngày</th>
                        <th>Tiết</th>
                        <th>Môn học</th>
                        <th>Phòng</th>
                        <th>Giáo viên</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($schedules)): ?>
                        <?php foreach ($schedules as $s): ?>
                        <tr>
                            <td>
                                <strong><?php echo $days[$s['day_of_week']] ?? $s['day_of_week']; ?></strong>
                            </td>
                            <td><?php echo $s['period']; ?></td>
                            <td><?php echo escape($s['subject']); ?></td>
                            <td><?php echo escape($s['location']); ?></td>
                            <td><?php echo escape($s['teacher_name']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle"></i> Chưa cập nhật thời khóa biểu
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
