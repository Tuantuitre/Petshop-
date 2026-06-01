<?php
class TaiKhoan {
    public int $id;
    public string $ten_dang_nhap;
    public string $mat_khau;
    public ?string $email;
    public string $ngay_tao;

    public function __construct(
        int $id,
        string $ten_dang_nhap,
        string $mat_khau,
        ?string $email = null,
        string $ngay_tao = ''
    ) {
        $this->id = $id;
        $this->ten_dang_nhap = $ten_dang_nhap;
        $this->mat_khau = $mat_khau;
        $this->email = $email;
        $this->ngay_tao = $ngay_tao;
    }
}