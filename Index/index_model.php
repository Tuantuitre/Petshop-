<?php

class HomeProduct {
    public int $id;
    public string $name;
    public float $price;
    public string $desc;
    public string $cat;
    public string $img;
    public int $luot_mua;

    public function __construct(
        int $id,
        string $name,
        float $price,
        string $desc,
        string $cat,
        string $img,
        int $luot_mua
    ) {
        $this->id        = $id;
        $this->name      = $name;
        $this->price     = $price;
        $this->desc      = $desc;
        $this->cat       = $cat;
        $this->img       = $img;
        $this->luot_mua  = $luot_mua;
    }

    public static function fromArray(array $row): self {
        return new self(
            (int)($row['id'] ?? 0),
            $row['name'] ?? '',
            (float)($row['price'] ?? 0),
            $row['desc'] ?? '',
            $row['cat'] ?? '',
            $row['img'] ?? '',
            (int)($row['luot_mua'] ?? 0)
        );
    }

    public function toArray(): array {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'price'     => $this->price,
            'desc'      => $this->desc,
            'cat'       => $this->cat,
            'img'       => $this->img,
            'luot_mua'  => $this->luot_mua,
        ];
    }
}

class HomeServiceItem {
    public int $id;
    public string $name;
    public string $desc;
    public float $price;
    public string $icon;

    public function __construct(
        int $id,
        string $name,
        string $desc,
        float $price,
        string $icon
    ) {
        $this->id    = $id;
        $this->name  = $name;
        $this->desc  = $desc;
        $this->price = $price;
        $this->icon  = $icon;
    }

    public function toArray(): array {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'desc'  => $this->desc,
            'price' => $this->price,
            'icon'  => $this->icon,
        ];
    }
}

class HomeBlogPost {
    public int $id;
    public string $title;
    public string $excerpt;
    public string $img;
    public int $luot_doc;
    public string $date;

    public function __construct(
        int $id,
        string $title,
        string $excerpt,
        string $img,
        int $luot_doc,
        string $date
    ) {
        $this->id       = $id;
        $this->title    = $title;
        $this->excerpt  = $excerpt;
        $this->img      = $img;
        $this->luot_doc = $luot_doc;
        $this->date     = $date;
    }

    public static function fromArray(array $row): self {
        $img = $row['img'] ?? '';
        if ($img !== '' && !str_starts_with($img, 'assets/') && !str_starts_with($img, 'http')) {
            $img = 'assets/images/' . $img;
        }

        $excerpt = trim(strip_tags($row['excerpt'] ?? ''));
        if ($excerpt !== '') {
            $excerpt = mb_substr($excerpt, 0, 120) . '...';
        }

        return new self(
            (int)($row['id'] ?? 0),
            $row['title'] ?? '',
            $excerpt,
            $img,
            (int)($row['luot_doc'] ?? 0),
            $row['date'] ?? ''
        );
    }

    public function toArray(): array {
        return [
            'id'       => $this->id,
            'title'    => $this->title,
            'excerpt'  => $this->excerpt,
            'img'      => $this->img,
            'luot_doc' => $this->luot_doc,
            'date'     => $this->date,
        ];
    }
}
