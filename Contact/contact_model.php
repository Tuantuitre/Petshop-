<?php
// ============================================================
// Contact_model.php
// Tầng MODEL — Định nghĩa cấu trúc dữ liệu
// Flow: UI → Controller → Service → DAO → DB
// ============================================================

class ContactMessage {
    public string $name;
    public string $phone;
    public string $email;
    public string $message;

    public function __construct(string $name, string $phone, string $email, string $message) {
        $this->name    = $name;
        $this->phone   = $phone;
        $this->email   = $email;
        $this->message = $message;
    }

    // ── Validate cơ bản ──────────────────────────────────────
    public function validate(): array {
        $errors = [];
        if (!$this->name)    $errors[] = 'Vui lòng nhập họ và tên.';
        if (!$this->phone)   $errors[] = 'Vui lòng nhập số điện thoại.';
        if (!preg_match('/^[0-9]{10,11}$/', $this->phone))
                             $errors[] = 'Số điện thoại không hợp lệ (10-11 chữ số).';
        if (!$this->email)   $errors[] = 'Vui lòng nhập email.';
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL))
                             $errors[] = 'Email không đúng định dạng.';
        if (!$this->message) $errors[] = 'Vui lòng nhập nội dung tin nhắn.';
        return $errors;
    }
}
