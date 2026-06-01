<?php
/**
 * shopcard_model.php
 * Chỉ giữ dữ liệu — không chứa logic, không có SQL.
 */

// ── CartItem ──────────────────────────────────────────────────────────────────
class CartItem {
    public int    $id;
    public string $name;
    public float  $price;
    public string $img;
    public int    $qty;

    public function __construct(int $id, string $name, float $price, string $img, int $qty = 1) {
        $this->id    = $id;
        $this->name  = $name;
        $this->price = $price;
        $this->img   = $img;
        $this->qty   = $qty;
    }

    public function subtotal(): float {
        return $this->price * $this->qty;
    }
}

// ── Order ────────────────────────────────────────────────────────────────────
class Order {
    public ?int   $id          = null;
    public string $ho_ten      = '';
    public string $sdt         = '';
    public string $dia_chi     = '';
    public string $phuong_thuc = 'COD';
    public float  $tong_tien   = 0;
    public string $trang_thai  = 'cho_xu_ly';

    public function __construct(
        string $ho_ten,
        string $sdt,
        string $dia_chi,
        string $phuong_thuc,
        float  $tong_tien
    ) {
        $this->ho_ten      = $ho_ten;
        $this->sdt         = $sdt;
        $this->dia_chi     = $dia_chi;
        $this->phuong_thuc = $phuong_thuc;
        $this->tong_tien   = $tong_tien;
    }
}

// ── OrderDetail ───────────────────────────────────────────────────────────────
class OrderDetail {
    public int    $don_hang_id  = 0;
    public int    $san_pham_id  = 0;
    public string $ten_san_pham = '';
    public float  $gia          = 0;
    public int    $so_luong     = 1;
    public float  $thanh_tien   = 0;

    public function __construct(
        int    $don_hang_id,
        int    $san_pham_id,
        string $ten_san_pham,
        float  $gia,
        int    $so_luong
    ) {
        $this->don_hang_id  = $don_hang_id;
        $this->san_pham_id  = $san_pham_id;
        $this->ten_san_pham = $ten_san_pham;
        $this->gia          = $gia;
        $this->so_luong     = $so_luong;
        $this->thanh_tien   = $gia * $so_luong;
    }
}

// ── OrderHistory — lịch sử đơn hàng theo user ────────────────────────────────
class OrderHistory {
    public int    $id;
    public string $ho_ten;
    public string $sdt;
    public string $dia_chi;
    public string $phuong_thuc;
    public float  $tong_tien;
    public string $trang_thai;
    public string $ngay_dat;
    public bool   $can_edit_info = false;
    /** @var OrderHistoryItem[] */
    public array  $items = [];

    public function trangThaiLabel(): string {
        return match($this->trang_thai) {
            'cho_xu_ly'  => '⏳ Chờ xử lý',
            'hoan_thanh' => '✅ Hoàn thành',
            'huy'        => '❌ Đã hủy',
            default      => $this->trang_thai,
        };
    }

    public function trangThaiClass(): string {
        return match($this->trang_thai) {
            'cho_xu_ly'  => 'status-pending',
            'hoan_thanh' => 'status-done',
            'huy'        => 'status-cancel',
            default      => '',
        };
    }

    public function canEditInfo(): bool {
        return $this->can_edit_info;
    }

    public static function fromArray(array $row): self {
        $obj              = new self();
        $obj->id          = (int)$row['id'];
        $obj->ho_ten      = $row['ho_ten']        ?? '';
        $obj->sdt         = $row['sdt']            ?? '';
        $obj->dia_chi     = $row['dia_chi']        ?? '';
        $obj->phuong_thuc = $row['phuong_thuc_tt'] ?? 'COD';
        $obj->tong_tien   = (float)($row['tong_tien'] ?? 0);
        $obj->trang_thai  = $row['trang_thai']     ?? 'cho_xu_ly';
        $obj->ngay_dat    = $row['ngay_dat']       ?? '';
        $obj->can_edit_info = !empty($row['can_edit_info']);
        return $obj;
    }
}

// ── OrderHistoryItem — chi tiết sản phẩm trong lịch sử ───────────────────────
class OrderHistoryItem {
    public string  $ten_san_pham;
    public float   $gia;
    public int     $so_luong;
    public float   $thanh_tien;
    public ?string $hinh_anh;

    public static function fromArray(array $row): self {
        $obj               = new self();
        $obj->ten_san_pham = $row['ten_san_pham'] ?? '';
        $obj->gia          = (float)($row['gia']       ?? 0);
        $obj->so_luong     = (int)($row['so_luong']    ?? 1);
        $obj->thanh_tien   = (float)($row['thanh_tien'] ?? 0);
        $obj->hinh_anh     = $row['hinh_anh']          ?? null;
        return $obj;
    }
}
