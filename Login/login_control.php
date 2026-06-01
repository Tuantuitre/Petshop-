<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../includes/db.php';   // $pdo
require_once __DIR__ . '/login_dao.php';
require_once __DIR__ . '/login_service.php';

// Khởi tạo
$dao     = new LoginDAO($pdo);
$service = new LoginService($dao);

// 1. Đăng xuất
if (isset($_GET['logout'])) {
    $service->logout();
    header('Location: index.php');
    exit;
}

// 2. Tự động đăng nhập bằng cookie (nếu chưa đăng nhập)
if (!isset($_SESSION['user_id'])) {
    $service->tryAutoLogin();
    if (isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}

// 3. Xử lý POST
$error = '';

// ── Đăng nhập thường ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_normal'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = isset($_POST['remember']);
    if (!$username || !$password) {
        $error = 'Vui lòng điền đầy đủ thông tin!';
    } else {
        $result = $service->login($username, $password, $remember);
        if ($result['success']) {
            header('Location: index.php');
            exit;
        } else {
            $error = $result['error'];
        }
    }
}

// ── Đăng nhập Google (AJAX) - bước 1: nhận token ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['google_id_token'])) {
    $idToken  = $_POST['google_id_token'];
    $remember = ($_POST['remember'] ?? '0') === '1';
    $result   = $service->loginWithGoogle($idToken, $remember);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

// ── Đăng nhập Google bước 2: nhận username ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['google_username'])) {
    $username = trim($_POST['username'] ?? '');
    $remember = ($_POST['remember'] ?? '0') === '1';
    if ($username === '') {
        $result = ['success' => false, 'error' => 'Tên đăng nhập không được để trống!'];
    } else {
        $result = $service->completeGoogleSignup($username, $remember);
    }
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

// 4. Nạp giao diện
require_once __DIR__ . '/login_ui.php';