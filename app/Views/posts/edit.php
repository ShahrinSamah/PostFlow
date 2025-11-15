<?php
use App\Core\Session;
$title = 'Edit Post | PostFlow';
ob_start();
?>

<div style="max-width: 600px; margin: 0 auto;">
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 32px;">
        <div style="margin-bottom: 32px;">
            <h2 style="font-size: 24px; font-weight: bold; color: #111827; margin-bottom: 8px;">Edit Post</h2>
            <p style="color: #6b7280;">Update your post content and image</p>
        </div>

        <?php if (Session::get('error')): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px;">
                <?= htmlspecialchars(Session::get('error')) ?>
                <?php Session::remove('error'); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/update-post" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 24px;">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            
            <div>
                <label style="display: block; font-weight: 500; color: #374151; margin-bottom: 8px;">Post Content</label>
                <textarea name="content" rows="4" 
                          style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px;"
                          placeholder="What's on your mind?"
                          required><?= htmlspecialchars($post['content']) ?></textarea>
                <p style="color: #6b7280; font-size: 12px; margin-top: 4px;">Maximum 255 characters</p>
            </div>

            <!-- Current Image -->
            <?php if ($post['image']): ?>
                <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px;">
                    <label style="display: block; font-weight: 500; color: #374151; margin-bottom: 12px;">Current Image</label>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <img src="http://localhost:8000/public/uploads/<?= htmlspecialchars($post['image']) ?>" 
                             alt="Current post image" 
                             style="max-width: 100%; max-height: 200px; border-radius: 6px; border: 1px solid #e5e7eb; object-fit: contain;">
                        <div>
                            <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                <input type="checkbox" id="remove_image" name="remove_image" value="1" style="margin-right: 8px;">
                                <label for="remove_image" style="color: #374151; font-size: 14px;">Remove current image</label>
                            </div>
                            <p style="color: #6b7280; font-size: 12px; margin: 0;">Check this box to remove the current image from your post.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- New Image Upload -->
            <div>
                <label style="display: block; font-weight: 500; color: #374151; margin-bottom: 8px;">
                    <?= $post['image'] ? 'Replace Image' : 'Add Image (Optional)' ?>
                </label>
                <input type="file" name="image" 
                       accept="image/jpeg,image/png,image/gif"
                       style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                <p style="color: #6b7280; font-size: 12px; margin-top: 4px;">Supported formats: JPG, PNG, GIF. Leave empty to keep current image.</p>
            </div>

            <div style="display: flex; gap: 16px; padding-top: 16px;">
                <button type="submit" style="background: #2563eb; color: white; padding: 10px 24px; border-radius: 6px; border: none; font-weight: 500;">
                    Update Post
                </button>
                <a href="/posts" style="background: #f3f4f6; color: #374151; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-weight: 500; border: 1px solid #d1d5db;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';