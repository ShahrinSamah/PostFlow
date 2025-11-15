<?php
declare(strict_types=1);

// autoload
require __DIR__ . '/../vendor/autoload.php';

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$key, $val] = array_map('trim', explode('=', $line, 2) + [1=>null]);
        if ($key && $val !== null) {
            putenv("$key=$val");
            $_ENV[$key] = $val;
        }
    }
}

use App\Core\Router;
use App\Core\Session;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\PostController; 
Session::start();

$router = new Router();
$auth = new AuthController();
$dash = new DashboardController();
$postController = new PostController(); 

$router->get('/', fn() => $auth->showLogin());
$router->get('/login', fn() => $auth->showLogin());
$router->get('/register', fn() => $auth->showRegister());
$router->get('/dashboard', fn() => $dash->index());
$router->get('/test-mail', fn() => $dash->testMail());


$router->get('/posts', fn() => $postController->showPosts());
$router->get('/create-post', fn() => $postController->showCreatePost());
$router->post('/create-post', fn() => $postController->createPost());


$router->get('/edit-post', fn() => $postController->showEditPost());
$router->post('/update-post', fn() => $postController->updatePost());
$router->post('/delete-post', fn() => $postController->deletePost());


$router->post('/register', fn() => $auth->register());
$router->post('/login', fn() => $auth->login());
$router->get('/logout', fn() => $auth->logout());

$router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');