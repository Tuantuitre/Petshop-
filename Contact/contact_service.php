<?php
// ============================================================
// Contact_service.php
// Tầng SERVICE — Business logic + gửi mail + gọi DAO
// Trả JSON cho Controller
// Flow: UI → Controller → [Service] → DAO → DB
// ============================================================
require_once __DIR__ . '/contact_dao.php';

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ContactService {
    private ContactDAO $dao;
    private string     $phpMailerBase;

    public function __construct(PDO $pdo) {
        $this->dao           = new ContactDAO($pdo);
        $this->phpMailerBase = __DIR__ . '/../PHPMailer/PHPMailer-6.9.1/src/';
        $this->dao->ensureSchema();
    }

    // ── Entry point: xử lý form submit, trả JSON ─────────────
    public function handleSubmit(array $post): array {
        // 1. Tạo model + validate
        $msg = new ContactMessage(
            trim($post['name']    ?? ''),
            trim($post['phone']   ?? ''),
            trim($post['email']   ?? ''),
            trim($post['message'] ?? '')
        );

        $errors = $msg->validate();
        if ($errors) {
            return ['success' => false, 'msg' => implode(' ', $errors)];
        }

        // 2. Lưu vào DB
        $this->dao->insert($msg);

        // 3. Gửi mail (nếu PHPMailer có sẵn)
        $mailResult = $this->sendMail($msg);
        if (!$mailResult['success']) {
            // Vẫn báo thành công với user, log lỗi mail nội bộ
            return [
                'success' => true,
                'msg'     => 'Tin nhắn đã được lưu! (Ghi chú nội bộ: ' . $mailResult['error'] . ')',
            ];
        }

        return [
            'success' => true,
            'msg'     => 'Gửi thành công! Chúng tôi sẽ phản hồi sớm nhất có thể.',
        ];
    }

    // ── Gửi email qua PHPMailer ───────────────────────────────
    private function sendMail(ContactMessage $msg): array {
        if (!file_exists($this->phpMailerBase . 'Exception.php')) {
            return ['success' => false, 'error' => 'PHPMailer không tìm thấy'];
        }

        require_once $this->phpMailerBase . 'Exception.php';
        require_once $this->phpMailerBase . 'PHPMailer.php';
        require_once $this->phpMailerBase . 'SMTP.php';

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'gachip443h@gmail.com';
            $mail->Password   = 'saabbgrxkmirkpux';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // Bỏ qua xác minh chứng chỉ SSL (fix lỗi SSL verify failed trên localhost)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom('gachip443h@gmail.com', 'Web Mailer');
            $mail->addReplyTo($msg->email, $msg->name);
            $mail->addAddress('gachip443h@gmail.com', 'PetShop Admin');

            $mail->isHTML(true);
            $mail->Subject = '=?UTF-8?B?' . base64_encode('Liên hệ từ Website PetShop') . '?=';
            $mail->Body    = "
                <div style='font-family:sans-serif;max-width:500px'>
                    <h3 style='color:#5a7a5a'>📬 Liên hệ mới từ PetShop</h3>
                    <p><b>Họ tên:</b> " . htmlspecialchars($msg->name) . "</p>
                    <p><b>SĐT:</b> "    . htmlspecialchars($msg->phone) . "</p>
                    <p><b>Email:</b> "  . htmlspecialchars($msg->email) . "</p>
                    <p><b>Nội dung:</b><br>" . nl2br(htmlspecialchars($msg->message)) . "</p>
                </div>
            ";
            $mail->AltBody = "Họ tên: {$msg->name}\nSĐT: {$msg->phone}\nEmail: {$msg->email}\nNội dung: {$msg->message}";

            $mail->send();
            return ['success' => true];

        } catch (Exception $e) {
            return ['success' => false, 'error' => $mail->ErrorInfo];
        }
    }
}
