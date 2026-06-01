<?php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/index_model.php';

class IndexDAO {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * @return HomeProduct[]
     */
    public function getTopProducts(int $limit = 4): array {
        $limit = max(1, min($limit, 12));

        $stmt = $this->pdo->prepare(
            "SELECT id,
                    ten_san_pham AS name,
                    gia AS price,
                    mo_ta AS `desc`,
                    danh_muc AS cat,
                    hinh_anh AS img,
                    COALESCE(luot_mua, 0) AS luot_mua
             FROM san_pham
             ORDER BY luot_mua DESC, id DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(
            fn($row) => HomeProduct::fromArray($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /**
     * @return HomeBlogPost[]
     */
    public function getTopBlogPosts(int $limit = 3): array {
        $limit = max(1, min($limit, 12));

        $stmt = $this->pdo->prepare(
            "SELECT id,
                    tieu_de AS title,
                    noi_dung_chinh AS excerpt,
                    hinh_anh_1 AS img,
                    COALESCE(luot_doc, 0) AS luot_doc,
                    DATE_FORMAT(ngay_dang, '%d/%m/%Y') AS date
             FROM tin_tuc
             ORDER BY luot_doc DESC, ngay_dang DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(
            fn($row) => HomeBlogPost::fromArray($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }
}
