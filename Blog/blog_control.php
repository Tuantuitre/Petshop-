<?php
/**
 * blog_control.php
 * Nhận request → gọi BlogService → truyền dữ liệu cho UI.
 */

require_once __DIR__ . '/../includes/db.php';       // $pdo
require_once __DIR__ . '/../includes/functions.php'; // getPetBlogPosts() fallback
require_once __DIR__ . '/blog_dao.php';
require_once __DIR__ . '/blog_service.php';

// ── Khởi tạo DAO & Service ───────────────────────────────────────────────────
$dao     = new BlogDAO($pdo);
$service = new BlogService($dao);

// ── Lấy danh sách bài viết ───────────────────────────────────────────────────
$posts = $service->getAllPosts();

// ── Nạp giao diện ────────────────────────────────────────────────────────────
require_once __DIR__ . '/blog_ui.php';