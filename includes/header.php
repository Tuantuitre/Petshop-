<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetShop - Chăm sóc thú cưng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<style>
:root {
    --sage:       #5a7a5a;
    --sage-light: #7fa87f;
    --sage-pale:  #edf4ed;
    --cream:      #faf7f2;
    --stone:      #e8e0d5;
    --ink:        #1e1e1e;
    --muted:      #7a7267;
    --white:      #ffffff;
    --trans:      0.22s cubic-bezier(.4,0,.2,1);
}
.petshop-nav {
    position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
    background: rgba(255,255,255,0.96);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--stone);
    font-family: 'DM Sans', sans-serif;
    box-shadow: 0 2px 20px rgba(90,122,90,0.08);
}
.petshop-nav .container {
    display: flex; align-items: center;
    height: 64px;
}
.nav-brand {
    display: flex; align-items: center; gap: 10px;
    text-decoration: none; flex-shrink: 0; margin-right: 32px;
}
.nav-brand img {
    width: 40px; height: 40px;
    object-fit: cover;
    border-radius: 50%;
    background: var(--white);
    border: 1px solid var(--stone);
    padding: 2px;
}
.nav-brand-text {
    font-family: 'Fraunces', serif;
    font-size: 1.3rem; font-weight: 700;
    color: var(--sage); letter-spacing: -0.3px;
    white-space: nowrap;
}
.nav-brand-paw { font-size: 1rem; margin-left: -2px; }
.nav-links {
    display: flex; align-items: center;
    gap: 2px; list-style: none; margin: 0; padding: 0; flex: 1;
}
.nav-links a {
    display: block; padding: 8px 13px;
    color: var(--muted); text-decoration: none;
    font-size: .88rem; font-weight: 500;
    border-radius: 8px;
    transition: background var(--trans), color var(--trans);
    white-space: nowrap;
}
.nav-links a:hover,
.nav-links a.active { background: var(--sage-pale); color: var(--sage); }
.nav-right {
    display: flex; align-items: center; gap: 8px;
    flex-shrink: 0; margin-left: 16px;
}
.nav-cta {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px;
    background: var(--sage); color: var(--white) !important;
    border-radius: 8px; text-decoration: none;
    font-size: .85rem; font-weight: 600;
    transition: background var(--trans), transform var(--trans);
    white-space: nowrap;
}
.nav-cta:hover { background: var(--sage-light); transform: translateY(-1px); }

/* NÚT QUẢN LÝ - chỉ admin thấy */
.nav-admin-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px;
    background: #2d4a2d; color: #fff !important;
    border-radius: 8px; text-decoration: none;
    font-size: .82rem; font-weight: 600;
    transition: background var(--trans), transform var(--trans);
    white-space: nowrap;
    border: none; cursor: pointer;
}
.nav-admin-btn:hover {
    background: #3d5c3d;
    transform: translateY(-1px);
    color: #fff !important;
}

.nav-icon-btn {
    width: 38px; height: 38px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: var(--muted); text-decoration: none;
    transition: background var(--trans), color var(--trans);
    position: relative; font-size: .95rem;
}
.nav-icon-btn:hover { background: var(--sage-pale); color: var(--sage); }

/* Logout btn */
.nav-logout-btn {
    width: 38px; height: 38px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: var(--muted); text-decoration: none;
    transition: background var(--trans), color var(--trans);
    font-size: .95rem; cursor: pointer;
    border: none; background: transparent;
}
.nav-logout-btn:hover { background: #fff0f0; color: #e74c3c; }

.nav-toggle {
    display: none;
    width: 38px; height: 38px; border-radius: 8px;
    border: 1.5px solid var(--stone);
    background: transparent; cursor: pointer;
    flex-direction: column; align-items: center; justify-content: center;
    gap: 5px; margin-left: auto;
}
.nav-toggle span {
    display: block; width: 18px; height: 2px;
    background: var(--ink); border-radius: 99px;
    transition: transform var(--trans), opacity var(--trans);
}
body { padding-top: 64px; }

@media (max-width: 991px) {
    .nav-toggle { display: flex; }
    .nav-collapse {
        display: none;
        position: absolute; top: 64px; left: 0; right: 0;
        background: var(--white);
        border-bottom: 1px solid var(--stone);
        padding: 12px 16px 20px;
        box-shadow: 0 8px 24px rgba(90,122,90,0.12);
        flex-direction: column; gap: 0;
    }
    .nav-collapse.open { display: flex; }
    .nav-links { flex-direction: column; gap: 2px; width: 100%; }
    .nav-links a { padding: 10px 14px; }
    .nav-right {
        margin-left: 0; padding-top: 12px;
        border-top: 1px solid var(--stone);
        width: 100%; flex-wrap: wrap;
    }
    .nav-cta { flex: 1; justify-content: center; }
    .nav-admin-btn { flex: 1; justify-content: center; }
}
</style>

<header class="petshop-nav">
    <div class="container">

        <a class="nav-brand" href="index.php">
            <img src="assets/images/logo.png" alt="PetShop Logo"
                 onerror="this.style.display='none'">
            <span class="nav-brand-text">PetShop</span>
            <span class="nav-brand-paw">🐾</span>
        </a>

        <button class="nav-toggle" id="navToggle" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>

        <div class="nav-collapse" id="navCollapse" style="display:flex; flex:1; align-items:center;">

            <ul class="nav-links">
                <?php
                $current = basename($_SERVER['PHP_SELF']);
                $links = [
                    'index.php'    => 'Trang chủ',
                    'products.php' => 'Sản phẩm',
                    'services.php' => 'Dịch vụ',
                    'booking.php'  => 'Đặt lịch',
                    'blog.php'     => 'Tin tức',
                    'contact.php'  => 'Liên hệ',
                ];
                foreach ($links as $href => $label):
                ?>
                <li><a href="<?= $href ?>"
                       class="<?= $current === $href ? 'active' : '' ?>">
                    <?= $label ?>
                </a></li>
                <?php endforeach; ?>
            </ul>

            <div class="nav-right">

                <!-- Nút Đặt lịch -->
                <a href="booking.php" class="nav-cta">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Đặt lịch ngay
                </a>

                <?php if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin'): ?>
                <!-- NÚT QUẢN LÝ - chỉ admin thấy -->
                <a href="admin.php" class="nav-admin-btn" title="Trang quản trị">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                        <path d="M2 12l10 5 10-5"/>
                    </svg>
                    Quản lý
                </a>
                <?php endif; ?>

                <!-- Giỏ hàng -->
                <a href="<?= isset($_SESSION['user_id']) ? 'shopcard.php' : 'dangnhap.php' ?>"
                   class="nav-icon-btn" title="Giỏ hàng">
                    <i class="fas fa-shopping-cart"></i>
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Đã đăng nhập: icon user + logout -->
                <a href="<?= $_SESSION['username'] === 'admin' ? 'admin.php' : 'profile.php' ?>"
                   class="nav-icon-btn" title="Tài khoản: <?= htmlspecialchars($_SESSION['username']) ?>">
                    <i class="fas fa-user"></i>
                </a>
                <a href="dangnhap.php?logout=1" class="nav-logout-btn" title="Đăng xuất">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
                <?php else: ?>
                <!-- Chưa đăng nhập -->
                <a href="dangnhap.php" class="nav-icon-btn" title="Đăng nhập">
                    <i class="fas fa-user"></i>
                </a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</header>

<script>
const toggle = document.getElementById('navToggle');
const collapse = document.getElementById('navCollapse');
if (toggle && collapse) {
    toggle.addEventListener('click', () => collapse.classList.toggle('open'));
}
window.addEventListener('resize', () => {
    if (window.innerWidth > 991) {
        collapse.style.display = 'flex';
        collapse.classList.remove('open');
    }
});
</script>
