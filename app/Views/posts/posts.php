<?php
use App\Core\Session;
$title = 'Posts | PostFlow';
ob_start();
?>

<?php if (Session::get('success')): ?>
    <div class="message success">
        <?= htmlspecialchars(Session::get('success')) ?>
        <?php Session::remove('success'); ?>
    </div>
<?php endif; ?>

<?php if (Session::get('error')): ?>
    <div class="message error">
        <?= htmlspecialchars(Session::get('error')) ?>
        <?php Session::remove('error'); ?>
    </div>
<?php endif; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h2>Posts</h2>
    <a href="/create-post" style="padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-size: 14px;">
        + Create Post
    </a>
</div>

<?php if (empty($posts)): ?>
    <div style="text-align: center; padding: 40px; color: #6b7280;">
        <p>No posts yet. Be the first to create a post!</p>
    </div>
<?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <?php foreach ($posts as $post): ?>
            <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; background: white;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                    <div>
                        <strong style="color: #374151;"><?= htmlspecialchars($post['user_name']) ?></strong>
                        <span style="color: #6b7280; font-size: 12px; margin-left: 8px;">
                            <?= date('M j, Y g:i A', strtotime($post['created_at'])) ?>
                        </span>
                    </div>
                </div>
                
                <p style="margin: 0 0 12px 0; color: #374151; line-height: 1.5;">
                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                </p>
                
                <?php if ($post['image']): ?>
                    <div style="margin-top: 12px;">
                        <img src="http://localhost/metro_wb_lab-main/public/uploads/<?= htmlspecialchars($post['image']) ?>" 
                             alt="Post image" 
                             style="max-width: 100%; max-height: 300px; border-radius: 6px; border: 1px solid #e5e7eb;">
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';