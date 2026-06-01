<?php
/**
 * register_dao.php
 * Chỉ chứa query DB — không có logic nghiệp vụ, không có HTML.
 */

require_once __DIR__ . '/../includes/db.php';   // $pdo
require_once __DIR__ . '/register_model.php';

class RegisterDAO {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // ── Kiểm tra tên đăng nhập đã tồn tại chưa ──────────────────────────────
    public function usernameExists(string $username): bool {
        $stmt = $this->pdo->prepare("SELECT id FROM tai_khoan WHERE ten_dang_nhap = ?");
        $stmt->execute([$username]);
        return (bool) $stmt->fetch();
    }

    // ── Tạo tài khoản mới ────────────────────────────────────────────────────
    public function createUser(string $username, string $email, string $hashedPassword): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO tai_khoan (ten_dang_nhap, mat_khau, email) VALUES (?, ?, ?)"
        );
        $stmt->execute([$username, $hashedPassword, $email]);
        return (int) $this->pdo->lastInsertId();
    }
}