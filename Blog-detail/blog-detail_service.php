<?php
/**
 * blog-detail_service.php
 * Chứa logic nghiệp vụ: lấy bài viết chi tiết + fallback functions.php.
 * Không có SQL, không có HTML.
 */

require_once __DIR__ . '/blog-detail_dao.php';
require_once __DIR__ . '/blog-detail_model.php';

class BlogDetailService {

    private BlogDetailDAO $dao;

    public function __construct(BlogDetailDAO $dao) {
        $this->dao = $dao;
    }

    // ── Lấy bài viết chi tiết theo id ───────────────────────────────────────
    // Fallback về functions.php nếu DB chưa có bảng
    public function getPostById(int $id): ?BlogDetail {
        try {
            $row = $this->dao->getPostById($id);
            if ($row) return new BlogDetail($row);
        } catch (Exception $e) {
            // fallback bên dưới
        }

        // Fallback dữ liệu tĩnh từ functions.php
        if (function_exists('getBlogPostById')) {
            $row = getBlogPostById($id);
            if ($row) return new BlogDetail($row);
        }

        return null;
    }

    // ── Lấy danh sách bài nổi bật ────────────────────────────────────────────
    /** @return FeaturedPost[] */
    public function getFeaturedPosts(int $limit = 4, int $exclude_id = 0): array {
        try {
            $posts = $this->dao->getFeaturedPosts($limit, $exclude_id);
            if (!empty($posts)) return $posts;
        } catch (Exception $e) {
            // fallback bên dưới
        }

        // Fallback dữ liệu tĩnh từ functions.php
        if (function_exists('getFeaturedPosts')) {
            $raw = getFeaturedPosts($limit);
            return array_map(fn($f) => new FeaturedPost(
                (int)($f['id']    ?? 0),
                $f['title']       ?? '',
                $f['date']        ?? '',
                $f['img']         ?? ''
            ), $raw);
        }

        return [];
    }
}