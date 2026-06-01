<?php
/**
 * register_model.php
 * Chỉ giữ dữ liệu — không chứa logic, không có SQL.
 */

class RegisterInput {
    public string $username   = '';
    public string $email      = '';
    public string $password   = '';
    public string $repassword = '';

    public function __construct(array $post) {
        $this->username   = trim($post['username']   ?? '');
        $this->email      = trim($post['email']      ?? '');
        $this->password   = trim($post['password']   ?? '');
        $this->repassword = trim($post['repassword'] ?? '');
    }
}