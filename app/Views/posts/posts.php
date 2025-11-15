<?php
use App\Core\Session;
use App\Models\Post;
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

<div class="page-header">
    <h2>Posts</h2>
    <a href="/create-post" class="create-post-btn">
        + Create Post
    </a>
</div>

<?php if (empty($posts)): ?>
    <div class="text-center" style="padding: 40px;">
        <p style="color: var(--text-muted);">No posts yet. Be the first to create a post!</p>
    </div>
<?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <?php foreach ($posts as $post): 
            $isLiked = Post::isLikedByUser($user['id'], $post['id']);
            $comments = Post::getComments($post['id']);
        ?>
            <div class="post-container">
                <div class="post-header-section">
                    <div class="post-user-info">
                        <strong class="post-user-name"><?= htmlspecialchars($post['user_name']) ?></strong>
                        <span class="post-timestamp">
                            <?= date('M j, Y g:i A', strtotime($post['created_at'])) ?>
                        </span>
                    </div>
                    
                    <!-- Edit/Delete Buttons -->
                    <?php if (isset($user) && $post['user_id'] == $user['id']): ?>
                        <div class="post-actions">
                            <!-- Edit Button -->
                            <a href="/edit-post?id=<?= $post['id'] ?>" class="post-action-btn">
                                Edit
                            </a>
                            
                            <!-- Delete Button -->
                            <form method="POST" action="/delete-post" style="display: inline;">
                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                <button type="submit" 
                                        class="post-action-btn post-action-delete"
                                        onclick="return confirm('Are you sure you want to delete this post?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                
                <p class="post-content-text">
                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                </p>
                
                <?php if ($post['image']): ?>
                    <div class="post-image-container">
                        <img src="http://localhost:8000/public/uploads/<?= htmlspecialchars($post['image']) ?>" 
                             alt="" 
                             class="post-image-style"
                             onerror="this.style.display='none'">
                    </div>
                <?php endif; ?>

                <!-- Like/Comment Section -->
                <div class="like-comment-section">
                    <!-- Like Button and Count -->
                    <div class="like-section">
                        <form method="POST" action="/toggle-like" style="display: inline;">
                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                            <button type="submit" class="like-btn" style="color: <?= $isLiked ? '#ef4444' : 'var(--text-muted)' ?>;">
                                <span style="color: <?= $isLiked ? '#ef4444' : 'var(--text-muted)' ?>; font-size: 16px;">♥</span> Like
                            </button>
                        </form>
                        <span class="like-count">
                            <?= $post['likes_count'] ?? 0 ?> likes
                        </span>
                        <span class="comment-count">
                            <?= $post['comments_count'] ?? 0 ?> comments
                        </span>
                    </div>

                    <!-- Comments Display -->
                    <?php if (!empty($comments)): ?>
                        <div style="background: var(--bg-tertiary); border-radius: 6px; padding: 12px; margin-bottom: 12px;">
                            <h4 style="margin: 0 0 8px 0; font-size: 14px; color: var(--text-primary);">Comments:</h4>
                            <?php foreach ($comments as $comment): ?>
                                <div style="margin-bottom: 8px; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                        <div style="flex: 1;">
                                            <strong style="font-size: 13px; color: var(--text-primary);"><?= htmlspecialchars($comment['user_name']) ?></strong>
                                            <span style="font-size: 11px; color: var(--text-muted); margin-left: 6px;">
                                                <?= date('M j, g:i A', strtotime($comment['created_at'])) ?>
                                            </span>
                                            <p style="margin: 4px 0 0 0; font-size: 13px; color: var(--text-primary); line-height: 1.4;">
                                                <?= nl2br(htmlspecialchars($comment['content'])) ?>
                                            </p>
                                        </div>
                                        
                                        <!-- Delete Comment Button -->
                                        <?php if (isset($user) && $comment['user_id'] == $user['id']): ?>
                                            <form method="POST" action="/delete-comment" style="margin-left: 8px;">
                                                <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                                <button type="submit" 
                                                        style="background: #ef4444; color: white; border: none; border-radius: 4px; padding: 2px 6px; font-size: 10px; cursor: pointer;"
                                                        onclick="return confirm('Delete this comment?')">
                                                    ×
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Comment Form -->
                    <form method="POST" action="/add-comment" class="comment-form">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <input type="text" 
                               name="comment_content" 
                               placeholder="Write a comment" 
                               class="comment-input"
                               autocomplete="off"
                               required>
                        <button type="submit" class="comment-submit">
                            Post
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';