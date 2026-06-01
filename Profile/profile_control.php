<?php
/**
 * profile_control.php
 * Nhận request → kiểm tra session → gọi ProfileService → truyền dữ liệu cho UI.
 * Không chứa HTML, không chứa SQL.
 * Chạy TRƯỚC khi include header vì có header() redirect.
 */

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../includes/db.php';   // $pdo
require_once __DIR__ . '/profile_dao.php';
require_once __DIR__ . '/profile_service.php';

// ── Chưa đăng nhập → về trang đăng nhập ─────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header('Location: dangnhap.php');
    exit;
}

$user_id  = (int)$_SESSION['user_id'];
$username = $_SESSION['username'];

// ── Đăng xuất ────────────────────────────────────────────────────────────────
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: dangnhap.php');
    exit;
}

// ── Khởi tạo DAO & Service ───────────────────────────────────────────────────
$dao     = new ProfileDAO($pdo);
$service = new ProfileService($dao);

// ── Kết quả mặc định ─────────────────────────────────────────────────────────
$success_msg = '';
$error_msg   = '';

// ── Xử lý đổi mật khẩu ───────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'])
    && $_POST['action'] === 'change_password'
) {
    $result = $service->changePassword($user_id, $_POST);
    if ($result['success']) {
        $success_msg = $result['message'];
    } else {
        $error_msg = $result['message'];
    }
}

// ── Lấy thông tin profile ─────────────────────────────────────────────────────
$profile = $service->getProfile($user_id, $username);
$booking_history = $service->getBookingHistory($user_id);

// ── Nạp giao diện ────────────────────────────────────────────────────────────
require_once __DIR__ . '/profile_ui.php';
