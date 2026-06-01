<?php
// ============================================================
// Contact_dao.php
// Tầng DAO — Toàn bộ SQL nằm ở đây
// Flow: UI → Controller → Service → [DAO] → DB
// ============================================================
require_once __DIR__ . '/contact_model.php';

class ContactDAO {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // ── Đảm bảo bảng lien_he tồn tại ────────────────────────
    public function ensureSchema(): void {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS lien_he (
                id         INT AUTO_INCREMENT PRIMARY KEY,
                ho_ten     VARCHAR(255) NOT NULL,
                sdt        VARCHAR(20)  NOT NULL,
                email      VARCHAR(255) NOT NULL,
                noi_dung   TEXT         NOT NULL,
                ngay_gui   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    // ── Lưu tin nhắn vào DB ───────────────────────────────────
    public function insert(ContactMessage $msg): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO lien_he (ho_ten, sdt, email, noi_dung)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([
            $msg->name,
            $msg->phone,
            $msg->email,
            $msg->message,
        ]);
    }
}
