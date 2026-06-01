<?php
/**
 * Chứa logic nghiệp vụ: gọi DAO, phân trang, build URL.
 */

require_once __DIR__ . '/products_dao.php';
require_once __DIR__ . '/products_model.php';

class ProductService {

    private ProductDAO $dao;

    // Danh sách danh mục — dùng chung cho UI và Service
    public static array $categories = [
        'all'                 => ['label' => 'Tất cả',           'emoji' => '🐾'],
        'dog-food'            => ['label' => 'Thức ăn chó',      'emoji' => '🦴'],
        'cat-food'            => ['label' => 'Thức ăn mèo',      'emoji' => '🐟'],
        'hamster-food'        => ['label' => 'Thức ăn hamster',  'emoji' => '🌾'],
        'dog-accessories'     => ['label' => 'Phụ kiện chó',     'emoji' => '🐕'],
        'cat-accessories'     => ['label' => 'Phụ kiện mèo',     'emoji' => '🐈'],
        'hamster-accessories' => ['label' => 'Phụ kiện hamster', 'emoji' => '🐹'],
    ];

    public function __construct(ProductDAO $dao) {
        $this->dao = $dao;
    }

    /**
     * Lấy sản phẩm + thông tin phân trang.
     *
     * @return array [
     *   'products'    => Product[],   // toàn bộ kết quả
     *   'paged'       => Product[],   // trang hiện tại
     *   'total'       => int,
     *   'total_pages' => int,
     *   'page'        => int,
     * ]
     */
    public function getPagedProducts(
        string $cat,
        string $sort,
        int    $price_min,
        int    $price_max,
        int    $page,
        int    $per_page = 8
    ): array {
        $products    = $this->dao->getProducts($cat, $sort, $price_min, $price_max);
        $total       = count($products);
        $total_pages = max(1, ceil($total / $per_page));
        $page        = max(1, min($page, $total_pages));
        $offset      = ($page - 1) * $per_page;
        $paged       = array_slice($products, $offset, $per_page);

        return compact('products', 'paged', 'total', 'total_pages', 'page');
    }

    public function getProductDetail(int $id): ?Product {
        if ($id <= 0) {
            return null;
        }

        return $this->dao->getProductById($id);
    }

    /**
     * Build URL 
     */
    public static function buildUrl(array $overrides = []): string {
        $params = array_merge($_GET, $overrides);
        $params = array_filter($params, fn($v) => $v !== '' && $v !== null);
        $query = http_build_query($params);

        return $query ? 'products.php?' . $query : 'products.php';
    }
}
