<?php
// ============================================================
// Admin_dao.php
// Tầng DAO — Toàn bộ câu query SQL nằm ở đây
// Flow: UI → Controller → Service → [DAO] → DB
// ============================================================
require_once __DIR__ . '/Admin_model.php';

class AdminDAO {
    private PDO $pdo;
    public  int $perPage = 10;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // ══════════════════════════════════════════════════════════
    // SCHEMA — đảm bảo bảng / cột tồn tại
    // ══════════════════════════════════════════════════════════
    public function ensureSchema(): void {
        $sqls = [
            "ALTER TABLE dat_lich  ADD COLUMN trang_thai ENUM('cho_xu_ly','dang_cho_lich','hoan_thanh','huy') DEFAULT 'cho_xu_ly'",
            "ALTER TABLE dat_lich  MODIFY COLUMN trang_thai ENUM('cho_xu_ly','dang_cho_lich','hoan_thanh','huy') DEFAULT 'cho_xu_ly'",
            "ALTER TABLE san_pham  ADD COLUMN danh_muc VARCHAR(50) DEFAULT 'all'",
            "ALTER TABLE san_pham  ADD COLUMN so_luong INT DEFAULT 100",
            "CREATE TABLE IF NOT EXISTS don_hang (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ho_ten VARCHAR(255) NOT NULL,
                sdt VARCHAR(20),
                dia_chi TEXT,
                phuong_thuc_tt VARCHAR(50) DEFAULT 'cod',
                tong_tien DECIMAL(12,2) NOT NULL DEFAULT 0,
                trang_thai ENUM('cho_xu_ly','hoan_thanh','huy') DEFAULT 'cho_xu_ly',
                ngay_dat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            "CREATE TABLE IF NOT EXISTS chi_tiet_don_hang (
                id INT AUTO_INCREMENT PRIMARY KEY,
                don_hang_id INT NOT NULL,
                san_pham_id INT,
                ten_san_pham VARCHAR(255),
                gia DECIMAL(10,2),
                so_luong INT,
                thanh_tien DECIMAL(12,2),
                FOREIGN KEY (don_hang_id) REFERENCES don_hang(id) ON DELETE CASCADE
            )",
        ];
        foreach ($sqls as $sql) {
            try { $this->pdo->exec($sql); } catch (Exception $e) { /* cột / bảng đã tồn tại */ }
        }
    }

    // ══════════════════════════════════════════════════════════
    // STATS — số liệu tổng quan
    // ══════════════════════════════════════════════════════════
    public function countLichCho(): int {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM dat_lich WHERE trang_thai IN ('cho_xu_ly','dang_cho_lich')")->fetchColumn();
    }

    public function countDonHangCho(): int {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM don_hang WHERE trang_thai='cho_xu_ly'")->fetchColumn();
    }

    public function countDonHangHoan(): int {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM don_hang WHERE trang_thai='hoan_thanh'")->fetchColumn();
    }

    public function sumRevenue(): float {
        return (float) $this->pdo->query("SELECT COALESCE(SUM(tong_tien),0) FROM don_hang WHERE trang_thai='hoan_thanh'")->fetchColumn();
    }

    public function countSanPham(): int {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM san_pham")->fetchColumn();
    }

    public function countBaiViet(): int {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM tin_tuc")->fetchColumn();
    }

    public function countLichHoanHoan(): int {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM dat_lich WHERE trang_thai='hoan_thanh'")->fetchColumn();
    }

    // ══════════════════════════════════════════════════════════
    // LỊCH HẸN
    // ══════════════════════════════════════════════════════════
    public function getPendingLichCount(string $date = ''): int {
        $sql    = "SELECT COUNT(*) FROM dat_lich WHERE trang_thai IN ('cho_xu_ly','dang_cho_lich')";
        $params = [];
        if ($date) {
            $sql   .= " AND DATE(ngay_hen) = ?";
            $params = [$date];
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getPendingLich(string $date = '', int $page = 1): array {
        $sql = "SELECT d.*, l.ten_loai, dv.ten_dich_vu
                FROM dat_lich d
                LEFT JOIN loai_thu_cung l  ON d.loai_thu_cung_id = l.id
                LEFT JOIN dich_vu dv        ON d.dich_vu_id = dv.id
                WHERE d.trang_thai IN ('cho_xu_ly','dang_cho_lich')";
        $params = [];
        if ($date) {
            $sql   .= " AND DATE(d.ngay_hen) = ?";
            $params = [$date];
        }
        $sql .= " ORDER BY FIELD(d.trang_thai, 'cho_xu_ly', 'dang_cho_lich'), d.ngay_hen ASC, d.gio_hen ASC";
        return $this->paginate($sql, $params, $page);
    }

    public function getLichSuCount(string $search = ''): int {
        $sql    = "SELECT COUNT(*) FROM dat_lich WHERE trang_thai IN ('hoan_thanh','huy')";
        $params = [];
        if ($search) {
            $sql   .= " AND (ho_ten LIKE ? OR sdt LIKE ? OR DATE(ngay_hen) = ?)";
            $params = ["%$search%", "%$search%", $search];
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getLichSu(string $search = '', int $page = 1): array {
        $sql = "SELECT d.*, l.ten_loai, dv.ten_dich_vu
                FROM dat_lich d
                LEFT JOIN loai_thu_cung l  ON d.loai_thu_cung_id = l.id
                LEFT JOIN dich_vu dv        ON d.dich_vu_id = dv.id
                WHERE d.trang_thai IN ('hoan_thanh','huy')";
        $params = [];
        if ($search) {
            $sql   .= " AND (d.ho_ten LIKE ? OR d.sdt LIKE ? OR DATE(d.ngay_hen) = ?)";
            $params = ["%$search%", "%$search%", $search];
        }
        $sql .= " ORDER BY d.ngay_tao DESC";
        return $this->paginate($sql, $params, $page);
    }

    public function getLichById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM dat_lich WHERE id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updateLichTrangThai(int $id, string $trangThai): bool {
        $this->pdo->prepare("UPDATE dat_lich SET trang_thai=? WHERE id=?")->execute([$trangThai, $id]);
        return true;
    }

    public function confirmLich(int $id): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE dat_lich
             SET trang_thai='dang_cho_lich'
             WHERE id=? AND trang_thai='cho_xu_ly'"
        );
        $stmt->execute([$id]);
        return $stmt->rowCount() === 1;
    }

    public function completeLichWhenDue(int $id): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE dat_lich
             SET trang_thai='hoan_thanh'
             WHERE id=?
               AND trang_thai='dang_cho_lich'
               AND DATE(ngay_hen) <= CURDATE()"
        );
        $stmt->execute([$id]);
        return $stmt->rowCount() === 1;
    }

    public function cancelLich(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE dat_lich SET trang_thai='huy' WHERE id=? AND trang_thai <> 'huy'");
        $stmt->execute([$id]);
        return $stmt->rowCount() === 1;
    }

    // ══════════════════════════════════════════════════════════
    // ĐƠN HÀNG
    // ══════════════════════════════════════════════════════════
    public function getDonHangCount(string $search = ''): int {
        $sql    = "SELECT COUNT(*) FROM don_hang WHERE 1=1";
        $params = [];
        if ($search) {
            $sql   .= " AND (ho_ten LIKE ? OR sdt LIKE ?)";
            $params = ["%$search%", "%$search%"];
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getDonHang(string $search = '', int $page = 1): array {
        $sql    = "SELECT * FROM don_hang WHERE 1=1";
        $params = [];
        if ($search) {
            $sql   .= " AND (ho_ten LIKE ? OR sdt LIKE ?)";
            $params = ["%$search%", "%$search%"];
        }
        $sql .= " ORDER BY ngay_dat DESC";
        return $this->paginate($sql, $params, $page);
    }

    public function getDonHangById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM don_hang WHERE id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getChiTietDonHang(int $donHangId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM chi_tiet_don_hang WHERE don_hang_id=?");
        $stmt->execute([$donHangId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateDonHangTrangThai(int $id, string $trangThai): void {
        $this->pdo->prepare("UPDATE don_hang SET trang_thai=? WHERE id=?")->execute([$trangThai, $id]);
    }

    // ══════════════════════════════════════════════════════════
    // SẢN PHẨM
    // ══════════════════════════════════════════════════════════
    public function getSanPhamCount(string $loaiFilter = ''): int {
        $sql    = "SELECT COUNT(*) FROM san_pham s LEFT JOIN loai_thu_cung l ON s.loai_id = l.id WHERE 1=1";
        $params = [];
        if ($loaiFilter) {
            $map = ['cho' => 'dog%', 'meo' => 'cat%', 'hamster' => 'hamster%'];
            if (isset($map[$loaiFilter])) {
                $sql   .= " AND s.danh_muc LIKE ?";
                $params = [$map[$loaiFilter]];
            }
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getSanPham(string $loaiFilter = '', int $page = 1): array {
        $sql    = "SELECT s.*, l.ten_loai
                FROM san_pham s
                LEFT JOIN loai_thu_cung l ON s.loai_id = l.id
                WHERE 1=1";
        $params = [];
        if ($loaiFilter) {
            $map = ['cho' => 'dog%', 'meo' => 'cat%', 'hamster' => 'hamster%'];
            if (isset($map[$loaiFilter])) {
                $sql   .= " AND s.danh_muc LIKE ?";
                $params = [$map[$loaiFilter]];
            }
        }
        $sql .= " ORDER BY s.id DESC";
        return $this->paginate($sql, $params, $page);
    }

    public function getSanPhamById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM san_pham WHERE id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function existsSanPhamByName(string $tenSanPham, int $excludeId = 0): bool {
        $sql    = "SELECT COUNT(*) FROM san_pham WHERE LOWER(TRIM(ten_san_pham)) = LOWER(TRIM(?)) AND id != ?";
        $stmt   = $this->pdo->prepare($sql);
        $stmt->execute([$tenSanPham, $excludeId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function insertSanPham(SanPhamModel $sp): void {
        $this->pdo->prepare(
            "INSERT INTO san_pham (ten_san_pham,gia,mo_ta,loai_id,so_luong,danh_muc,hinh_anh)
             VALUES (?,?,?,?,?,?,?)"
        )->execute([
            $sp->ten_san_pham, $sp->gia, $sp->mo_ta,
            $sp->loai_id, $sp->so_luong, $sp->danh_muc, $sp->hinh_anh,
        ]);
    }

    public function updateSanPham(SanPhamModel $sp): void {
        if ($sp->hinh_anh) {
            $this->pdo->prepare(
                "UPDATE san_pham SET ten_san_pham=?,gia=?,mo_ta=?,loai_id=?,so_luong=?,danh_muc=?,hinh_anh=? WHERE id=?"
            )->execute([
                $sp->ten_san_pham, $sp->gia, $sp->mo_ta,
                $sp->loai_id, $sp->so_luong, $sp->danh_muc, $sp->hinh_anh, $sp->id,
            ]);
        } else {
            $this->pdo->prepare(
                "UPDATE san_pham SET ten_san_pham=?,gia=?,mo_ta=?,loai_id=?,so_luong=?,danh_muc=? WHERE id=?"
            )->execute([
                $sp->ten_san_pham, $sp->gia, $sp->mo_ta,
                $sp->loai_id, $sp->so_luong, $sp->danh_muc, $sp->id,
            ]);
        }
    }

    public function deleteSanPham(int $id): void {
        $this->pdo->prepare("DELETE FROM san_pham WHERE id=?")->execute([$id]);
    }

    // ══════════════════════════════════════════════════════════
    // BÀI VIẾT (TIN TỨC)
    // ══════════════════════════════════════════════════════════
    public function getBaiVietCount(string $search = ''): int {
        $sql    = "SELECT COUNT(*) FROM tin_tuc WHERE 1=1";
        $params = [];
        if ($search) {
            $sql   .= " AND tieu_de LIKE ?";
            $params = ["%$search%"];
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getBaiViet(string $search = '', int $page = 1): array {
        $sql    = "SELECT id, tieu_de, hinh_anh_1, ngay_dang FROM tin_tuc WHERE 1=1";
        $params = [];
        if ($search) {
            $sql   .= " AND tieu_de LIKE ?";
            $params = ["%$search%"];
        }
        $sql .= " ORDER BY ngay_dang DESC";
        return $this->paginate($sql, $params, $page);
    }

    public function searchTieuDe(string $keyword): array {
        $stmt = $this->pdo->prepare(
            "SELECT id, tieu_de FROM tin_tuc WHERE tieu_de LIKE ? ORDER BY ngay_dang DESC LIMIT 8"
        );
        $stmt->execute(["%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBaiVietById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM tin_tuc WHERE id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function insertBaiViet(TinTucModel $tt): void {
        $this->pdo->prepare(
            "INSERT INTO tin_tuc
             (tieu_de,noi_dung_chinh,hinh_anh_1,nguyen_nhan_pho_bien,hinh_anh_2,huong_dan,hinh_anh_3,cach_cham,hinh_anh_4,email_nhan_thong_bao)
             VALUES (?,?,?,?,?,?,?,?,?,?)"
        )->execute([
            $tt->tieu_de, $tt->noi_dung_chinh, $tt->hinh_anh_1,
            $tt->nguyen_nhan_pho_bien, $tt->hinh_anh_2,
            $tt->huong_dan, $tt->hinh_anh_3,
            $tt->cach_cham, $tt->hinh_anh_4,
            $tt->email_nhan_thong_bao,
        ]);
    }

    public function updateBaiViet(TinTucModel $tt): void {
        $this->pdo->prepare(
            "UPDATE tin_tuc SET
             tieu_de=?,noi_dung_chinh=?,hinh_anh_1=?,
             nguyen_nhan_pho_bien=?,hinh_anh_2=?,
             huong_dan=?,hinh_anh_3=?,
             cach_cham=?,hinh_anh_4=?,
             email_nhan_thong_bao=?
             WHERE id=?"
        )->execute([
            $tt->tieu_de, $tt->noi_dung_chinh, $tt->hinh_anh_1,
            $tt->nguyen_nhan_pho_bien, $tt->hinh_anh_2,
            $tt->huong_dan, $tt->hinh_anh_3,
            $tt->cach_cham, $tt->hinh_anh_4,
            $tt->email_nhan_thong_bao,
            $tt->id,
        ]);
    }

    public function deleteBaiViet(int $id): void {
        $this->pdo->prepare("DELETE FROM tin_tuc WHERE id=?")->execute([$id]);
    }

    // ══════════════════════════════════════════════════════════
    // LOẠI THÚ CƯNG
    // ══════════════════════════════════════════════════════════
    public function getLoaiList(): array {
        return $this->pdo->query("SELECT * FROM loai_thu_cung")->fetchAll(PDO::FETCH_ASSOC);
    }

    // ══════════════════════════════════════════════════════════
    // DOANH THU / THỐNG KÊ
    // ══════════════════════════════════════════════════════════
    public function getRevenueByMonth(int $year): array {
        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $stmt = $this->pdo->prepare(
                "SELECT COALESCE(SUM(tong_tien),0) FROM don_hang
                 WHERE trang_thai='hoan_thanh' AND YEAR(ngay_dat)=? AND MONTH(ngay_dat)=?"
            );
            $stmt->execute([$year, $m]);
            $result[$m] = (float) $stmt->fetchColumn();
        }
        return $result;
    }

    public function getOrderCountByMonth(int $year): array {
        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM don_hang
                 WHERE trang_thai='hoan_thanh' AND YEAR(ngay_dat)=? AND MONTH(ngay_dat)=?"
            );
            $stmt->execute([$year, $m]);
            $result[$m] = (int) $stmt->fetchColumn();
        }
        return $result;
    }

    public function getRevenueByYear(int $fromYear, int $toYear): array {
        $result = [];
        for ($y = $fromYear; $y <= $toYear; $y++) {
            $stmt = $this->pdo->prepare(
                "SELECT COALESCE(SUM(tong_tien),0) FROM don_hang
                 WHERE trang_thai='hoan_thanh' AND YEAR(ngay_dat)=?"
            );
            $stmt->execute([$y]);
            $result[$y] = (float) $stmt->fetchColumn();
        }
        return $result;
    }

    public function getOrderCountByYear(int $year): int {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM don_hang WHERE trang_thai='hoan_thanh' AND YEAR(ngay_dat)=?"
        );
        $stmt->execute([$year]);
        return (int) $stmt->fetchColumn();
    }

    public function getDvStats(): array {
        return $this->pdo->query(
            "SELECT dv.ten_dich_vu, COUNT(d.id) AS cnt
             FROM dat_lich d
             LEFT JOIN dich_vu dv ON d.dich_vu_id = dv.id
             WHERE d.trang_thai='hoan_thanh'
             GROUP BY d.dich_vu_id
             ORDER BY cnt DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    // ══════════════════════════════════════════════════════════
    // HELPER — phân trang (ĐÃ FIX)
    // ══════════════════════════════════════════════════════════
    private function paginate(string $sql, array $params, int $page): array {
        $offset = ($page - 1) * $this->perPage;
        // Thêm LIMIT và OFFSET vào SQL với placeholder là ?
        $sql .= " LIMIT ? OFFSET ?";
        $stmt = $this->pdo->prepare($sql);
        
        // Bind tất cả params, bao gồm cả limit và offset
        $index = 1;
        foreach ($params as $val) {
            $stmt->bindValue($index++, $val);
        }
        $stmt->bindValue($index++, $this->perPage, PDO::PARAM_INT);
        $stmt->bindValue($index++, $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
