<?php
/**
 * blog_service.php
 * Chứa logic nghiệp vụ: gọi DAO lấy bài viết.
 */

require_once __DIR__ . '/blog_dao.php';
require_once __DIR__ . '/blog_model.php';

class BlogService {

    private BlogDAO $dao;

    public function __construct(BlogDAO $dao) {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách bài viết.
     * Nếu DB chưa có bảng hoặc lỗi, fallback về dữ liệu tĩnh từ functions.php.
     *
     * @return BlogPost[]
     */
    public function getAllPosts(): array {
        try {
            $posts = $this->dao->getAllPosts();

            // Nếu DB trả về rỗng, dùng dữ liệu tĩnh từ functions.php (nếu có)
            if (empty($posts) && function_exists('getPetBlogPosts')) {
                return $this->mapFromFunctions();
            }

            return $posts;

        } catch (Exception $e) {
            // Fallback dữ liệu tĩnh nếu DB lỗi
            if (function_exists('getPetBlogPosts')) {
                return $this->mapFromFunctions();
            }
            return [];
        }
    }

    /** Map dữ liệu tĩnh từ getPetBlogPosts() sang BlogPost object */
    private function mapFromFunctions(): array {
        return array_map(fn($p) => new BlogPost(
            (int)($p['id']      ?? 0),
            $p['title']         ?? '',
            $p['excerpt']       ?? '',
            $p['img']           ?? '',
            $p['date']          ?? ''
        ), getPetBlogPosts());
    }
}