<?php

class BlogPost {
    public int    $id;
    public string $title;
    public string $excerpt;
    public string $img;
    public string $date;

    public function __construct(
        int    $id,
        string $title,
        string $excerpt,
        string $img,
        string $date
    ) {
        $this->id      = $id;
        $this->title   = $title;
        $this->excerpt = $excerpt;
        $this->img     = $img;
        $this->date    = $date;
    }
}