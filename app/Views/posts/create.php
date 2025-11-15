<?php
use App\Core\Session;
$title = 'Create Post | AuthBoard';
ob_start();
?>

<?php if (Session::get('error')): ?>
    <div class="message error">
        <?= htmlspecialchars(Session::get('error')) ?>
        <?php Session::remove('error'); ?>
    </div>
<?php endif; ?>

<h2>Create New Post</h2>

<form method="POST" action="/create-post" enctype="multipart/form-data" class="form" style="max-width: 100%;">
    <div>
        <label for="content">Post Content *</label>
        <textarea id="content" name="content" rows="4" 
                  placeholder="What's on your mind?" 
                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit;"
                  required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
        <small style="color: #6b7280; font-size: 12px;">Maximum 255 characters</small>
    </div>

    <div>
        <label for="image">Image (Optional)</label>
        <input type="file" id="image" name="image" 
               accept="image/jpeg,image/png,image/gif"
               style="padding: 8px; border: 1px dashed #ddd; border-radius: 6px; width: 100%;">
        <small style="color: #6b7280; font-size: 12px;">Supported formats: JPG, PNG, GIF</small>
    </div>

    <div style="display: flex; gap: 12px; margin-top: 16px;">
        <button type="submit" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer;">
            Create Post
        </button>
        <a href="/posts" style="padding: 10px 20px; background: #6b7280; color: white; text-decoration: none; border-radius: 6px; display: inline-flex; align-items: center;">
            Cancel
        </a>
    </div>
</form>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';