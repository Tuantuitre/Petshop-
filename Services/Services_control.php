<?php
/**
 * Nhận request → gọi ServiceService → truyền dữ liệu cho UI.
 */

require_once __DIR__ . '/Services_service.php';

// ── Khởi tạo Service ─────────────────────────────────────────────────────────
$serviceObj = new ServiceService();

// ── Lấy danh sách dịch vụ ────────────────────────────────────────────────────
$services = $serviceObj->getAllServices();

// ── Nạp giao diện ────────────────────────────────────────────────────────────
require_once __DIR__ . '/Services_ui.php';