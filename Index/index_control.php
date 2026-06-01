<?php
/**
 * index_control.php
 * Nhận request trang chủ -> gọi service JSON -> truyền dữ liệu cho UI.
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/index_dao.php';
require_once __DIR__ . '/index_service.php';

$dao = new IndexDAO($pdo);
$service = new IndexService($dao);

$homeJson = $service->getHomeDataJson();
$homeData = json_decode($homeJson, true);
if (!is_array($homeData)) {
    $homeData = [];
}

$topProducts      = $homeData['topProducts']      ?? [];
$featuredServices = $homeData['featuredServices'] ?? [];
$topBlogPosts     = $homeData['topBlogPosts']     ?? [];

require_once __DIR__ . '/index_ui.php';
