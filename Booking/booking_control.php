<?php
/**
 * booking_control.php
 * Nhận request → gọi BookingService → truyền dữ liệu cho UI.
 * Không chứa HTML, không chứa SQL.
 * Phải chạy TRƯỚC include header để tránh lỗi redirect.
 */

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: dangnhap.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];

require_once __DIR__ . '/../includes/db.php';   // $pdo
require_once __DIR__ . '/booking_dao.php';
require_once __DIR__ . '/booking_service.php';

// ── Khởi tạo DAO & Service ───────────────────────────────────────────────────
$dao     = new BookingDAO($pdo);
$service = new BookingService($dao);

// ── Kết quả mặc định ─────────────────────────────────────────────────────────
$booking_success = false;
$booking_error   = '';

// ── Xử lý POST đặt lịch ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dat_lich'])) {
    $result          = $service->placeBooking($_POST, $user_id);
    $booking_success = $result['success'];
    $booking_error   = $result['error'];
}

// ── Lấy danh sách cho dropdown ───────────────────────────────────────────────
$loai_list = $service->getLoaiList();
$dv_list   = $service->getDichVuList();

// ── Nạp giao diện ────────────────────────────────────────────────────────────
require_once __DIR__ . '/booking_ui.php';
