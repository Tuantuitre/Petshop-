<?php
// ============================================================
// Admin_service.php
// Tầng SERVICE — Chứa toàn bộ business logic
// Nhận raw data từ Controller, xử lý, gọi DAO, trả kết quả
// Flow: UI → Controller → [Service] → DAO → DB
// ============================================================
require_once __DIR__ . '/Admin_dao.php';

class AdminService {
    private AdminDAO $dao;

    public function __construct(PDO $pdo) {
        $this->dao = new AdminDAO($pdo);
    }

    // ══════════════════════════════════════════════════════════
    // KHỞI TẠO
    // ══════════════════════════════════════════════════════════
    public function init(): void {
        $this->dao->ensureSchema();
    }

    // ══════════════════════════════════════════════════════════
    // STATS
    // ══════════════════════════════════════════════════════════
    public function getStats(): array {
        return [
            'total_lich'   => $this->dao->countLichCho(),
            'total_dh_cho' => $this->dao->countDonHangCho(),
            'total_dh_hoan'=> $this->dao->countDonHangHoan(),
            'total_revenue'=> $this->dao->sumRevenue(),
            'total_sp'     => $this->dao->countSanPham(),
            'total_bv'     => $this->dao->countBaiViet(),
            'total_hoan'   => $this->dao->countLichHoanHoan(),
        ];
    }

    // ══════════════════════════════════════════════════════════
    // LẤY DỮ LIỆU THEO TAB
    // ══════════════════════════════════════════════════════════
    public function getDataForTab(string $tab, string $search, int $page, int $yearFilter, string $loaiFilter = ''): array {
        switch ($tab) {

            case 'dashboard':
                return $this->getDashboardData();

            case 'lich':
                $rows  = $this->dao->getPendingLich($search, $page);
                $total = $this->dao->getPendingLichCount($search);
                return array_merge(
                    $this->wrapPaginated($rows, $total, $page),
                    ['lich_cho' => $rows]
                );

            case 'lichsu':
                $rows  = $this->dao->getLichSu($search, $page);
                $total = $this->dao->getLichSuCount($search);
                return $this->wrapPaginated($rows, $total, $page);

            case 'donhang':
                $rows  = $this->dao->getDonHang($search, $page);
                $total = $this->dao->getDonHangCount($search);
                return array_merge(
                    $this->wrapPaginated($rows, $total, $page),
                    $this->getStats()
                );

            case 'sanpham':
                $rows  = $this->dao->getSanPham($loaiFilter, $page);
                $total = $this->dao->getSanPhamCount($loaiFilter);
                return array_merge(
                    $this->wrapPaginated($rows, $total, $page),
                    ['loai_list' => $this->dao->getLoaiList()]
                );

            case 'baiviet':
                $rows  = $this->dao->getBaiViet($search, $page);
                $total = $this->dao->getBaiVietCount($search);
                // Lấy full content từng bài để nút Sửa có đủ dữ liệu
                $fullArticles = [];
                foreach ($rows as $row) {
                    $fullArticles[] = $this->dao->getBaiVietById((int)$row['id']);
                }
                return array_merge(
                    $this->wrapPaginated($rows, $total, $page),
                    ['articles_full' => $fullArticles]
                );

            case 'thongke':
                return $this->getThongKeData($yearFilter);

            default:
                return [];
        }
    }

    // ── Dashboard ─────────────────────────────────────────────
    private function getDashboardData(): array {
        $year          = (int) date('Y');
        $revenue_month = $this->dao->getRevenueByMonth($year);
        $orders_month  = $this->dao->getOrderCountByMonth($year);
        $dv_stats      = $this->dao->getDvStats();
        // Đơn hàng mới nhất (6 cái)
        $don_hang_recent = $this->dao->getDonHang('', 1);

        return array_merge($this->getStats(), [
            'revenue_month'    => $revenue_month,
            'orders_month'     => $orders_month,
            'dv_stats'         => $dv_stats,
            'don_hang_recent'  => $don_hang_recent,
        ]);
    }

    // ── Thống kê ──────────────────────────────────────────────
    private function getThongKeData(int $year): array {
        $revenue_month = $this->dao->getRevenueByMonth($year);
        $revenue_year  = $this->dao->getRevenueByYear(date('Y') - 4, date('Y'));
        $orders_month  = $this->dao->getOrderCountByMonth($year);
        $dv_stats      = $this->dao->getDvStats();
        $so_don_yr     = $this->dao->getOrderCountByYear($year);
        $total_rev_yr  = array_sum($revenue_month);
        $avg_order     = $so_don_yr > 0 ? $total_rev_yr / $so_don_yr : 0;
        $best_month    = array_search(max($revenue_month), $revenue_month);

        // Chi tiết từng tháng (dùng trong bảng thống kê)
        $monthly_detail = [];
        $max_rev = max(1, max($revenue_month));
        for ($m = 1; $m <= 12; $m++) {
            $val  = $revenue_month[$m];
            $prev = $m > 1 ? $revenue_month[$m - 1] : 0;
            $monthly_detail[$m] = [
                'revenue' => $val,
                'orders'  => $orders_month[$m],
                'diff'    => $val - $prev,
                'pct'     => round($val / $max_rev * 100),
            ];
        }

        return [
            'year_filter'    => $year,
            'revenue_month'  => $revenue_month,
            'revenue_year'   => $revenue_year,
            'orders_month'   => $orders_month,
            'dv_stats'       => $dv_stats,
            'so_don_yr'      => $so_don_yr,
            'total_rev_yr'   => $total_rev_yr,
            'avg_order'      => $avg_order,
            'best_month'     => $best_month,
            'rev_this_month' => $revenue_month[(int) date('n')],
            'monthly_detail' => $monthly_detail,
        ];
    }

    // ── Helper phân trang ─────────────────────────────────────
    private function wrapPaginated(array $rows, int $total, int $page): array {
        $pages = (int) ceil($total / $this->dao->perPage);
        return [
            'rows'     => $rows,
            'total'    => $total,
            'page'     => $page,
            'pages'    => $pages,
            'per_page' => $this->dao->perPage,
        ];
    }

    // ══════════════════════════════════════════════════════════
    // CẬP NHẬT TRẠNG THÁI
    // ══════════════════════════════════════════════════════════
    public function updateLichStatus(int $id, string $action): array {
        $lich = $this->dao->getLichById($id);
        if (!$lich) {
            return ['success' => false, 'message' => "Khong tim thay lich hen #$id.", 'type' => 'error'];
        }

        if ($action === 'dong_y') {
            if (($lich['trang_thai'] ?? '') !== 'cho_xu_ly') {
                return ['success' => false, 'message' => "Chi co the dong y lich dang cho xu ly #$id.", 'type' => 'warning'];
            }

            $ok = $this->dao->confirmLich($id);
            return [
                'success' => $ok,
                'message' => $ok ? "Da dong y lich hen #$id. Lich dang cho den ngay hen." : "Khong the dong y lich hen #$id.",
                'type' => $ok ? 'success' : 'error',
            ];
        }

        if ($action === 'hoan_thanh') {
            if (($lich['trang_thai'] ?? '') !== 'dang_cho_lich') {
                return ['success' => false, 'message' => "Chi lich dang cho lich moi duoc chuyen sang hoan thanh.", 'type' => 'warning'];
            }

            if (($lich['ngay_hen'] ?? '') && date('Y-m-d') < date('Y-m-d', strtotime($lich['ngay_hen']))) {
                return ['success' => false, 'message' => "Chua toi ngay hen nen chua the hoan thanh lich #$id.", 'type' => 'warning'];
            }

            $ok = $this->dao->completeLichWhenDue($id);
            return [
                'success' => $ok,
                'message' => $ok ? "Da hoan thanh lich hen #$id." : "Khong the hoan thanh lich hen #$id.",
                'type' => $ok ? 'success' : 'error',
            ];
        }

        if ($action === 'huy') {
            $ok = $this->dao->cancelLich($id);
            return [
                'success' => $ok,
                'message' => $ok ? "Da huy lich hen #$id." : "Lich hen #$id da bi huy hoac khong the huy.",
                'type' => $ok ? 'warning' : 'error',
            ];
        }

        return ['success' => false, 'message' => 'Hanh dong lich hen khong hop le.', 'type' => 'error'];
    }

    public function updateDonHangStatus(int $id, string $status): void {
        $allowed = ['cho_xu_ly', 'hoan_thanh', 'huy'];
        if (!in_array($status, $allowed, true)) return;
        $this->dao->updateDonHangTrangThai($id, $status);
    }

    // ══════════════════════════════════════════════════════════
    // XỬ LÝ POST — SẢN PHẨM
    // ══════════════════════════════════════════════════════════
    public function saveSanPham(array $post, array $files): string {
        // Upload ảnh
        $img = '';
        if (!empty($files['hinh_anh']['name'])) {
            $ext  = pathinfo($files['hinh_anh']['name'], PATHINFO_EXTENSION);
            $img  = 'sp_' . time() . '.' . $ext;
            move_uploaded_file($files['hinh_anh']['tmp_name'], 'assets/images/' . $img);
        }

        $sp              = new SanPhamModel();
        $sp->ten_san_pham= trim($post['ten_san_pham'] ?? '');
        $sp->gia         = (float) ($post['gia'] ?? 0);
        $sp->mo_ta       = trim($post['mo_ta'] ?? '');
        $sp->loai_id     = (int)   ($post['loai_id'] ?? 1);
        $sp->so_luong    = (int)   ($post['so_luong'] ?? 100);
        $sp->danh_muc    = trim($post['danh_muc'] ?? 'all');
        $sp->hinh_anh    = $img;
        $sp->id          = (int)   ($post['product_id'] ?? 0);

        if ($sp->id > 0) {
            // Cập nhật — kiểm tra trùng tên với sản phẩm khác (loại trừ chính nó)
            if ($this->dao->existsSanPhamByName($sp->ten_san_pham, $sp->id)) {
                return '❌ Sản phẩm "' . $sp->ten_san_pham . '" đã tồn tại! Vui lòng chọn tên khác.';
            }
            $this->dao->updateSanPham($sp);
            return '✅ Đã cập nhật sản phẩm!';
        } else {
            // Thêm mới — kiểm tra trùng tên
            if ($this->dao->existsSanPhamByName($sp->ten_san_pham)) {
                return '❌ Sản phẩm "' . $sp->ten_san_pham . '" đã tồn tại! Vui lòng chọn tên khác.';
            }
            $this->dao->insertSanPham($sp);
            return '✅ Đã thêm sản phẩm mới!';
        }
    }

    public function deleteSanPham(int $id): string {
        $this->dao->deleteSanPham($id);
        return '🗑️ Đã xóa sản phẩm!';
    }

    // ══════════════════════════════════════════════════════════
    // XỬ LÝ POST — BÀI VIẾT
    // ══════════════════════════════════════════════════════════
    public function saveBaiViet(array $post, array $files): string {
        // Upload 4 ảnh
        $imgs = [];
        for ($i = 1; $i <= 4; $i++) {
            if (!empty($files["hinh_anh_$i"]['name'])) {
                $ext      = pathinfo($files["hinh_anh_$i"]['name'], PATHINFO_EXTENSION);
                $fn       = 'news_' . time() . "_$i.$ext";
                move_uploaded_file($files["hinh_anh_$i"]['tmp_name'], 'assets/images/' . $fn);
                $imgs[$i] = $fn;
            } else {
                $imgs[$i] = $post["old_hinh_anh_$i"] ?? '';
            }
        }

        $tt                       = new TinTucModel();
        $tt->id                   = (int) ($post['article_id'] ?? 0);
        $tt->tieu_de              = trim($post['tieu_de'] ?? '');
        $tt->noi_dung_chinh       = trim($post['noi_dung_chinh'] ?? '');
        $tt->hinh_anh_1           = $imgs[1];
        $tt->nguyen_nhan_pho_bien = trim($post['nguyen_nhan_pho_bien'] ?? '');
        $tt->hinh_anh_2           = $imgs[2];
        $tt->huong_dan            = trim($post['huong_dan'] ?? '');
        $tt->hinh_anh_3           = $imgs[3];
        $tt->cach_cham            = trim($post['cach_cham'] ?? '');
        $tt->hinh_anh_4           = $imgs[4];
        $tt->email_nhan_thong_bao = trim($post['email_nhan_thong_bao'] ?? '');

        if ($tt->id > 0) {
            $this->dao->updateBaiViet($tt);
            return '✅ Đã cập nhật bài viết!';
        } else {
            $this->dao->insertBaiViet($tt);
            return '✅ Đã thêm bài viết mới!';
        }
    }

    public function deleteBaiViet(int $id): string {
        $this->dao->deleteBaiViet($id);
        return '🗑️ Đã xóa bài viết!';
    }

    // ══════════════════════════════════════════════════════════
    // CHI TIẾT ĐƠN HÀNG (AJAX)
    // ══════════════════════════════════════════════════════════
    public function getOrderDetail(int $id): array {
        $order = $this->dao->getDonHangById($id);
        if (!$order) {
            return ['error' => 'Không tìm thấy đơn hàng'];
        }
        $order['ngay_dat'] = date('d/m/Y H:i', strtotime($order['ngay_dat']));
        $items = $this->dao->getChiTietDonHang($id);
        return ['order' => $order, 'items' => $items];
    }

    // ══════════════════════════════════════════════════════════
    // LẤY LOẠI THÚ CƯNG (dùng trong modal sản phẩm)
    // ══════════════════════════════════════════════════════════
    public function getLoaiList(): array {
        return $this->dao->getLoaiList();
    }

    // ══════════════════════════════════════════════════════════
    // AUTOCOMPLETE BÀI VIẾT
    // ══════════════════════════════════════════════════════════
    public function suggestTieuDe(string $keyword): array {
        if (mb_strlen(trim($keyword)) < 1) return [];
        return $this->dao->searchTieuDe(trim($keyword));
    }
}
