<?php
/**
 * booking_model.php
 * Chỉ giữ dữ liệu — không chứa logic, không có SQL.
 */

class Booking {
    public int    $id         = 0;
    public ?int   $user_id    = null;
    public string $ho_ten     = '';
    public string $sdt        = '';
    public string $email      = '';
    public int    $loai_id    = 0;
    public int    $dv_id      = 0;
    public string $ngay_hen   = '';
    public string $gio_hen    = '';
    public string $ghi_chu    = '';

    public function __construct(
        string $ho_ten,
        string $sdt,
        string $email,
        int    $loai_id,
        int    $dv_id,
        string $ngay_hen,
        string $gio_hen,
        string $ghi_chu = '',
        ?int   $user_id = null
    ) {
        $this->ho_ten   = $ho_ten;
        $this->sdt      = $sdt;
        $this->email    = $email;
        $this->loai_id  = $loai_id;
        $this->dv_id    = $dv_id;
        $this->ngay_hen = $ngay_hen;
        $this->gio_hen  = $gio_hen;
        $this->ghi_chu  = $ghi_chu;
        $this->user_id  = $user_id;
    }
}

// ── Loại thú cưng (từ DB) ────────────────────────────────────────────────────
class LoaiThuCung {
    public int    $id;
    public string $ten_loai;

    public function __construct(int $id, string $ten_loai) {
        $this->id       = $id;
        $this->ten_loai = $ten_loai;
    }
}

// ── Dịch vụ (từ DB) ──────────────────────────────────────────────────────────
class DichVu {
    public int    $id;
    public string $ten_dich_vu;

    public function __construct(int $id, string $ten_dich_vu) {
        $this->id          = $id;
        $this->ten_dich_vu = $ten_dich_vu;
    }
}
