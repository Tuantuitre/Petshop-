<?php
/**
 * profile_dao.php
 * Chỉ chứa query DB — không có logic nghiệp vụ, không có HTML.
 */

require_once __DIR__ . '/../includes/db.php';   // $pdo
require_once __DIR__ . '/profile_model.php';

class ProfileDAO {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->ensureBookingSchema();
    }

    private function ensureBookingSchema(): void {
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
            // Migration best-effort; history queries will fail visibly if DB is not writable.
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

    // ── Lấy mat_khau + ngay_tao theo id ─────────────────────────────────────
    public function getAccountById(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT mat_khau, ngay_tao FROM tai_khoan WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ── Cập nhật mật khẩu đã hash ────────────────────────────────────────────
    public function updatePassword(int $id, string $hashed): void {
        $stmt = $this->pdo->prepare(
            "UPDATE tai_khoan SET mat_khau = ? WHERE id = ?"
        );
        $stmt->execute([$hashed, $id]);
    }

    /** @return ProfileBookingHistory[] */
    public function getBookingHistoryByUserId(int $userId): array {
        $stmt = $this->pdo->prepare("
            SELECT d.*, l.ten_loai, dv.ten_dich_vu
            FROM dat_lich d
            LEFT JOIN loai_thu_cung l ON d.loai_thu_cung_id = l.id
            LEFT JOIN dich_vu dv ON d.dich_vu_id = dv.id
            WHERE d.user_id = :user_id
               OR (
                    d.user_id IS NULL
                    AND d.email <> ''
                    AND d.email = (
                        SELECT tk.email
                        FROM tai_khoan tk
                        WHERE tk.id = :email_user_id
                          AND tk.email IS NOT NULL
                          AND tk.email <> ''
                        LIMIT 1
                    )
               )
            ORDER BY d.ngay_hen DESC, d.gio_hen DESC, d.ngay_tao DESC
        ");
        $stmt->execute([
            'user_id' => $userId,
            'email_user_id' => $userId,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => new ProfileBookingHistory($row), $rows);
    }
}
