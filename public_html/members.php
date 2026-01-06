<?php
/**
 * MEMBERS.PHP - DANH SÁCH THÀNH VIÊN
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$page_title = 'Danh sách thành viên';

// Lấy danh sách thành viên
$users = getAllUsers();

?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="section-header">
    <div class="row align-items-center">
        <div class="col">
            <h1><i class="fas fa-users"></i> Danh sách thành viên Tổ 4</h1>
            <p>Các bạn trong đội ngũ tổ 4 lớp A4</p>
        </div>
        <div class="col-auto">
            <span class="badge badge-light" style="font-size: 1rem;">
                <i class="fas fa-users"></i> <?php echo count($users); ?> thành viên
            </span>
        </div>
    </div>
</div>

<?php if (!empty($users)): ?>
<div class="row">
    <?php foreach ($users as $user): ?>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100 text-center">
            <?php if (!empty($user['avatar'])): ?>
                <img src="<?php echo escape($user['avatar']); ?>" class="card-img-top" alt="<?php echo escape($user['full_name']); ?>" style="height: 250px; object-fit: cover;">
            <?php else: ?>
                <div style="height: 250px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); display: flex; align-items: center; justify-content: center; color: white; font-size: 60px;">
                    <i class="fas fa-user-circle"></i>
                </div>
            <?php endif; ?>
            
            <div class="card-body">
                <h5 class="card-title"><?php echo escape($user['full_name']); ?></h5>
                
                <?php if (!empty($user['class_position'])): ?>
                <p class="card-text">
                    <span class="badge" style="background-color: var(--primary-color); color: white;">
                        <?php echo escape($user['class_position']); ?>
                    </span>
                </p>
                <?php endif; ?>
                
                <p class="card-text text-muted" style="font-size: 0.9rem;">
                    <i class="fas fa-user"></i> @<?php echo escape($user['username']); ?>
                </p>
                
                <?php if (!empty($user['email'])): ?>
                <p class="card-text text-muted" style="font-size: 0.9rem;">
                    <i class="fas fa-envelope"></i> 
                    <a href="mailto:<?php echo escape($user['email']); ?>" style="color: inherit;">
                        <?php echo escape($user['email']); ?>
                    </a>
                </p>
                <?php endif; ?>
                
                <small class="text-muted">
                    <i class="fas fa-calendar"></i> Tham gia: <?php echo formatDate($user['created_at'], 'd/m/Y'); ?>
                </small>
            </div>
            
            <?php if (isLoggedIn()): ?>
            <div class="card-footer bg-light">
                <a href="<?php echo BASE_URL; ?>profile.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary w-100">
                    <i class="fas fa-eye"></i> Xem hồ sơ
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> Chưa có thành viên nào.
</div>
<?php endif; ?>

<!-- Thông tin tổ -->
<div class="row mt-5">
    <div class="col-lg-8 mx-auto">
        <div class="card bg-light border-top-primary">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-info-circle"></i> Về Tổ 4
                </h5>
                <p class="card-text">
                    Tổ 4 là một cộng đồng gồm các bạn học sinh lớp A4 năng động, thân thiện và tích cực. 
                    Chúng mình cùng nhau học tập, chia sẻ kinh nghiệm, giúp đỡ lẫn nhau và tạo ra những 
                    kỷ niệm đáng quý.
                </p>
                <p class="card-text">
                    Mỗi thành viên đều có giá trị riêng và đóng góp quan trọng cho sự phát triển của tổ. 
                    Chúng ta cùng nhau vượt qua những thử thách, chia sẻ niềm vui và xây dựng một tổ 4 
                    mạnh mẽ!
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
