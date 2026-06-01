<?php

class DatLichModel {
    public int    $id          = 0;
    public string $ho_ten      = '';
    public string $sdt         = '';
    public string $email       = '';
    public string $ngay_hen    = '';
    public string $gio_hen     = '';
    public string $trang_thai  = 'cho_xu_ly';   // cho_xu_ly | hoan_thanh | huy
    public string $ghi_chu     = '';
    public string $ten_loai    = '';
    public string $ten_dich_vu = '';
    public string $ngay_tao    = '';

    public function __construct(array $data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}

// ─────────────────────────────────────────────────────────────

class SanPhamModel {
    public int    $id           = 0;
    public string $ten_san_pham = '';
    public float  $gia          = 0;
    public string $mo_ta        = '';
    public int    $loai_id      = 1;
    public int    $so_luong     = 100;
    public string $danh_muc     = 'all';
    public string $hinh_anh     = '';
    public string $ten_loai     = '';

    public function __construct(array $data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}

// ─────────────────────────────────────────────────────────────

class DonHangModel {
    public int    $id             = 0;
    public string $ho_ten         = '';
    public string $sdt            = '';
    public string $dia_chi        = '';
    public string $phuong_thuc_tt = 'COD';
    public float  $tong_tien      = 0;
    public string $trang_thai     = 'cho_xu_ly';   // cho_xu_ly | hoan_thanh | huy
    public string $ngay_dat       = '';

    public function __construct(array $data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}

// ─────────────────────────────────────────────────────────────

class ChiTietDonHangModel {
    public int    $id          = 0;
    public int    $don_hang_id = 0;
    public int    $san_pham_id = 0;
    public string $ten_san_pham= '';
    public float  $gia         = 0;
    public int    $so_luong    = 1;
    public float  $thanh_tien  = 0;

    public function __construct(array $data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}

// ─────────────────────────────────────────────────────────────

class TinTucModel {
    public int    $id                    = 0;
    public string $tieu_de               = '';
    public string $noi_dung_chinh        = '';
    public string $hinh_anh_1            = '';
    public string $nguyen_nhan_pho_bien  = '';
    public string $hinh_anh_2            = '';
    public string $huong_dan             = '';
    public string $hinh_anh_3            = '';
    public string $cach_cham             = '';
    public string $hinh_anh_4            = '';
    public string $email_nhan_thong_bao  = '';
    public string $ngay_dang             = '';

    public function __construct(array $data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}

// ─────────────────────────────────────────────────────────────

class LoaiThuCungModel {
    public int    $id       = 0;
    public string $ten_loai = '';

    public function __construct(array $data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}