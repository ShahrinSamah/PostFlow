<?php
use App\Core\Session;
$title = 'Dashboard | PostFlow';
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

<h2>Welcome, <?php echo  htmlspecialchars($user['name']) ?></h2>
<p>Your email: <?= htmlspecialchars($user['email']) ?></p>

<div class="dashboard-actions">
    <a href="/posts" class="btn btn-primary">
         View Posts
    </a>
    <a href="/create-post" class="btn btn-primary">
        + Create Post
    </a>
</div>

<div class="email-testing-box">
    <h3>Email Testing</h3>
    <p>
        Test your Mailtrap integration by sending a test email to <strong><?= htmlspecialchars($user['email']) ?></strong>
    </p>
    <a href="/test-mail" 
       class="btn btn-primary test-email-btn"
       onclick="return confirm('Send a test email to <?= htmlspecialchars($user['email']) ?>?');">
         Send Test Email
    </a>
    <p class="mailtrap-note">
         Check your <a href="https://mailtrap.io/inboxes" target="_blank">Mailtrap inbox</a> to see the email.
    </p>
</div>

<div class="logout-section">
    <a href="/logout" 
       class="btn btn-danger"
       onclick="return confirm('Are you sure you want to logout?');">
         Logout
    </a>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';