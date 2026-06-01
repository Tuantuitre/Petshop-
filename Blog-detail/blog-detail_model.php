<?php
/**
 * blog-detail_model.php
 * Chỉ giữ dữ liệu — không chứa logic, không có SQL.
 */

class BlogDetail {
    public int    $id;
    public string $title;
    public string $date;
    public string $img;
    public string $content;
    public string $nguyen_nhan_pho_bien;
    public string $hinh_anh_2;
    public string $huong_dan;
    public string $hinh_anh_3;
    public string $cach_cham;
    public string $hinh_anh_4;

    public function __construct(array $row) {
        $this->id                   = (int)($row['id']                   ?? 0);
        $this->title                = $row['title']                      ?? '';
        $this->date                 = $row['date']                       ?? '';
        $this->img                  = $row['img']                        ?? '';
        $this->content              = $row['content']                    ?? '';
        $this->nguyen_nhan_pho_bien = $row['nguyen_nhan_pho_bien']       ?? '';
        $this->hinh_anh_2           = $row['hinh_anh_2']                 ?? '';
        $this->huong_dan            = $row['huong_dan']                  ?? '';
        $this->hinh_anh_3           = $row['hinh_anh_3']                 ?? '';
        $this->cach_cham            = $row['cach_cham']                  ?? '';
        $this->hinh_anh_4           = $row['hinh_anh_4']                 ?? '';
    }
}

class FeaturedPost {
    public int    $id;
    public string $title;
    public string $date;
    public string $img;

    public function __construct(int $id, string $title, string $date, string $img) {
        $this->id    = $id;
        $this->title = $title;
        $this->date  = $date;
        $this->img   = $img;
    }
}