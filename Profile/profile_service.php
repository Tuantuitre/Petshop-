<?php
/**
 * profile_service.php
 * Chứa toàn bộ logic nghiệp vụ: lấy profile, validate & đổi mật khẩu.
 * Không có SQL, không có HTML.
 */

require_once __DIR__ . '/profile_dao.php';
require_once __DIR__ . '/profile_model.php';

class ProfileService {

    private ProfileDAO $dao;

    public function __construct(ProfileDAO $dao) {
        $this->dao = $dao;
    }

    // ── Lấy thông tin profile ────────────────────────────────────────────────
    public function getProfile(int $user_id, string $username): UserProfile {
        try {
            $account  = $this->dao->getAccountById($user_id);
            $ngay_tao = ($account && !empty($account['ngay_tao']))
                ? date('d/m/Y', strtotime($account['ngay_tao']))
                : 'N/A';
        } catch (PDOException $e) {
            $ngay_tao = 'N/A';
        }

        return new UserProfile($user_id, $username, $ngay_tao);
    }

    /** @return ProfileBookingHistory[] */
    public function getBookingHistory(int $user_id): array {
        try {
            return $this->dao->getBookingHistoryByUserId($user_id);
        } catch (PDOException $e) {
            return [];
        }
    }

    // ── Đổi mật khẩu ────────────────────────────────────────────────────────
    /**
     * @return array ['success'=>bool, 'message'=>string]
     */
    public function changePassword(int $user_id, array $post): array {
        $current = trim($post['current_password']  ?? '');
        $new_pw  = trim($post['new_password']       ?? '');
        $confirm = trim($post['confirm_password']   ?? '');

        if (!$current || !$new_pw || !$confirm) {
            return ['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin!'];
        }
        if (strlen($new_pw) < 6) {
            return ['success' => false, 'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự!'];
        }
        if ($new_pw !== $confirm) {
            return ['success' => false, 'message' => 'Mật khẩu xác nhận không khớp!'];
        }

        try {
            $account = $this->dao->getAccountById($user_id);

            if ($account && password_verify($current, $account['mat_khau'])) {
                $this->dao->updatePassword($user_id, password_hash($new_pw, PASSWORD_DEFAULT));
                return ['success' => true, 'message' => 'Đổi mật khẩu thành công!'];
            }

            return ['success' => false, 'message' => 'Mật khẩu hiện tại không đúng!'];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại!'];
        }
    }
}
