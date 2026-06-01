<?php
/**
 * shopcard_dao.php
 * Chỉ chứa query DB — không có logic nghiệp vụ, không có HTML.
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/shopcard_model.php';

class ShopcardDAO {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function beginTransaction(): void {
        $this->pdo->beginTransaction();
    }

    public function commit(): void {
        $this->pdo->commit();
    }

    public function rollBack(): void {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    // ── Tạo bảng + thêm cột user_id nếu chưa có ─────────────────────────────
    public function ensureTables(): void {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS don_hang (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ho_ten VARCHAR(255) NOT NULL,
            sdt VARCHAR(20),
            dia_chi TEXT,
            phuong_thuc_tt VARCHAR(50) DEFAULT 'cod',
            tong_tien DECIMAL(12,2) NOT NULL DEFAULT 0,
            trang_thai ENUM('cho_xu_ly','hoan_thanh','huy') DEFAULT 'cho_xu_ly',
            ngay_dat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS chi_tiet_don_hang (
            id INT AUTO_INCREMENT PRIMARY KEY,
            don_hang_id INT NOT NULL,
            san_pham_id INT NOT NULL,
            ten_san_pham VARCHAR(255),
            gia DECIMAL(10,2),
            so_luong INT,
            thanh_tien DECIMAL(12,2),
            FOREIGN KEY (don_hang_id) REFERENCES don_hang(id) ON DELETE CASCADE
        )");

        // Thêm cột user_id vào don_hang nếu chưa có
        // (dùng để liên kết đơn hàng với tài khoản)
        try {
            $this->pdo->exec("ALTER TABLE don_hang ADD COLUMN user_id INT NULL DEFAULT NULL");
            $this->pdo->exec("ALTER TABLE don_hang ADD INDEX idx_user_id (user_id)");
        } catch (Exception $e) {
            // Cột đã tồn tại — bỏ qua
        }
    }

    // ── Lấy sản phẩm theo id ─────────────────────────────────────────────────
    public function getProductById(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT id, ten_san_pham AS name, gia AS price, hinh_anh AS img, so_luong
             FROM san_pham WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ── Lấy giá từ DB khi đặt hàng ───────────────────────────────────────────
    public function getProductPriceById(int $id, bool $forUpdate = false): array|false {
        $sql = "SELECT id, ten_san_pham, gia, so_luong FROM san_pham WHERE id = ?";
        if ($forUpdate) {
            $sql .= " FOR UPDATE";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function decreaseProductStock(int $productId, int $qty): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE san_pham
             SET so_luong = so_luong - ?, luot_mua = COALESCE(luot_mua, 0) + ?
             WHERE id = ? AND so_luong >= ?"
        );
        $stmt->execute([$qty, $qty, $productId, $qty]);
        return $stmt->rowCount() === 1;
    }

    // ── Lưu đơn hàng, trả về ID vừa insert ──────────────────────────────────
    public function insertOrder(Order $order, ?int $userId = null): int {
        $this->pdo->prepare(
            "INSERT INTO don_hang (ho_ten, sdt, dia_chi, phuong_thuc_tt, tong_tien, user_id)
             VALUES (?, ?, ?, ?, ?, ?)"
        )->execute([
            $order->ho_ten,
            $order->sdt,
            $order->dia_chi,
            $order->phuong_thuc,
            $order->tong_tien,
            $userId,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    // ── Lưu chi tiết từng sản phẩm ───────────────────────────────────────────
    public function insertOrderDetail(OrderDetail $detail): void {
        $this->pdo->prepare(
            "INSERT INTO chi_tiet_don_hang
             (don_hang_id, san_pham_id, ten_san_pham, gia, so_luong, thanh_tien)
             VALUES (?, ?, ?, ?, ?, ?)"
        )->execute([
            $detail->don_hang_id,
            $detail->san_pham_id,
            $detail->ten_san_pham,
            $detail->gia,
            $detail->so_luong,
            $detail->thanh_tien,
        ]);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // LỊCH SỬ ĐƠN HÀNG
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * Lấy tất cả đơn hàng của user theo user_id.
     * @return array  mảng raw rows từ DB
     */
    public function getOrdersByUserId(int $userId): array {
        $stmt = $this->pdo->prepare(
            "SELECT *,
                    CASE
                        WHEN ngay_dat >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                             AND trang_thai <> 'huy'
                        THEN 1 ELSE 0
                    END AS can_edit_info
             FROM don_hang
             WHERE user_id = ?
             ORDER BY ngay_dat DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderByIdForUser(int $orderId, int $userId): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT *,
                    CASE
                        WHEN ngay_dat >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                             AND trang_thai <> 'huy'
                        THEN 1 ELSE 0
                    END AS can_edit_info
             FROM don_hang
             WHERE id = ? AND user_id = ?
             LIMIT 1"
        );
        $stmt->execute([$orderId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateOrderInfoWithin24Hours(
        int $orderId,
        int $userId,
        string $hoTen,
        string $sdt,
        string $diaChi
    ): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE don_hang
             SET ho_ten = ?, sdt = ?, dia_chi = ?
             WHERE id = ?
               AND user_id = ?
               AND trang_thai <> 'huy'
               AND ngay_dat >= DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        );
        $stmt->execute([$hoTen, $sdt, $diaChi, $orderId, $userId]);
        if ($stmt->rowCount() === 1) {
            return true;
        }

        $order = $this->getOrderByIdForUser($orderId, $userId);
        return $order
            && !empty($order['can_edit_info'])
            && ($order['ho_ten'] ?? '') === $hoTen
            && ($order['sdt'] ?? '') === $sdt
            && ($order['dia_chi'] ?? '') === $diaChi;
    }

    /**
     * Lấy chi tiết sản phẩm của một đơn hàng,
     * JOIN sang san_pham để lấy thêm hinh_anh.
     * @return array  mảng raw rows
     */
    public function getOrderItemsByOrderId(int $orderId): array {
        $stmt = $this->pdo->prepare(
            "SELECT ct.*,
                    sp.hinh_anh
             FROM chi_tiet_don_hang ct
             LEFT JOIN san_pham sp ON sp.id = ct.san_pham_id
             WHERE ct.don_hang_id = ?"
        );
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
