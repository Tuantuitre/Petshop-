<?php
// ============================================================
// Contact_control.php
// Tầng CONTROLLER — Nhận request, điều phối
// Flow: UI → [Controller] → Service → DAO → DB
// ============================================================
ob_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/contact_service.php';

// ── POST: xử lý form, trả JSON rồi thoát ─────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    header('Content-Type: application/json; charset=utf-8');
    ob_end_clean();

    $service = new ContactService($pdo);
    echo json_encode($service->handleSubmit($_POST));
    exit;
}

// ── GET: nạp giao diện ───────────────────────────────────────
ob_end_clean();
require_once __DIR__ . '/contact_ui.php';
