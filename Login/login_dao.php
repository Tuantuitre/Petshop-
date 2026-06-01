<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/login_model.php';

class LoginDAO {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Lấy user theo tên đăng nhập
    public function getUserByUsername(string $username): ?TaiKhoan {
        $stmt = $this->pdo->prepare("SELECT * FROM tai_khoan WHERE ten_dang_nhap = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->rowToUser($row) : null;
    }

    // Lấy user theo email
    public function getUserByEmail(string $email): ?TaiKhoan {
        $stmt = $this->pdo->prepare("SELECT * FROM tai_khoan WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->rowToUser($row) : null;
    }

    // Tạo user mới (có thể có email hoặc không)
    public function createUser(string $username, string $passwordHash, ?string $email = null): int {
        $stmt = $this->pdo->prepare("INSERT INTO tai_khoan (ten_dang_nhap, mat_khau, email) VALUES (?, ?, ?)");
        $stmt->execute([$username, $passwordHash, $email]);
        return (int) $this->pdo->lastInsertId();
    }

    // Tạo remember token
    public function createRememberToken(int $userId, string $token, string $expires): void {
        $stmt = $this->pdo->prepare("INSERT INTO remember_tokens (user_id, token, expires) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $token, $expires]);
    }

    // Lấy user từ token còn hạn
    public function getUserByRememberToken(string $token): ?TaiKhoan {
        $stmt = $this->pdo->prepare("
            SELECT u.* FROM tai_khoan u 
            JOIN remember_tokens t ON u.id = t.user_id 
            WHERE t.token = ? AND t.expires > NOW()
        ");
        $stmt->execute([$token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->rowToUser($row) : null;
    }

    // Xoá token
    public function deleteRememberToken(string $token): void {
        $stmt = $this->pdo->prepare("DELETE FROM remember_tokens WHERE token = ?");
        $stmt->execute([$token]);
    }

    private function rowToUser(array $row): TaiKhoan {
        return new TaiKhoan(
            (int) $row['id'],
            $row['ten_dang_nhap'],
            $row['mat_khau'],
            $row['email'] ?? null,
            $row['ngay_tao'] ?? ''
        );
    }
}