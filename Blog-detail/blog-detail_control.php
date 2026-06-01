<?php
/**
 * blog-detail_control.php
 * Nhận request → gọi BlogDetailService → truyền dữ liệu cho UI.
 * Không chứa HTML, không chứa SQL.
 */

require_once __DIR__ . '/../includes/db.php';           // $pdo
require_once __DIR__ . '/../includes/functions.php';    // fallback getBlogPostById, getFeaturedPosts
require_once __DIR__ . '/blog-detail_dao.php';
require_once __DIR__ . '/blog-detail_service.php';

// ── Đọc tham số ──────────────────────────────────────────────────────────────
$id = (int)($_GET['id'] ?? 1);

// ── Khởi tạo DAO & Service ───────────────────────────────────────────────────
$dao     = new BlogDetailDAO($pdo);
$service = new BlogDetailService($dao);

// ── Lấy bài viết — nếu không tìm thấy → về blog.php ─────────────────────────
$post = $service->getPostById($id);
if (!$post) {
    header('Location: blog.php');
    exit;
}

// ── Lấy bài viết nổi bật ─────────────────────────────────────────────────────
$featured = $service->getFeaturedPosts(4, $id);

// ── Nạp giao diện ────────────────────────────────────────────────────────────
require_once __DIR__ . '/blog-detail_ui.php';