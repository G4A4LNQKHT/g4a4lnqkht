<?php
/**
 * POSTS.PHP - XEM BÀI VIẾT VÀ BÌNH LUẬN
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

$page_title = 'Bài viết';

// Lấy trang hiện tại (pagination)
$page = max(1, (int)getGet('page', 1));
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Nếu có ID bài viết, lấy bài viết chi tiết
$post_detail = null;
$comments = [];
$post_id = (int)getGet('id', 0);

if ($post_id > 0) {
    $post_detail = getPostById($post_id);
    
    if ($post_detail) {
        // Tăng view count
        incrementPostViews($post_id);
        
        // Lấy bình luận
        $comments = getCommentsByPost($post_id, true);
        
        $page_title = escape($post_detail['title']);
    } else {
        $post_detail = null;
    }
}

// Lấy danh sách bài viết
$posts = getAllPosts($per_page, $offset);

// Tổng số bài viết
$total_result = Database::getInstance()->getConnection()->query("SELECT COUNT(*) as total FROM posts WHERE status = 'published'");
$total_posts = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $per_page);

?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<div class="section-header">
    <h1><i class="fas fa-newspaper"></i> Tin tức & Bài viết</h1>
    <p>Thông báo, kỷ niệm và các bài viết từ Tổ 4</p>
</div>

<?php if ($post_detail): ?>
<!-- CHI TIẾT BÀI VIẾT -->
<div class="row mb-5">
    <div class="col-lg-8 mx-auto">
        <!-- Bài viết -->
        <article class="card mb-4">
            <?php if (!empty($post_detail['image'])): ?>
            <img src="<?php echo escape($post_detail['image']); ?>" class="card-img-top" alt="<?php echo escape($post_detail['title']); ?>" style="height: 400px; object-fit: cover;">
            <?php endif; ?>
            
            <div class="card-body">
                <div class="mb-3">
                    <span class="badge" style="background-color: var(--primary-color); color: white; padding: 0.5rem 1rem; font-size: 0.9rem;">
                        <?php 
                        $categories = [
                            'news' => 'Tin tức',
                            'memory' => 'Kỷ niệm',
                            'announcement' => 'Thông báo',
                            'other' => 'Khác'
                        ];
                        echo $categories[$post_detail['category']] ?? $post_detail['category'];
                        ?>
                    </span>
                </div>
                
                <h1 class="card-title mb-3"><?php echo escape($post_detail['title']); ?></h1>
                
                <div class="d-flex gap-3 mb-3 text-muted" style="flex-wrap: wrap;">
                    <span><i class="fas fa-user"></i> <?php echo escape($post_detail['author_name'] ?? 'Unknown'); ?></span>
                    <span><i class="fas fa-calendar"></i> <?php echo formatDate($post_detail['created_at'], 'd/m/Y H:i'); ?></span>
                    <span><i class="fas fa-eye"></i> <?php echo $post_detail['view_count']; ?> lượt xem</span>
                </div>
                
                <hr>
                
                <div class="card-text" style="line-height: 1.8; font-size: 1.05rem;">
                    <?php echo nl2br(escape($post_detail['content'])); ?>
                </div>
                
                <hr>
                
                <div class="d-flex gap-2">
                    <a href="<?php echo BASE_URL; ?>posts.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </article>
        
        <!-- BÌNH LUẬN -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-comments"></i> Bình luận (<?php echo count($comments); ?>)
                </h5>
            </div>
            
            <div class="card-body">
                <!-- Form bình luận -->
                <?php if (isLoggedIn()): ?>
                <form method="POST" action="<?php echo BASE_URL; ?>admin/api/add-comment.php" class="mb-4" id="commentForm">
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    
                    <div class="form-group">
                        <label for="comment_content" class="form-label">Viết bình luận</label>
                        <textarea 
                            class="form-control" 
                            id="comment_content" 
                            name="content" 
                            rows="3" 
                            placeholder="Chia sẻ ý kiến của bạn..." 
                            required
                        ></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Gửi bình luận
                    </button>
                </form>
                <?php else: ?>
                <div class="alert alert-info mb-4">
                    <i class="fas fa-lock"></i> 
                    <a href="<?php echo BASE_URL; ?>login.php">Đăng nhập</a> để bình luận
                </div>
                <?php endif; ?>
                
                <!-- Danh sách bình luận -->
                <?php if (!empty($comments)): ?>
                <div class="timeline">
                    <?php foreach ($comments as $comment): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong><?php echo escape($comment['author_name'] ?? 'Unknown'); ?></strong>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-clock"></i> <?php echo formatDate($comment['created_at'], 'd/m/Y H:i'); ?>
                                    </small>
                                </div>
                                <?php if (isAdmin() || (isLoggedIn() && getCurrentUserId() == $comment['author_id'])): ?>
                                <button class="btn btn-sm btn-danger" onclick="if(confirm('Xóa bình luận?')) deleteComment(<?php echo $comment['id']; ?>, <?php echo $post_id; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                            <p class="mb-0"><?php echo escape($comment['content']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted text-center py-4">
                    <i class="fas fa-comments"></i> Chưa có bình luận nào. Hãy là người bình luận đầu tiên!
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- DANH SÁCH BÀI VIẾT -->
<div class="row">
    <div class="col-lg-8 mx-auto">
        <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $post): ?>
        <div class="card mb-4">
            <div class="row g-0">
                <?php if (!empty($post['image'])): ?>
                <div class="col-md-4">
                    <img src="<?php echo escape($post['image']); ?>" class="img-fluid rounded-start h-100" alt="<?php echo escape($post['title']); ?>" style="object-fit: cover;">
                </div>
                <?php endif; ?>
                
                <div class="col-md-<?php echo !empty($post['image']) ? '8' : '12'; ?>">
                    <div class="card-body">
                        <div class="mb-2">
                            <span class="badge" style="background-color: var(--primary-color); color: white;">
                                <?php 
                                $categories = [
                                    'news' => 'Tin tức',
                                    'memory' => 'Kỷ niệm',
                                    'announcement' => 'Thông báo',
                                    'other' => 'Khác'
                                ];
                                echo $categories[$post['category']] ?? $post['category'];
                                ?>
                            </span>
                        </div>
                        
                        <h5 class="card-title">
                            <a href="<?php echo BASE_URL; ?>posts.php?id=<?php echo $post['id']; ?>" style="color: inherit; text-decoration: none;">
                                <?php echo escape($post['title']); ?>
                            </a>
                        </h5>
                        
                        <p class="card-text text-muted">
                            <?php echo escape(substr($post['content'], 0, 150)); ?>...
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.9rem;">
                            <div>
                                <i class="fas fa-user"></i> <?php echo escape($post['author_name'] ?? 'Unknown'); ?> 
                                • <i class="fas fa-calendar"></i> <?php echo formatDate($post['created_at'], 'd/m/Y'); ?>
                            </div>
                            <div>
                                <i class="fas fa-eye"></i> <?php echo $post['view_count']; ?> 
                                • <i class="fas fa-comments"></i> 
                                <span id="comment-count-<?php echo $post['id']; ?>">0</span>
                            </div>
                        </div>
                        
                        <a href="<?php echo BASE_URL; ?>posts.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary mt-2">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo BASE_URL; ?>posts.php?page=<?php echo $page - 1; ?>">
                        <i class="fas fa-chevron-left"></i> Trước
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo BASE_URL; ?>posts.php?page=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo BASE_URL; ?>posts.php?page=<?php echo $page + 1; ?>">
                        Sau <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Chưa có bài viết nào.
        </div>
        <?php endif; ?>
    </div>
</div>

<?php endif; ?>

<script>
function deleteComment(commentId, postId) {
    fetch('<?php echo BASE_URL; ?>admin/api/delete-comment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + commentId
    })
    .then(r => r.json())
    .then(d => {
        if (d.status === 'success') {
            location.reload();
        } else {
            alert('Lỗi: ' + d.message);
        }
    });
}

document.getElementById('commentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('<?php echo BASE_URL; ?>admin/api/add-comment.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(d => {
        if (d.status === 'success') {
            showSuccess('Bình luận đã được thêm!');
            setTimeout(() => location.reload(), 1000);
        } else {
            showError('Lỗi: ' + d.message);
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
