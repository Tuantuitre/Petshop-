<?php
/**
 * register_service.php
 * Chứa toàn bộ logic nghiệp vụ: validate + tạo tài khoản.
 * Không có SQL, không có HTML.
 */

require_once __DIR__ . '/register_dao.php';
require_once __DIR__ . '/register_model.php';

class RegisterService {

    private RegisterDAO $dao;

    public function __construct(RegisterDAO $dao) {
        $this->dao = $dao;
    }

    /**
     * Xử lý đăng ký tài khoản mới.
     * @return array ['success'=>bool, 'error'=>string]
     */
    public function register(RegisterInput $input): array {
        // Validate bắt buộc
        if (!$input->username || !$input->email || !$input->password || !$input->repassword) {
            return ['success' => false, 'error' => 'Vui lòng điền đầy đủ thông tin!'];
        }

        // Validate email
        if (!filter_var($input->email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Email không hợp lệ!'];
        }

        // Validate độ dài mật khẩu
        if (strlen($input->password) < 6) {
            return ['success' => false, 'error' => 'Mật khẩu phải có ít nhất 6 ký tự!'];
        }

        // Validate xác nhận mật khẩu
        if ($input->password !== $input->repassword) {
            return ['success' => false, 'error' => 'Mật khẩu xác nhận không khớp!'];
        }

        try {
            // Kiểm tra trùng tên đăng nhập
            if ($this->dao->usernameExists($input->username)) {
                return ['success' => false, 'error' => 'Tên đăng nhập đã tồn tại, vui lòng chọn tên khác!'];
            }

            // Tạo tài khoản
            $hashed = password_hash($input->password, PASSWORD_DEFAULT);
            $this->dao->createUser($input->username, $input->email, $hashed);

            return ['success' => true, 'error' => ''];

        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Lỗi hệ thống, vui lòng thử lại sau!'];
        }
    }
}