<?php
require_once __DIR__ . '/login_dao.php';
require_once __DIR__ . '/login_model.php';

class LoginService {
    private LoginDAO $dao;
    private string $googleClientId = '478995794288-8rtr3d27ip8vmqcoi761oq3q47k8e39h.apps.googleusercontent.com';

    public function __construct(LoginDAO $dao) {
        $this->dao = $dao;
    }

    // ── Đăng nhập thường ─────────────────────────────────────
    public function login(string $username, string $password, bool $remember): array {
        $user = $this->dao->getUserByUsername($username);
        if (!$user || !password_verify($password, $user->mat_khau)) {
            return ['success' => false, 'error' => 'Tên đăng nhập hoặc mật khẩu không đúng!'];
        }
        $this->completeLogin($user, $remember);
        return ['success' => true];
    }

    // ── Đăng nhập bằng Google (bước 1: nhận token) ─────────
    public function loginWithGoogle(string $idToken, bool $remember): array {
        $tokenInfo = $this->verifyGoogleToken($idToken);
        if (!$tokenInfo || ($tokenInfo->aud ?? '') !== $this->googleClientId) {
            return ['success' => false, 'error' => 'Token Google không hợp lệ!'];
        }

        $email = $tokenInfo->email ?? '';
        $name  = $tokenInfo->name  ?? '';
        if (!$email) {
            return ['success' => false, 'error' => 'Không lấy được email từ Google!'];
        }

        $user = $this->dao->getUserByEmail($email);
        if ($user) {
            // Email đã tồn tại => đăng nhập luôn
            $this->completeLogin($user, $remember);
            return ['success' => true];
        }

        // Email chưa có: lưu tạm vào session và yêu cầu nhập username
        $_SESSION['google_temp'] = [
            'email' => $email,
            'name'  => $name,
        ];
        return ['success' => false, 'need_username' => true];
    }

    // ── Hoàn tất đăng ký Google (bước 2: nhận username) ──────
    public function completeGoogleSignup(string $username, bool $remember): array {
        if (!isset($_SESSION['google_temp'])) {
            return ['success' => false, 'error' => 'Phiên làm việc đã hết hạn, vui lòng thử lại!'];
        }

        $googleData = $_SESSION['google_temp'];
        $email = $googleData['email'];

        // Kiểm tra username đã tồn tại chưa
        if ($this->dao->getUserByUsername($username)) {
            return ['success' => false, 'error' => 'Tên đăng nhập đã có người dùng, vui lòng chọn tên khác!'];
        }

        // Tạo tài khoản với username được chọn
        $randomPass = bin2hex(random_bytes(16));
        $hashedPass = password_hash($randomPass, PASSWORD_DEFAULT);
        $userId = $this->dao->createUser($username, $hashedPass, $email);

        // Xóa session tạm
        unset($_SESSION['google_temp']);

        // Đăng nhập
        $user = new TaiKhoan($userId, $username, $hashedPass, $email, date('Y-m-d H:i:s'));
        $this->completeLogin($user, $remember);
        return ['success' => true];
    }

    // ── Tự động đăng nhập bằng Remember Token ────────────────
    public function tryAutoLogin(): void {
        if (isset($_COOKIE['remember_token'])) {
            $token = $_COOKIE['remember_token'];
            $user = $this->dao->getUserByRememberToken($token);
            if ($user) {
                $this->dao->deleteRememberToken($token);
                $this->completeLogin($user, false);
                $this->setRememberCookie($user->id);
            } else {
                setcookie('remember_token', '', time() - 3600, '/');
            }
        }
    }

    // ── Đăng xuất ────────────────────────────────────────────
    public function logout(): void {
        session_destroy();
        if (isset($_COOKIE['remember_token'])) {
            $this->dao->deleteRememberToken($_COOKIE['remember_token']);
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }

    // ── Private helpers ──────────────────────────────────────
    private function completeLogin(TaiKhoan $user, bool $remember): void {
        $_SESSION['user_id']  = $user->id;
        $_SESSION['username'] = $user->ten_dang_nhap;
        $_SESSION['email']    = $user->email;
        if ($remember) {
            $this->setRememberCookie($user->id);
        }
    }

    private function setRememberCookie(int $userId): void {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        $this->dao->createRememberToken($userId, $token, $expires);
        setcookie('remember_token', $token, time() + 60*60*24*30, '/', '', true, true);
    }

    private function verifyGoogleToken(string $idToken): ?object {
        $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);
        $opts = [
            'http' => [
                'method' => 'GET',
                'ignore_errors' => true,
            ],
        ];
        $context = stream_context_create($opts);
        $response = @file_get_contents($url, false, $context);
        if ($response === false) return null;
        $data = json_decode($response);
        if (!$data || isset($data->error)) return null;
        return $data;
    }
}