<?php
/**
 * profile_model.php
 * Chỉ giữ dữ liệu — không chứa logic, không có SQL.
 */

class UserProfile {
    public int    $id;
    public string $username;
    public string $ngay_tao;
    public string $avatar_letter;

    public function __construct(int $id, string $username, string $ngay_tao) {
        $this->id            = $id;
        $this->username      = $username;
        $this->ngay_tao      = $ngay_tao;
        // Lấy chữ cái đầu làm avatar
        $this->avatar_letter = mb_strtoupper(mb_substr($username, 0, 1, 'UTF-8'), 'UTF-8');
    }
}

class ProfileBookingHistory {
    public int $id;
    public string $ho_ten;
    public string $sdt;
    public string $email;
    public string $ten_loai;
    public string $ten_dich_vu;
    public string $ngay_hen;
    public string $gio_hen;
    public string $ghi_chu;
    public string $trang_thai;
    public string $ngay_tao;

    public function __construct(array $row) {
        $this->id          = (int)($row['id'] ?? 0);
        $this->ho_ten      = (string)($row['ho_ten'] ?? '');
        $this->sdt         = (string)($row['sdt'] ?? '');
        $this->email       = (string)($row['email'] ?? '');
        $this->ten_loai    = (string)($row['ten_loai'] ?? 'N/A');
        $this->ten_dich_vu = (string)($row['ten_dich_vu'] ?? 'N/A');
        $this->ngay_hen    = (string)($row['ngay_hen'] ?? '');
        $this->gio_hen     = (string)($row['gio_hen'] ?? '');
        $this->ghi_chu     = (string)($row['ghi_chu'] ?? '');
        $this->trang_thai  = (string)($row['trang_thai'] ?? 'cho_xu_ly');
        $this->ngay_tao    = (string)($row['ngay_tao'] ?? '');
    }

    public function ngayHenFormatted(): string {
        return $this->ngay_hen ? date('d/m/Y', strtotime($this->ngay_hen)) : 'N/A';
    }

    public function gioHenFormatted(): string {
        return $this->gio_hen ? substr($this->gio_hen, 0, 5) : 'N/A';
    }

    public function ngayTaoFormatted(): string {
        return $this->ngay_tao ? date('d/m/Y H:i', strtotime($this->ngay_tao)) : 'N/A';
    }

    public function statusLabel(): string {
        return [
            'cho_xu_ly'     => 'Chờ xử lý',
            'dang_cho_lich' => 'Đang chờ lịch',
            'hoan_thanh'    => 'Hoàn thành',
            'huy'           => 'Đã hủy',
        ][$this->trang_thai] ?? 'Không xác định';
    }

    public function statusClass(): string {
        return [
            'cho_xu_ly'     => 'status-pending',
            'dang_cho_lich' => 'status-waiting',
            'hoan_thanh'    => 'status-done',
            'huy'           => 'status-cancelled',
        ][$this->trang_thai] ?? 'status-pending';
    }

    public function customerStatusLabel(): string {
        return [
            'cho_xu_ly'     => 'Chờ admin xác nhận',
            'dang_cho_lich' => 'Đã đồng ý - chờ ngày hẹn',
            'hoan_thanh'    => 'Đã hoàn thành',
            'huy'           => 'Đã hủy',
        ][$this->trang_thai] ?? 'Không xác định';
    }

    public function statusMessage(): string {
        if ($this->trang_thai === 'cho_xu_ly') {
            return 'Lịch của bạn đã được gửi. PetShop đang kiểm tra và sẽ xác nhận trong thời gian sớm nhất.';
        }

        if ($this->trang_thai === 'dang_cho_lich') {
            if (!$this->ngay_hen) {
                return 'Admin đã đồng ý lịch của bạn. Lịch đang ở trạng thái chờ đến ngày hẹn để thực hiện dịch vụ.';
            }

            $today = new DateTimeImmutable(date('Y-m-d'));
            $appointment = new DateTimeImmutable(date('Y-m-d', strtotime($this->ngay_hen)));

            if ($appointment > $today) {
                $daysLeft = (int)$today->diff($appointment)->format('%a');
                return 'Admin đã đồng ý lịch của bạn. Hiện đang chờ đến ngày hẹn '
                    . $this->ngayHenFormatted() . ' lúc ' . $this->gioHenFormatted()
                    . ' để thực hiện dịch vụ, còn ' . $daysLeft . ' ngày.';
            }

            if ($appointment == $today) {
                return 'Hôm nay là ngày hẹn. Vui lòng đến đúng giờ ' . $this->gioHenFormatted()
                    . '. Sau khi dịch vụ xong, admin sẽ chuyển lịch sang hoàn thành.';
            }

            return 'Lịch đã qua ngày hẹn. PetShop đang chờ admin kiểm tra và chuyển sang hoàn thành sau khi dịch vụ đã xong.';
        }

        if ($this->trang_thai === 'hoan_thanh') {
            return 'Admin đã xác nhận lịch này hoàn thành. Cảm ơn bạn đã sử dụng dịch vụ của PetShop.';
        }

        if ($this->trang_thai === 'huy') {
            return 'Lịch hẹn này đã bị hủy. Bạn có thể đặt lịch mới nếu vẫn cần sử dụng dịch vụ.';
        }

        return 'Trạng thái lịch hẹn chưa xác định. Vui lòng liên hệ PetShop để được hỗ trợ.';
    }
}
