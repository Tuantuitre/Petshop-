<?php
/**
 * booking_dao.php
 * Chỉ chứa query DB — không có logic nghiệp vụ, không có HTML.
 */

require_once __DIR__ . '/../includes/db.php';   // $pdo
require_once __DIR__ . '/booking_model.php';

class BookingDAO {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->ensureSchema();
    }

    private function ensureSchema(): void {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*)
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'dat_lich'
                  AND COLUMN_NAME = 'user_id'
            ");
            $stmt->execute();

            if ((int)$stmt->fetchColumn() === 0) {
                $this->pdo->exec("ALTER TABLE dat_lich ADD COLUMN user_id INT NULL DEFAULT NULL");
            }
        } catch (Exception $e) {
            // Migration best-effort; the insert below will expose real DB errors.
        }

        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*)
                FROM INFORMATION_SCHEMA.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'dat_lich'
                  AND INDEX_NAME = 'idx_dat_lich_user_id'
            ");
            $stmt->execute();

            if ((int)$stmt->fetchColumn() === 0) {
                $this->pdo->exec("ALTER TABLE dat_lich ADD INDEX idx_dat_lich_user_id (user_id)");
            }
        } catch (Exception $e) {
            // Index cannot be created in this database state.
        }

        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*)
                FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = DATABASE()
                  AND TABLE_NAME = 'dat_lich'
                  AND CONSTRAINT_NAME = 'fk_dat_lich_user'
            ");
            $stmt->execute();

            if ((int)$stmt->fetchColumn() === 0) {
                $this->pdo->exec("
                    ALTER TABLE dat_lich
                    ADD CONSTRAINT fk_dat_lich_user
                    FOREIGN KEY (user_id) REFERENCES tai_khoan(id)
                    ON DELETE SET NULL
                ");
            }
        } catch (Exception $e) {
            // Constraint cannot be created in this database state.
        }
    }

    // ── Lấy danh sách loại thú cưng ─────────────────────────────────────────
    /** @return LoaiThuCung[] */
    public function getAllLoai(): array {
        $rows = $this->pdo->query("SELECT * FROM loai_thu_cung ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => new LoaiThuCung((int)$r['id'], $r['ten_loai']), $rows);
    }

    // ── Lấy danh sách dịch vụ ───────────────────────────────────────────────
    /** @return DichVu[] */
    public function getAllDichVu(): array {
        $rows = $this->pdo->query("SELECT * FROM dich_vu ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => new DichVu((int)$r['id'], $r['ten_dich_vu']), $rows);
    }

    // ── Lưu đặt lịch vào DB ─────────────────────────────────────────────────
    public function insertBooking(Booking $b): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO dat_lich
                (ho_ten, sdt, email, loai_thu_cung_id, dich_vu_id, ngay_hen, gio_hen, ghi_chu, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $b->ho_ten,
            $b->sdt,
            $b->email,
            $b->loai_id,
            $b->dv_id,
            $b->ngay_hen,
            $b->gio_hen,
            $b->ghi_chu,
            $b->user_id,
        ]);
    }
}
