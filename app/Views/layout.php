<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?? 'PostFlow ' ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>PostFlow</h1>
        <?php if (!empty($_SESSION['user'])): ?>
            <nav>
                <a href="/dashboard">Dashboard</a>
                <a href="/posts">Posts</a>
            </nav>
        <?php endif; ?>
    </header>

    <main>
        <?php echo $content ; ?>
    </main>

    <footer>
        <small>PostFlow-Connect.Share.Post</small>
    </footer>
</div>

<!-- Theme Toggle -->
<div class="theme-toggle">
    <button class="theme-toggle-btn" id="themeToggle" aria-label="Toggle theme"></button>
</div>

<script>
// Theme toggle functionality
const themeToggle = document.getElementById('themeToggle');
const html = document.documentElement;

// Get saved theme or default to light
const savedTheme = localStorage.getItem('theme') || 'light';
html.setAttribute('data-theme', savedTheme);

themeToggle.addEventListener('click', () => {
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
});

// Auto-detect system preference
if (!localStorage.getItem('theme')) {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    html.setAttribute('data-theme', prefersDark ? 'dark' : 'light');
}
</script>
</body>
</html>