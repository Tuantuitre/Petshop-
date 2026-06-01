<?php
/**
 * blog-detail_dao.php
 * Chỉ chứa query DB — không có logic nghiệp vụ, không có HTML.
 */

require_once __DIR__ . '/../includes/db.php';       // $pdo
require_once __DIR__ . '/blog-detail_model.php';

class BlogDetailDAO {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // ── Lấy bài viết chi tiết theo id ───────────────────────────────────────
    public function getPostById(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT id, tieu_de AS title, ngay_dang AS date,
                    hinh_anh AS img, noi_dung AS content,
                    nguyen_nhan_pho_bien, hinh_anh_2,
                    huong_dan, hinh_anh_3,
                    cach_cham, hinh_anh_4
             FROM bai_viet WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ── Lấy danh sách bài viết nổi bật (loại trừ bài hiện tại) ─────────────
    /**
     * @return FeaturedPost[]
     */
    public function getFeaturedPosts(int $limit = 4, int $exclude_id = 0): array {
        $stmt = $this->pdo->prepare(
            "SELECT id, tieu_de AS title, ngay_dang AS date, hinh_anh AS img
             FROM bai_viet
             WHERE id != ?
             ORDER BY ngay_dang DESC
             LIMIT ?"
        );
        $stmt->execute([$exclude_id, $limit]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($r) => new FeaturedPost(
            (int)$r['id'],
            $r['title'] ?? '',
            $r['date']  ?? '',
            $r['img']   ?? ''
        ), $rows);
    }
}