<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Post;

class PostController extends Controller {
    public function showPosts() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $posts = Post::getAllWithCounts();
        $this->view('posts/posts.php', [
            'user' => $user, 
            'posts' => $posts
        ]);
    }

    public function showCreatePost() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $this->view('posts/create.php', ['user' => $user]);
    }

    public function createPost() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $content = trim($_POST['content'] ?? '');
        $image = null;

        if (empty($content)) {
            Session::set('error', 'Post content is required.');
            header('Location: /create-post');
            exit;
        }

        if (strlen($content) > 255) {
            Session::set('error', 'Post content must be less than 255 characters.');
            header('Location: /create-post');
            exit;
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['image']['type'];
            
            if (in_array($fileType, $allowedTypes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/'; 
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . $user['id'] . '.' . $extension;
                $filepath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                    $image = $filename;
                }
            }
        }

        Post::create($user['id'], $content, $image);
        
        Session::set('success', 'Post created successfully!');
        header('Location: /posts');
        exit;
    }

    // NEW: Show edit post form
    public function showEditPost() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $postId = $_GET['id'] ?? null;
        if (!$postId) {
            Session::set('error', 'Post not found.');
            header('Location: /posts');
            exit;
        }

        $post = Post::find($postId);
        if (!$post) {
            Session::set('error', 'Post not found.');
            header('Location: /posts');
            exit;
        }

        // Check if user owns the post
        if ($post['user_id'] != $user['id']) {
            Session::set('error', 'You can only edit your own posts.');
            header('Location: /posts');
            exit;
        }

        $this->view('posts/edit.php', ['user' => $user, 'post' => $post]);
    }

    // NEW: Handle post update
    public function updatePost() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $postId = $_POST['post_id'] ?? null;
        $content = trim($_POST['content'] ?? '');
        $removeImage = isset($_POST['remove_image']) && $_POST['remove_image'] === '1';

        if (!$postId) {
            Session::set('error', 'Post not found.');
            header('Location: /posts');
            exit;
        }

        // Verify post exists and user owns it
        $post = Post::find($postId);
        if (!$post || $post['user_id'] != $user['id']) {
            Session::set('error', 'You can only edit your own posts.');
            header('Location: /posts');
            exit;
        }

        if (empty($content)) {
            Session::set('error', 'Post content is required.');
            header("Location: /edit-post?id=$postId");
            exit;
        }

        if (strlen($content) > 255) {
            Session::set('error', 'Post content must be less than 255 characters.');
            header("Location: /edit-post?id=$postId");
            exit;
        }

        $image = $post['image'];

        // Handle image removal
        if ($removeImage && $post['image']) {
            // Delete old image file
            $oldImagePath = __DIR__ . '/../../public/uploads/' . $post['image'];
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            $image = null;
        }

        // Handle new image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['image']['type'];
            
            if (in_array($fileType, $allowedTypes)) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                
                // Delete old image if exists
                if ($post['image']) {
                    $oldImagePath = $uploadDir . $post['image'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . $user['id'] . '.' . $extension;
                $filepath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                    $image = $filename;
                }
            }
        }

        // Update post
        Post::update($postId, $user['id'], $content, $image);
        
        Session::set('success', 'Post updated successfully!');
        header('Location: /posts');
        exit;
    }

    // NEW: Handle post deletion
    public function deletePost() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $postId = $_POST['post_id'] ?? null;
        if (!$postId) {
            Session::set('error', 'Post not found.');
            header('Location: /posts');
            exit;
        }

        // Verify post exists and user owns it
        $post = Post::find($postId);
        if (!$post || $post['user_id'] != $user['id']) {
            Session::set('error', 'You can only delete your own posts.');
            header('Location: /posts');
            exit;
        }

        // Delete image file if exists
        if ($post['image']) {
            $imagePath = __DIR__ . '/../../public/uploads/' . $post['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Delete post
        Post::delete($postId, $user['id']);
        
        Session::set('success', 'Post deleted successfully!');
        header('Location: /posts');
        exit;
    }

    // NEW: Handle like/unlike
    public function toggleLike() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $postId = $_POST['post_id'] ?? null;
        if (!$postId) {
            Session::set('error', 'Post not found.');
            header('Location: /posts');
            exit;
        }

        $isLiked = Post::toggleLike($user['id'], $postId);
        
        if ($isLiked) {
            Session::set('success', 'Post liked!');
        } else {
            Session::set('success', 'Post unliked!');
        }
        
        header('Location: /posts');
        exit;
    }

    // NEW: Handle comment submission
    public function addComment() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $postId = $_POST['post_id'] ?? null;
        $content = trim($_POST['comment_content'] ?? '');

        if (!$postId) {
            Session::set('error', 'Post not found.');
            header('Location: /posts');
            exit;
        }

        if (empty($content)) {
            Session::set('error', 'Comment cannot be empty.');
            header('Location: /posts');
            exit;
        }

        Post::addComment($user['id'], $postId, $content);
        
        Session::set('success', 'Comment added!');
        header('Location: /posts');
        exit;
    }

    // NEW: Handle comment deletion
    public function deleteComment() {
        $user = Session::get('user');
        if (!$user) {
            header('Location: /login');
            exit;
        }

        $commentId = $_POST['comment_id'] ?? null;
        if (!$commentId) {
            Session::set('error', 'Comment not found.');
            header('Location: /posts');
            exit;
        }

        $success = Post::deleteComment($commentId, $user['id']);
        
        if ($success) {
            Session::set('success', 'Comment deleted!');
        } else {
            Session::set('error', 'Failed to delete comment or you are not the owner.');
        }
        
        header('Location: /posts');
        exit;
    }
}