<?php

class Product {
    public int    $id;
    public string $name;
    public string $desc;
    public float  $price;
    public string $img;
    public string $cat;
    public int    $so_luong;
    public int    $loai_id;
    public string $loai_name;
    public int    $luot_mua;

    public function __construct(
        int    $id,
        string $name,
        string $desc,
        float  $price,
        string $img,
        string $cat,
        int    $so_luong,
        int    $loai_id = 0,
        string $loai_name = '',
        int    $luot_mua = 0
    ) {
        $this->id       = $id;
        $this->name     = $name;
        $this->desc     = $desc;
        $this->price    = $price;
        $this->img      = $img;
        $this->cat      = $cat;
        $this->so_luong = $so_luong;
        $this->loai_id  = $loai_id;
        $this->loai_name = $loai_name;
        $this->luot_mua = $luot_mua;
    }

    /** Còn hàng không? */
    public function inStock(): bool {
        return $this->so_luong > 0;
    }

    /** Sắp hết hàng? */
    public function isLowStock(): bool {
        return $this->so_luong > 0 && $this->so_luong < 10;
    }
}
