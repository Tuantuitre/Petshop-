<?php
/**
 * booking_service.php
 * Chứa toàn bộ logic nghiệp vụ: validate, xử lý đặt lịch.
 * Gọi DAO để truy DB — không tự viết SQL, không có HTML.
 */

require_once __DIR__ . '/booking_dao.php';
require_once __DIR__ . '/booking_model.php';

class BookingService {

    private BookingDAO $dao;

    public function __construct(BookingDAO $dao) {
        $this->dao = $dao;
    }

    // ── Lấy danh sách loại thú cưng ─────────────────────────────────────────
    /** @return LoaiThuCung[] */
    public function getLoaiList(): array {
        return $this->dao->getAllLoai();
    }

    // ── Lấy danh sách dịch vụ ───────────────────────────────────────────────
    /** @return DichVu[] */
    public function getDichVuList(): array {
        return $this->dao->getAllDichVu();
    }

    // ── Xử lý đặt lịch ──────────────────────────────────────────────────────
    /**
     * @return array ['success'=>bool, 'error'=>string]
     */
    public function placeBooking(array $post, int $userId): array {
        $ho_ten  = trim($post['ho_ten']  ?? '');
        $sdt     = trim($post['sdt']     ?? '');
        $email   = trim($post['email']   ?? '');
        $loai_id = (int)($post['loai_thu_cung_id'] ?? 0);
        $dv_id   = (int)($post['dich_vu_id']       ?? 0);
        $ngay    = trim($post['ngay_hen'] ?? '');
        $gio     = trim($post['gio_hen']  ?? '');
        $ghi_chu = trim($post['ghi_chu']  ?? '');

        // Validate bắt buộc
        if (!$ho_ten || !$sdt || !$email || !$loai_id || !$dv_id || !$ngay || !$gio) {
            return ['success' => false, 'error' => 'Vui lòng điền đầy đủ thông tin bắt buộc!'];
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Email không hợp lệ!'];
        }

        // Validate ngày không được trong quá khứ
        if (strtotime($ngay) < strtotime(date('Y-m-d'))) {
            return ['success' => false, 'error' => 'Ngày hẹn không được là ngày trong quá khứ!'];
        }

        try {
            $booking = new Booking($ho_ten, $sdt, $email, $loai_id, $dv_id, $ngay, $gio, $ghi_chu, $userId);
            $this->dao->insertBooking($booking);
            return ['success' => true, 'error' => ''];
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Lỗi hệ thống, vui lòng thử lại! (' . $e->getMessage() . ')'];
        }
    }
}
