<?php

class Service {
    public int    $id;
    public string $name;
    public string $desc;
    public float  $price;
    public string $icon;

    public function __construct(
        int    $id,
        string $name,
        string $desc,
        float  $price,
        string $icon
    ) {
        $this->id    = $id;
        $this->name  = $name;
        $this->desc  = $desc;
        $this->price = $price;
        $this->icon  = $icon;
    }
}