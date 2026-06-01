<?php
/**
 * products_control.php
 * Nhận request từ trình duyệt → gọi Service → truyền dữ liệu cho UI.
 */

require_once __DIR__ . '/../includes/db.php';   // $pdo
require_once __DIR__ . '/products_dao.php';
require_once __DIR__ . '/products_service.php';

// ── Đọc tham số từ GET ───────────────────────────────────────────────────────
$cat       = $_GET['cat']       ?? 'all';
$sort      = $_GET['sort']      ?? 'default';
$price_min = (int)($_GET['price_min'] ?? 0);
$price_max = (int)($_GET['price_max'] ?? 9999999);
$page      = (int)($_GET['page'] ?? 1);
$detail_id = (int)($_GET['detail'] ?? 0);

if ($price_max === 0) $price_max = 9999999;

// ── Khởi tạo DAO & Service ───────────────────────────────────────────────────
$dao     = new ProductDAO($pdo);
$service = new ProductService($dao);

// ── Lấy dữ liệu phân trang ───────────────────────────────────────────────────
$result      = $service->getPagedProducts($cat, $sort, $price_min, $price_max, $page);
$products    = $result['products'];
$paged       = $result['paged'];
$total       = $result['total'];
$total_pages = $result['total_pages'];
$page        = $result['page'];
$detailProduct = $service->getProductDetail($detail_id);

// ── Danh sách danh mục (dùng trong UI) ───────────────────────────────────────
$categories = ProductService::$categories;

// ── Nạp giao diện ────────────────────────────────────────────────────────────
require_once __DIR__ . '/products_ui.php';
