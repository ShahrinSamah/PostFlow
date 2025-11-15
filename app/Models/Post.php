<?php
namespace App\Models;

use PDO;

class Post {
    private static function connect(): PDO {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $db = getenv('DB_NAME') ?: 'metro_web_class';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    }

    public static function create(int $userId, string $content, ?string $image = null): int {
        $stmt = self::connect()->prepare('INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $content, $image]);
        return (int)self::connect()->lastInsertId();
    }

    public static function getAllWithUsers(): array {
        $stmt = self::connect()->prepare('
            SELECT p.*, u.name as user_name 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function findByUserId(int $userId): array {
        $stmt = self::connect()->prepare('SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // NEW: Find single post by ID
    public static function find(int $postId): ?array {
        $stmt = self::connect()->prepare('
            SELECT p.*, u.name as user_name 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.id = ?
        ');
        $stmt->execute([$postId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // NEW: Update post
    public static function update(int $postId, int $userId, string $content, ?string $image = null): bool {
        if ($image !== null) {
            $stmt = self::connect()->prepare('UPDATE posts SET content = ?, image = ? WHERE id = ? AND user_id = ?');
            return $stmt->execute([$content, $image, $postId, $userId]);
        } else {
            $stmt = self::connect()->prepare('UPDATE posts SET content = ? WHERE id = ? AND user_id = ?');
            return $stmt->execute([$content, $postId, $userId]);
        }
    }

    // NEW: Delete post
    public static function delete(int $postId, int $userId): bool {
        $stmt = self::connect()->prepare('DELETE FROM posts WHERE id = ? AND user_id = ?');
        return $stmt->execute([$postId, $userId]);
    }

    // NEW: Check if user owns the post
    public static function isOwner(int $postId, int $userId): bool {
        $stmt = self::connect()->prepare('SELECT id FROM posts WHERE id = ? AND user_id = ?');
        $stmt->execute([$postId, $userId]);
        return (bool) $stmt->fetch();
    }

    // NEW: Like/Unlike methods
    public static function toggleLike(int $userId, int $postId): bool {
        $pdo = self::connect();
        
        // Check if already liked
        $stmt = $pdo->prepare('SELECT id FROM likes WHERE user_id = ? AND post_id = ?');
        $stmt->execute([$userId, $postId]);
        
        if ($stmt->fetch()) {
            // Unlike
            $stmt = $pdo->prepare('DELETE FROM likes WHERE user_id = ? AND post_id = ?');
            $stmt->execute([$userId, $postId]);
            return false;
        } else {
            // Like
            $stmt = $pdo->prepare('INSERT INTO likes (user_id, post_id) VALUES (?, ?)');
            $stmt->execute([$userId, $postId]);
            return true;
        }
    }

    public static function getLikesCount(int $postId): int {
        $stmt = self::connect()->prepare('SELECT COUNT(*) as count FROM likes WHERE post_id = ?');
        $stmt->execute([$postId]);
        return (int) $stmt->fetch()['count'];
    }

    public static function isLikedByUser(int $userId, int $postId): bool {
        $stmt = self::connect()->prepare('SELECT id FROM likes WHERE user_id = ? AND post_id = ?');
        $stmt->execute([$userId, $postId]);
        return (bool) $stmt->fetch();
    }

    // NEW: Comment methods
    public static function addComment(int $userId, int $postId, string $content): int {
        $stmt = self::connect()->prepare('INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $postId, $content]);
        return (int) self::connect()->lastInsertId();
    }

    public static function getComments(int $postId): array {
        $stmt = self::connect()->prepare('
            SELECT c.*, u.name as user_name 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.post_id = ? 
            ORDER BY c.created_at ASC
        ');
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }

    public static function getCommentCount(int $postId): int {
        $stmt = self::connect()->prepare('SELECT COUNT(*) as count FROM comments WHERE post_id = ?');
        $stmt->execute([$postId]);
        return (int) $stmt->fetch()['count'];
    }

    public static function deleteComment(int $commentId, int $userId): bool {
        $stmt = self::connect()->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?');
        return $stmt->execute([$commentId, $userId]);
    }

    // NEW: Get posts with like and comment counts
    public static function getAllWithCounts(): array {
        $stmt = self::connect()->prepare('
            SELECT p.*, u.name as user_name,
                   COUNT(DISTINCT l.id) as likes_count,
                   COUNT(DISTINCT c.id) as comments_count
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            LEFT JOIN likes l ON p.id = l.post_id
            LEFT JOIN comments c ON p.id = c.post_id
            GROUP BY p.id
            ORDER BY p.created_at DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll();
    }
}