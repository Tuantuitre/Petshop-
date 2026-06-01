<?php

require_once __DIR__ . '/../includes/db.php';   // cung cấp $pdo
require_once __DIR__ . '/products_model.php';

class ProductDAO {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Lấy danh sách sản phẩm theo bộ lọc.
     * Trả về mảng các Product object.
     *
     * @param string $cat       danh mục ('all' = tất cả)
     * @param string $sort      kiểu sắp xếp
     * @param int    $price_min giá từ
     * @param int    $price_max giá đến
     * @return Product[]
     */
    public function getProducts(
        string $cat       = 'all',
        string $sort      = 'default',
        int    $price_min = 0,
        int    $price_max = 9999999
    ): array {
        $sql    = "SELECT sp.*, l.ten_loai
                   FROM san_pham sp
                   LEFT JOIN loai_thu_cung l ON l.id = sp.loai_id
                   WHERE sp.gia BETWEEN :pmin AND :pmax";
        $params = [':pmin' => $price_min, ':pmax' => $price_max];

        if ($cat !== 'all') {
            $sql .= " AND sp.danh_muc = :cat";
            $params[':cat'] = $cat;
        }

        $sql .= match ($sort) {
            'name_az'    => " ORDER BY sp.ten_san_pham ASC",
            'name_za'    => " ORDER BY sp.ten_san_pham DESC",
            'price_asc'  => " ORDER BY sp.gia ASC",
            'price_desc' => " ORDER BY sp.gia DESC",
            default      => " ORDER BY sp.id DESC",
        };

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($r) => $this->mapProduct($r), $rows);
    }

    public function getProductById(int $id): ?Product {
        $stmt = $this->pdo->prepare(
            "SELECT sp.*, l.ten_loai
             FROM san_pham sp
             LEFT JOIN loai_thu_cung l ON l.id = sp.loai_id
             WHERE sp.id = :id
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapProduct($row) : null;
    }

    private function mapProduct(array $r): Product {
        return new Product(
            (int)$r['id'],
            $r['ten_san_pham']  ?? '',
            $r['mo_ta']         ?? '',
            (float)$r['gia'],
            $r['hinh_anh']      ?? '',
            $r['danh_muc']      ?? '',
            (int)($r['so_luong'] ?? 0),
            (int)($r['loai_id'] ?? 0),
            $r['ten_loai']      ?? '',
            (int)($r['luot_mua'] ?? 0)
        );
    }
}
