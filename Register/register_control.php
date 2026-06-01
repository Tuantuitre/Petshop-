<?php
/**
 * register_control.php
 * Nhận request → gọi RegisterService → truyền dữ liệu cho UI.
 * Không chứa HTML, không chứa SQL.
 */

require_once __DIR__ . '/../includes/db.php';   // $pdo
require_once __DIR__ . '/register_dao.php';
require_once __DIR__ . '/register_service.php';
require_once __DIR__ . '/register_model.php';

// ── Kết quả mặc định ─────────────────────────────────────────────────────────
$success = false;
$error   = '';

// ── Xử lý POST đăng ký ───────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dao     = new RegisterDAO($pdo);
    $service = new RegisterService($dao);
    $input   = new RegisterInput($_POST);
    $result  = $service->register($input);
    $success = $result['success'];
    $error   = $result['error'];
}

// ── Nạp giao diện ────────────────────────────────────────────────────────────
require_once __DIR__ . '/register_ui.php';