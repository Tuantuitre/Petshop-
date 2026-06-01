<?php
/**
 * profile_ui.php
 * Toàn bộ HTML + CSS inline + JS.
 * Không cần file profile.css ngoài.
 * Nhận biến từ profile_control.php:
 *   $profile      UserProfile
 *   $success_msg  string
 *   $error_msg    string
 */
$booking_history = $booking_history ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ho so - PetShop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root {
    --cream:       #fdf8f2;
    --cream-dark:  #f5ede0;
    --green:       #2d6a4f;
    --green-light: #40916c;
    --green-pale:  #d8f3dc;
    --amber:       #f4a261;
    --red:         #e63946;
    --red-pale:    #fde8ea;
    --text:        #1a1a2e;
    --text-muted:  #7a7a8c;
    --border:      #e8ddd0;
    --white:       #ffffff;
    --shadow-sm:   0 2px 12px rgba(45,106,79,0.08);
    --shadow-md:   0 8px 32px rgba(45,106,79,0.12);
    --radius:      16px;
    --radius-sm:   10px;
    --sidebar-w:   270px;
    --trans:       0.25s cubic-bezier(0.4,0,0.2,1);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'DM Sans', sans-serif;
    background: var(--cream);
    color: var(--text);
    min-height: 100vh;
    overflow-x: hidden;
}

/* -- Background blobs -- */
.bg-blob {
    position: fixed;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.35;
    pointer-events: none;
    z-index: 0;
}
.blob-1 {
    width: 420px; height: 420px;
    background: radial-gradient(circle, rgba(216,243,220,0.9), rgba(216,243,220,0));
    top: -100px; left: -100px;
    animation: floatBlob 10s ease-in-out infinite;
}
.blob-2 {
    width: 350px; height: 350px;
    background: radial-gradient(circle, rgba(253,232,212,0.9), rgba(253,232,212,0));
    bottom: -80px; right: -80px;
    animation: floatBlob 13s ease-in-out infinite reverse;
}
.blob-3 {
    width: 250px; height: 250px;
    background: radial-gradient(circle, rgba(253,232,234,0.9), rgba(253,232,234,0));
    top: 50%; left: 40%;
    animation: floatBlob 16s ease-in-out infinite;
}
@keyframes floatBlob {
    0%, 100% { transform: translate(0,0) scale(1); }
    50%       { transform: translate(20px,30px) scale(1.05); }
}

/* -- Layout -- */
.page-wrapper {
    position: relative; z-index: 1;
    display: flex; min-height: 100vh;
}

/* -- SIDEBAR -- */
.sidebar {
    width: var(--sidebar-w);
    min-height: 100vh;
    background: var(--green);
    display: flex; flex-direction: column;
    padding: 28px 20px 24px;
    position: fixed; top: 0; left: 0;
    z-index: 100;
    box-shadow: 4px 0 24px rgba(45,106,79,0.20);
}
.sidebar-logo {
    display: flex; align-items: center; gap: 10px;
    text-decoration: none; margin-bottom: 32px;
}
.logo-paw {
    font-size: 1.8rem;
    animation: pawBounce 2s ease-in-out infinite;
}
@keyframes pawBounce {
    0%, 100% { transform: rotate(-8deg); }
    50%       { transform: rotate(8deg); }
}
.logo-text {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem; color: var(--white); letter-spacing: 0.5px;
}
.avatar-block {
    display: flex; align-items: center; gap: 14px;
    background: rgba(255,255,255,0.10);
    border-radius: var(--radius-sm);
    padding: 14px 16px; margin-bottom: 28px;
}
.avatar-ring {
    width: 52px; height: 52px; border-radius: 50%;
    background: linear-gradient(135deg, var(--amber), #e76f51);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.25); flex-shrink: 0;
}
.avatar {
    font-family: 'Playfair Display', serif;
    font-size: 1.4rem; font-weight: 700; color: var(--white);
}
.avatar-name {
    font-weight: 600; font-size: 0.95rem; color: var(--white);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.avatar-since {
    font-size: 0.75rem; color: rgba(255,255,255,0.60); margin-top: 2px;
}
.sidebar-nav { display: flex; flex-direction: column; gap: 4px; flex: 1; }
.nav-item {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 16px; border-radius: var(--radius-sm);
    color: rgba(255,255,255,0.72); text-decoration: none;
    font-size: 0.92rem; font-weight: 500;
    transition: background var(--trans), color var(--trans), transform var(--trans);
    cursor: pointer;
}
.nav-item svg {
    width: 18px; height: 18px; stroke: currentColor; fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0;
}
.nav-item:hover { background: rgba(255,255,255,0.12); color: var(--white); transform: translateX(4px); }
.nav-item.active { background: rgba(255,255,255,0.18); color: var(--white); font-weight: 600; }
.logout-btn {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 16px; border-radius: var(--radius-sm);
    color: rgba(255,180,180,0.85); text-decoration: none;
    font-size: 0.92rem; font-weight: 500; margin-top: 12px;
    border: 1px solid rgba(255,150,150,0.25);
    transition: background var(--trans), color var(--trans);
}
.logout-btn svg {
    width: 18px; height: 18px; stroke: currentColor; fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0;
}
.logout-btn:hover { background: rgba(230,57,70,0.20); color: #ffb3b3; }

/* -- MAIN -- */
.main-content {
    margin-left: var(--sidebar-w); flex: 1;
    padding: 48px 48px 64px; min-height: 100vh;
}

/* Alert */
.alert {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 20px; border-radius: var(--radius-sm);
    font-size: 0.92rem; font-weight: 500;
    margin-bottom: 28px; transition: opacity 0.6s ease;
}
.alert svg {
    width: 18px; height: 18px; stroke: currentColor; fill: none;
    stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0;
}
.alert-success { background: var(--green-pale); color: var(--green); border: 1px solid #b7e4c7; }
.alert-error   { background: var(--red-pale);   color: var(--red);   border: 1px solid #f5b8bc; }

/* Section */
.content-section { display: none; animation: fadeUp 0.35s ease forwards; }
.content-section.active { display: block; }
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.section-header { margin-bottom: 36px; }
.section-title {
    font-family: 'Playfair Display', serif;
    font-size: 2rem; color: var(--green); line-height: 1.2;
}
.section-sub { color: var(--text-muted); font-size: 0.9rem; margin-top: 6px; }

/* Info cards */
.info-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px,1fr));
    gap: 16px; margin-bottom: 36px;
}
.info-card {
    background: var(--white); border-radius: var(--radius);
    padding: 22px 20px; display: flex; align-items: center; gap: 16px;
    box-shadow: var(--shadow-sm); border: 1px solid var(--border);
    transition: transform var(--trans), box-shadow var(--trans);
}
.info-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
.info-card-icon { font-size: 1.6rem; line-height: 1; }
.info-card-body { display: flex; flex-direction: column; gap: 4px; min-width: 0; }
.info-label {
    font-size: 0.75rem; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: 0.8px; font-weight: 600;
}
.info-value {
    font-size: 0.97rem; font-weight: 600; color: var(--text);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.badge-active {
    display: inline-flex; align-items: center; gap: 6px;
    color: var(--green) !important; font-size: 0.88rem !important;
}
.badge-active::before {
    content: ''; width: 8px; height: 8px; border-radius: 50%;
    background: var(--green-light);
    box-shadow: 0 0 0 2px var(--green-pale); flex-shrink: 0;
}

/* Quick actions */
.quick-actions { display: flex; flex-wrap: wrap; gap: 12px; }
.quick-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 22px; border-radius: var(--radius-sm);
    background: var(--white); border: 1.5px solid var(--border);
    color: var(--green); font-family: 'DM Sans', sans-serif;
    font-size: 0.92rem; font-weight: 600; text-decoration: none; cursor: pointer;
    transition: background var(--trans), border-color var(--trans),
                transform var(--trans), box-shadow var(--trans);
    box-shadow: var(--shadow-sm);
}
.quick-btn svg {
    width: 16px; height: 16px; stroke: currentColor; fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}
.quick-btn:hover {
    background: var(--green-pale); border-color: var(--green-light);
    transform: translateY(-2px); box-shadow: var(--shadow-md);
}

/* Form card */
.form-card {
    background: var(--white); border-radius: var(--radius);
    padding: 36px 40px; max-width: 520px;
    box-shadow: var(--shadow-md); border: 1px solid var(--border);
}
.form-group { margin-bottom: 22px; }
.form-group label {
    display: block; font-size: 0.85rem; font-weight: 600; color: var(--text);
    margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.6px;
}
.input-wrap {
    display: flex; align-items: center;
    border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    height: 50px; padding: 0 14px; gap: 10px;
    transition: border-color var(--trans), box-shadow var(--trans);
    background: var(--cream);
}
.input-wrap:focus-within {
    border-color: var(--green-light);
    box-shadow: 0 0 0 3px rgba(64,145,108,0.12);
    background: var(--white);
}
.input-wrap > svg {
    width: 18px; height: 18px; stroke: var(--text-muted); fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0;
}
.input-wrap input {
    flex: 1; border: none; background: transparent;
    font-family: 'DM Sans', sans-serif; font-size: 0.95rem;
    color: var(--text); outline: none;
}
.input-wrap input::placeholder { color: var(--text-muted); }
.toggle-pw {
    background: none; border: none; cursor: pointer; padding: 4px;
    color: var(--text-muted); display: flex; align-items: center;
    transition: color var(--trans);
}
.toggle-pw:hover { color: var(--green); }
.toggle-pw svg {
    width: 17px; height: 17px; stroke: currentColor; fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}

/* Strength bar */
.strength-wrap { display: flex; align-items: center; gap: 10px; margin-top: 8px; }
.strength-bar {
    flex: 1; height: 4px; background: var(--border);
    border-radius: 99px; overflow: hidden;
}
.strength-fill {
    height: 100%; width: 0; border-radius: 99px;
    transition: width 0.4s ease, background 0.4s ease;
}
.strength-label {
    font-size: 0.75rem; font-weight: 600; color: var(--text-muted);
    min-width: 72px; text-align: right;
}

/* Submit btn */
.submit-btn {
    width: 100%; height: 52px; border-radius: var(--radius-sm);
    background: var(--green); color: var(--white); border: none;
    font-family: 'DM Sans', sans-serif; font-size: 0.97rem; font-weight: 600;
    cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;
    margin-top: 8px;
    transition: background var(--trans), transform var(--trans), box-shadow var(--trans);
    box-shadow: 0 4px 16px rgba(45,106,79,0.25);
}
.submit-btn svg {
    width: 18px; height: 18px; stroke: currentColor; fill: none;
    stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;
}
.submit-btn:hover {
    background: var(--green-light); transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(45,106,79,0.30);
}
.submit-btn:active { transform: translateY(0); }

.history-panel {
    background: var(--white); border-radius: var(--radius);
    border: 1px solid var(--border); box-shadow: var(--shadow-md);
    overflow: hidden; max-width: 980px;
}
.history-row {
    display: grid; grid-template-columns: minmax(0, 1fr) auto;
    gap: 18px; padding: 20px 24px; border-bottom: 1px solid var(--border);
}
.history-row:last-child { border-bottom: none; }
.history-title {
    display: flex; flex-wrap: wrap; align-items: center; gap: 10px;
    font-weight: 700; color: var(--text); margin-bottom: 8px;
}
.history-code { color: var(--green); font-size: 0.9rem; }
.history-meta {
    display: flex; flex-wrap: wrap; gap: 10px 18px;
    color: var(--text-muted); font-size: 0.88rem; line-height: 1.6;
}
.history-note {
    margin-top: 10px; color: var(--text); font-size: 0.9rem;
    background: var(--cream); border: 1px solid var(--border);
    border-radius: var(--radius-sm); padding: 10px 12px;
}
.status-message {
    margin-top: 12px; padding: 12px 14px; border-radius: var(--radius-sm);
    font-size: 0.9rem; line-height: 1.5; font-weight: 600;
    border: 1px solid rgba(0,0,0,0.04);
}
.status-pill {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 116px; height: 34px; padding: 0 12px;
    border-radius: 999px; font-size: 0.82rem; font-weight: 700;
    white-space: nowrap;
}
.status-pending { background: #fff4d6; color: #946200; }
.status-waiting { background: #e8f1ff; color: #1f5fbf; }
.status-done { background: var(--green-pale); color: var(--green); }
.status-cancelled { background: var(--red-pale); color: var(--red); }
.empty-state {
    background: var(--white); border: 1px solid var(--border);
    border-radius: var(--radius); box-shadow: var(--shadow-sm);
    padding: 28px; max-width: 620px;
}
.empty-state p { color: var(--text-muted); margin-bottom: 16px; }

/* Responsive */
@media (max-width: 900px) {
    .sidebar { width: 64px; padding: 20px 8px; overflow: hidden; }
    .logo-text, .avatar-info, .avatar-block { display: none; }
    .nav-item, .logout-btn { justify-content: center; padding: 12px; gap: 0; }
    .logo-paw { font-size: 1.5rem; }
    .main-content { margin-left: 64px; padding: 32px 24px 48px; }
}
@media (max-width: 600px) {
    .sidebar { transform: translateX(-100%); }
    .main-content { margin-left: 0; padding: 24px 18px 48px; }
    .form-card { padding: 24px 18px; }
    .section-title { font-size: 1.6rem; }
    .info-cards { grid-template-columns: 1fr; }
    .history-row { grid-template-columns: 1fr; padding: 18px; }
    .status-pill { width: max-content; }
}
</style>
</head>
<body>

<div class="bg-blob blob-1"></div>
<div class="bg-blob blob-2"></div>
<div class="bg-blob blob-3"></div>

<div class="page-wrapper">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <a href="index.php" class="sidebar-logo">
            <span class="logo-paw">🐾</span>
            <span class="logo-text">PetShop</span>
        </a>

        <div class="avatar-block">
            <div class="avatar-ring">
                <div class="avatar"><?= htmlspecialchars($profile->avatar_letter) ?></div>
            </div>
            <div class="avatar-info">
                <p class="avatar-name"><?= htmlspecialchars($profile->username) ?></p>
                <p class="avatar-since">Thanh vien tu <?= $profile->ngay_tao ?></p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="#section-info" class="nav-item active" data-section="info">
                <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Thong tin
            </a>
            <a href="#section-password" class="nav-item" data-section="password">
                <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Doi mat khau
            </a>
            <a href="#section-bookings" class="nav-item" data-section="bookings">
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/></svg>
                Lich dat
            </a>
            <a href="shopcard.php" class="nav-item">
                <svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                Gio hang
            </a>
        </nav>

        <a href="?logout=1" class="logout-btn"
           onclick="return confirm('Ban co chac muon dang xuat?')">
            <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Dang xuat
        </a>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <?php if ($success_msg): ?>
        <div class="alert alert-success">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            <?= htmlspecialchars($success_msg) ?>
        </div>
        <?php elseif ($error_msg): ?>
        <div class="alert alert-error">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?= htmlspecialchars($error_msg) ?>
        </div>
        <?php endif; ?>

        <!-- Section: Thong tin -->
        <section class="content-section active" id="section-info">
            <div class="section-header">
                <h1 class="section-title">Hồ sơ của tôi</h1>
                <p class="section-sub">Xem và quản lý thông tin tài khoản</p>
            </div>

            <div class="info-cards">
                <div class="info-card">
                    <div class="info-card-icon">👤</div>
                    <div class="info-card-body">
                        <span class="info-label">Tên đăng nhập</span>
                        <span class="info-value"><?= htmlspecialchars($profile->username) ?></span>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-card-icon">🗓️</div>
                    <div class="info-card-body">
                        <span class="info-label">Ngày tạo tài khoản</span>
                        <span class="info-value"><?= $profile->ngay_tao ?></span>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-card-icon">🛡️</div>
                    <div class="info-card-body">
                        <span class="info-label">Trạng thái</span>
                        <span class="info-value badge-active">Đang hoạt động</span>
                    </div>
                </div>
            </div>

            <div class="quick-actions">
                <button class="quick-btn" onclick="switchSection('password')">
                    <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Đổi mật khẩu
                </button>
                <button class="quick-btn" onclick="switchSection('bookings')">
                    <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Lich dat
                </button>
                <a href="shopcard.php" class="quick-btn">
                    <svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    Xem giỏ hàng
                </a>
                <a href="index.php" class="quick-btn">
                    <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Trang chủ
                </a>
            </div>
        </section>

        <!-- Section: Lich dat -->
        <section class="content-section" id="section-bookings">
            <div class="section-header">
                <h1 class="section-title">Lịch đặt của tôi</h1>
                <p class="section-sub">Theo dõi lịch dịch vụ đã đặt bằng tài khoản này</p>
            </div>

            <?php if (empty($booking_history)): ?>
                <div class="empty-state">
                    <p>Bạn chưa có lịch đặt dịch vụ nào.</p>
                    <a href="booking.php" class="quick-btn">
                        <svg viewBox="0 0 24 24"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
                        Đặt lịch mới
                    </a>
                </div>
            <?php else: ?>
                <div class="history-panel">
                    <?php foreach ($booking_history as $booking): ?>
                        <article class="history-row">
                            <div>
                                <div class="history-title">
                                    <span class="history-code">#<?= (int)$booking->id ?></span>
                                    <span><?= htmlspecialchars($booking->ten_dich_vu) ?></span>
                                </div>
                                <div class="history-meta">
                                    <span>Thú cưng: <?= htmlspecialchars($booking->ten_loai) ?></span>
                                    <span>Ngày hẹn: <?= htmlspecialchars($booking->ngayHenFormatted()) ?></span>
                                    <span>Giờ hẹn: <?= htmlspecialchars($booking->gioHenFormatted()) ?></span>
                                    <span>Ngày đặt: <?= htmlspecialchars($booking->ngayTaoFormatted()) ?></span>
                                    <span>SĐT: <?= htmlspecialchars($booking->sdt) ?></span>
                                </div>
                                <?php if ($booking->ghi_chu !== ''): ?>
                                    <div class="history-note">
                                        <?= nl2br(htmlspecialchars($booking->ghi_chu)) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="status-message <?= htmlspecialchars($booking->statusClass()) ?>">
                                    <?= htmlspecialchars($booking->statusMessage()) ?>
                                </div>
                            </div>
                            <span class="status-pill <?= htmlspecialchars($booking->statusClass()) ?>">
                                <?= htmlspecialchars($booking->customerStatusLabel()) ?>
                            </span>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Section: Doi mat khau -->
        <section class="content-section" id="section-password">
            <div class="section-header">
                <h1 class="section-title">Đổi mật khẩu</h1>
                <p class="section-sub">Cập nhật mật khẩu để bảo mật tài khoản</p>
            </div>

            <div class="form-card">
                <form method="POST" action="profile.php#section-password">
                    <input type="hidden" name="action" value="change_password">

                    <div class="form-group">
                        <label>Mật khẩu hiện tại</label>
                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input type="password" name="current_password" placeholder="Nhập mật khẩu hiện tại" required>
                            <button type="button" class="toggle-pw" tabindex="-1">
                                <svg class="eye-open" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" viewBox="0 0 24 24" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input type="password" name="new_password" placeholder="Ít nhất 6 ký tự" required id="new-pw">
                            <button type="button" class="toggle-pw" tabindex="-1">
                                <svg class="eye-open" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" viewBox="0 0 24 24" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        <div class="strength-wrap">
                            <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
                            <span class="strength-label" id="strength-label"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Xác nhận mật khẩu mới</label>
                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required>
                            <button type="button" class="toggle-pw" tabindex="-1">
                                <svg class="eye-open" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" viewBox="0 0 24 24" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">
                        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        Cập nhật mật khẩu
                    </button>
                </form>
            </div>
        </section>

    </main>
</div>

<script>
function switchSection(name) {
    document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    const sec = document.getElementById('section-' + name);
    if (sec) sec.classList.add('active');
    const nav = document.querySelector('[data-section="' + name + '"]');
    if (nav) nav.classList.add('active');
}
document.querySelectorAll('.nav-item[data-section]').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        switchSection(this.dataset.section);
    });
});

document.querySelectorAll('.toggle-pw').forEach(btn => {
    btn.addEventListener('click', function() {
        const input     = this.closest('.input-wrap').querySelector('input');
        const eyeOpen   = this.querySelector('.eye-open');
        const eyeClosed = this.querySelector('.eye-closed');
        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.style.display   = 'none';
            eyeClosed.style.display = 'block';
        } else {
            input.type = 'password';
            eyeOpen.style.display   = 'block';
            eyeClosed.style.display = 'none';
        }
    });
});

const newPwInput   = document.getElementById('new-pw');
const strengthFill = document.getElementById('strength-fill');
const strengthLbl  = document.getElementById('strength-label');
if (newPwInput) {
    newPwInput.addEventListener('input', function() {
        const val = this.value;
        let score = 0;
        if (val.length >= 6)          score++;
        if (val.length >= 10)         score++;
        if (/[A-Z]/.test(val))        score++;
        if (/[0-9]/.test(val))        score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const levels = [
            { pct:'0%',   color:'transparent', label:'' },
            { pct:'25%',  color:'#ff4d4d',     label:'Rat yeu' },
            { pct:'50%',  color:'#ffa500',     label:'Yeu' },
            { pct:'75%',  color:'#2d79f3',     label:'Trung binh' },
            { pct:'90%',  color:'#00b894',     label:'Manh' },
            { pct:'100%', color:'#00b894',     label:'Rat manh' },
        ];
        const lv = levels[score] || levels[0];
        strengthFill.style.width      = lv.pct;
        strengthFill.style.background = lv.color;
        strengthLbl.textContent       = lv.label;
    });
}

(function() {
    if (window.location.hash === '#section-password') switchSection('password');
    if (window.location.hash === '#section-bookings') switchSection('bookings');
    <?php if ($error_msg || $success_msg): ?>
    switchSection('password');
    <?php endif; ?>
})();

const alertEl = document.querySelector('.alert');
if (alertEl) setTimeout(() => alertEl.style.opacity = '0', 4000);
</script>
</body>
</html>
