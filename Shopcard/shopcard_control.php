<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/shopcard_dao.php';
require_once __DIR__ . '/shopcard_service.php';

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
if (!$userId) {
    header('Location: dangnhap.php');
    exit;
}

$dao     = new ShopcardDAO($pdo);
$service = new ShopcardService($dao);

// ── Thêm sản phẩm vào giỏ ──────────────────────────────────────────
if (isset($_GET['add'])) {
    $service->addToCart((int)$_GET['add']);
    header('Location: shopcard.php');
    exit;
}

// ── Xóa sản phẩm ───────────────────────────────────────────────────
if (isset($_GET['remove'])) {
    $service->removeFromCart((int)$_GET['remove']);
    header('Location: shopcard.php');
    exit;
}

// ── Cập nhật số lượng ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {
    $service->updateQty($_POST['qty'] ?? []);
    header('Location: shopcard.php');
    exit;
}

// ── Đặt hàng ───────────────────────────────────────────────────────
$order_success = false;
$order_error   = '';
$history_success_msg = $_SESSION['history_success_msg'] ?? '';
$history_error_msg   = $_SESSION['history_error_msg'] ?? '';
unset($_SESSION['history_success_msg'], $_SESSION['history_error_msg']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_info'])) {
    $result = $service->updateOrderInfo($_POST, $userId);
    if ($result['success']) {
        $_SESSION['history_success_msg'] = $result['message'];
    } else {
        $_SESSION['history_error_msg'] = $result['message'];
    }

    header('Location: shopcard.php?tab=history');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Truyền userId để gắn đơn hàng với tài khoản
    $result        = $service->placeOrder($_POST, $userId);
    $order_success = $result['success'];
    $order_error   = $result['error'];
}

// ── Dữ liệu giỏ hàng ───────────────────────────────────────────────
$cart  = $service->getCart();
$total = $service->calcTotal($cart);

// ── Lịch sử đơn hàng ───────────────────────────────────────────────
$order_history = [];
if ($userId) {
    $order_history = $service->getOrderHistory($userId);
}

// ── Tab hiện tại: cart | history ───────────────────────────────────
$active_tab = $_GET['tab'] ?? 'cart';

// Nạp giao diện
require_once __DIR__ . '/shopcard_ui.php';
