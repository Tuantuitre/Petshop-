<?php
// ============================================================
// Admin_control.php
// Tầng CONTROLLER — Nhận request, điều phối, redirect
// Flow: UI → [Controller] → Service → DAO → DB
// ============================================================
require_once __DIR__ . '/Admin_service.php';

class AdminController {
    private AdminService $service;
    public  array        $data = [];

    public function __construct(PDO $pdo) {
        $this->service = new AdminService($pdo);
    }

    // ── Entry point: gọi từ admin.php ────────────────────────
    public function run(): void {
        $this->guardAuth();
        $this->service->init();

        // ── AJAX ─────────────────────────────────────────────
        if (isset($_GET['ajax'])) {
            $this->handleAjax();
            exit;
        }

        // ── GET actions ──────────────────────────────────────
        if (isset($_GET['action'], $_GET['id'])) {
            $this->handleLichAction((int)$_GET['id'], $_GET['action']);
        }
        if (isset($_GET['action_dh'], $_GET['dh_id'])) {
            $this->handleDonHangAction((int)$_GET['dh_id'], $_GET['action_dh']);
        }

        // ── POST ─────────────────────────────────────────────
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
            // handlePost() luôn redirect → không chạy tiếp
        }

        // ── Chuẩn bị data cho UI ─────────────────────────────
        $this->buildViewData();
    }

    // ══════════════════════════════════════════════════════════
    // AUTH GUARD
    // ══════════════════════════════════════════════════════════
    private function guardAuth(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
            header('Location: dangnhap.php');
            exit;
        }
    }

    // ══════════════════════════════════════════════════════════
    // AJAX HANDLER
    // ══════════════════════════════════════════════════════════
    private function handleAjax(): void {
        header('Content-Type: application/json');
        switch ($_GET['ajax']) {
            case 'order_detail':
                echo json_encode(
                    $this->service->getOrderDetail((int)($_GET['id'] ?? 0))
                );
                break;
            case 'suggest_baiviet':
                echo json_encode(
                    $this->service->suggestTieuDe($_GET['q'] ?? '')
                );
                break;
            default:
                echo json_encode(['error' => 'Unknown ajax action']);
        }
    }

    // ══════════════════════════════════════════════════════════
    // GET ACTIONS (lịch hẹn / đơn hàng)
    // ══════════════════════════════════════════════════════════
    private function handleLichAction(int $id, string $action): void {
        $result = $this->service->updateLichStatus($id, $action);
        $status = $action;

        $msg = ($status === 'hoan_thanh')
            ? "✅ Đã đánh dấu hoàn thành lịch hẹn #$id"
            : "🚫 Đã hủy lịch hẹn #$id";
        $type = ($status === 'huy') ? 'warning' : 'success';

        $msg  = $result['message'] ?? $msg;
        $type = $result['type'] ?? $type;

        $this->redirect("admin.php?tab=lich&msg=" . urlencode($msg) . "&msg_type=$type");
    }

    private function handleDonHangAction(int $id, string $action): void {
        $status = ($action === 'hoan_thanh') ? 'hoan_thanh' : 'huy';
        $this->service->updateDonHangStatus($id, $status);

        $msg = ($status === 'hoan_thanh')
            ? "✅ Đã hoàn thành đơn hàng #$id"
            : "🚫 Đã hủy đơn hàng #$id";
        $type = ($status === 'huy') ? 'warning' : 'success';

        $this->redirect("admin.php?tab=donhang&msg=" . urlencode($msg) . "&msg_type=$type");
    }

    // ══════════════════════════════════════════════════════════
    // POST HANDLER
    // ══════════════════════════════════════════════════════════
    private function handlePost(): void {
        $post  = $_POST;
        $files = $_FILES;

        // Sản phẩm
        if (isset($post['save_product'])) {
            $msg     = $this->service->saveSanPham($post, $files);
            $msgType = str_starts_with($msg, '❌') ? 'error' : 'success';
            $this->redirect("admin.php?tab=sanpham&msg=" . urlencode($msg) . "&msg_type=$msgType");
        }
        if (isset($post['delete_product'])) {
            $msg = $this->service->deleteSanPham((int)($post['product_id'] ?? 0));
            $this->redirect("admin.php?tab=sanpham&msg=" . urlencode($msg) . "&msg_type=warning");
        }

        // Bài viết
        if (isset($post['save_article'])) {
            $msg = $this->service->saveBaiViet($post, $files);
            $this->redirect("admin.php?tab=baiviet&msg=" . urlencode($msg));
        }
        if (isset($post['delete_article'])) {
            $msg = $this->service->deleteBaiViet((int)($post['article_id'] ?? 0));
            $this->redirect("admin.php?tab=baiviet&msg=" . urlencode($msg) . "&msg_type=warning");
        }

        // Fallback
        $this->redirect("admin.php?tab=dashboard");
    }

    // ══════════════════════════════════════════════════════════
    // BUILD VIEW DATA
    // ══════════════════════════════════════════════════════════
    private function buildViewData(): void {
        $tab        = $_GET['tab']    ?? 'dashboard';
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $searchKey  = 'search_' . $tab;
        $search     = $_GET[$searchKey] ?? '';
        $yearFilter = (int)($_GET['year'] ?? date('Y'));
        $loaiFilter = $_GET['loai_filter'] ?? '';

        $stats   = $this->service->getStats();
        $tabData = $this->service->getDataForTab($tab, $search, $page, $yearFilter, $loaiFilter);
        $loaiList = $this->service->getLoaiList();

        $this->data = array_merge($stats, $tabData, [
            'tab'         => $tab,
            'page'        => $page,
            'search'      => $search,
            'year_filter' => $yearFilter,
            'loai_filter' => $loaiFilter,
            'msg'         => $_GET['msg']      ?? '',
            'msg_type'    => $_GET['msg_type'] ?? 'success',
            'loai_list'   => $loaiList,
        ]);
    }

    // ══════════════════════════════════════════════════════════
    // HELPER
    // ══════════════════════════════════════════════════════════
    private function redirect(string $url): void {
        header("Location: $url");
        exit;
    }
}
