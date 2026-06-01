<?php

require_once __DIR__ . '/../includes/db.php';   // $pdo
require_once __DIR__ . '/blog_model.php';

class BlogDAO {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lấy toàn bộ bài viết, sắp xếp mới nhất lên trước.
     * @return BlogPost[]
     */
    public function getAllPosts(): array {
        $rows = $this->pdo
            ->query("SELECT id, tieu_de, mo_ta_ngan, hinh_anh, ngay_dang
                     FROM bai_viet
                     ORDER BY ngay_dang DESC")
            ->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($r) => new BlogPost(
            (int)$r['id'],
            $r['tieu_de']    ?? '',
            $r['mo_ta_ngan'] ?? '',
            $r['hinh_anh']   ?? '',
            $r['ngay_dang']  ?? ''
        ), $rows);
    }
}