<?php
/**
 * Footer - PetShop
 */
?>
<style>
.petshop-footer {
    background: linear-gradient(160deg, #2d4a2d 0%, #3d5c3d 60%, #4a6e4a 100%);
    font-family: 'DM Sans', sans-serif;
    color: rgba(255,255,255,0.75);
    padding: 56px 0 0;
    margin-top: 0;
}

/* ── BRAND COL ── */
.footer-brand { margin-bottom: 16px; }
.footer-logo {
    display: flex; align-items: center; gap: 10px;
    text-decoration: none; margin-bottom: 14px;
}
.footer-logo img {
    width: 42px; height: 42px;
    object-fit: cover;
    border-radius: 50%;
    background: #ffffff;
    padding: 2px;
}
.footer-logo-text {
    font-family: 'Fraunces', serif;
    font-size: 1.3rem; font-weight: 700;
    color: #ffffff; letter-spacing: -0.3px;
    white-space: nowrap;
}
.footer-tagline {
    font-size: .85rem; line-height: 1.65;
    color: rgba(255,255,255,.62); margin-bottom: 20px;
}

/* Social icons */
.footer-socials { display: flex; gap: 10px; }
.social-btn {
    width: 36px; height: 36px; border-radius: 8px;
    border: 1px solid rgba(255,255,255,.20);
    background: rgba(255,255,255,.08);
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,.75); text-decoration: none;
    font-size: .85rem;
    transition: background 0.22s ease, color 0.22s ease, border-color 0.22s ease;
}
.social-btn:hover { background: rgba(255,255,255,.20); color: #fff; border-color: rgba(255,255,255,.4); }

/* ── COLUMNS ── */
.footer-col-title {
    font-family: 'Fraunces', serif;
    font-size: .95rem; color: #ffffff;
    margin-bottom: 16px; font-weight: 700;
    display: flex; align-items: center; gap: 8px;
}
.footer-col-title::after {
    content: ''; flex: 1; height: 1px;
    background: rgba(255,255,255,.15);
    border-radius: 99px;
}

/* Links */
.footer-nav { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
.footer-nav a {
    color: rgba(255,255,255,.65); text-decoration: none;
    font-size: .87rem; display: flex; align-items: center; gap: 8px;
    transition: color 0.2s ease, padding-left 0.2s ease;
}
.footer-nav a::before {
    content: '›'; font-size: 1rem;
    color: rgba(255,255,255,.35);
    transition: color 0.2s ease;
}
.footer-nav a:hover { color: #ffffff; padding-left: 4px; }
.footer-nav a:hover::before { color: rgba(255,255,255,.8); }

/* Service list (no links) */
.footer-service-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
.footer-service-list li {
    font-size: .87rem; color: rgba(255,255,255,.65);
    display: flex; align-items: center; gap: 8px;
}
.footer-service-list li::before { content: '🐾'; font-size: .65rem; }

/* Contact */
.footer-contact-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; }
.footer-contact-list li {
    font-size: .85rem; color: rgba(255,255,255,.65);
    display: flex; align-items: flex-start; gap: 10px; line-height: 1.5;
}
.footer-contact-list .contact-icon {
    width: 28px; height: 28px; border-radius: 7px;
    background: rgba(255,255,255,.10);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: .75rem; color: rgba(255,255,255,.8);
    margin-top: 1px;
}

/* ── DIVIDER & BOTTOM ── */
.footer-divider {
    border: none; border-top: 1px solid rgba(255,255,255,.12);
    margin: 40px 0 0;
}
.footer-bottom {
    padding: 18px 0;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 10px;
    font-size: .8rem; color: rgba(255,255,255,.45);
}
.footer-bottom a { color: rgba(255,255,255,.55); text-decoration: none; }
.footer-bottom a:hover { color: rgba(255,255,255,.85); }
.footer-heart { color: #ff8a80; }
</style>

<footer class="petshop-footer">
    <div class="container">
        <div class="row gy-5">

            <!-- Col 1: Brand -->
            <div class="col-lg-3 col-md-6">
                <a href="index.php" class="footer-logo">
                    <img src="assets/images/logo.png" alt="PetShop"
                         onerror="this.style.display='none'">
                    <span class="footer-logo-text">PetShop 🐾</span>
                </a>
                <p class="footer-tagline">
                    Chăm sóc thú cưng yêu thương như gia đình.<br>
                    Cung cấp sản phẩm và dịch vụ chất lượng cao.
                </p>
                <div class="footer-socials">
                    <a href="https://www.facebook.com" target="_blank" rel="noopener" class="social-btn" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com" target="_blank" rel="noopener" class="social-btn" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.youtube.com" target="_blank" rel="noopener" class="social-btn" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="https://zalo.me" target="_blank" rel="noopener" class="social-btn" title="Zalo">
                        <i class="fas fa-comment-dots"></i>
                    </a>
                </div>
            </div>

            <!-- Col 2: Quick links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="footer-col-title">Liên kết</h6>
                <ul class="footer-nav">
                    <li><a href="index.php">Trang chủ</a></li>
                    <li><a href="products.php">Sản phẩm</a></li>
                    <li><a href="services.php">Dịch vụ</a></li>
                    <li><a href="booking.php">Đặt lịch</a></li>
                    <li><a href="blog.php">Tin tức</a></li>
                    <li><a href="contact.php">Liên hệ</a></li>
                </ul>
            </div>

            <!-- Col 3: Services -->
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-col-title">Dịch vụ</h6>
                <ul class="footer-service-list">
                    <li>Tắm gội thú cưng</li>
                    <li>Cắt tỉa lông</li>
                    <li>Khám sức khỏe</li>
                    <li>Tiêm phòng</li>
                    <li>Massage thư giãn</li>
                    <li>Spa cao cấp</li>
                </ul>
            </div>

            <!-- Col 4: Contact -->
            <div class="col-lg-4 col-md-6">
                <h6 class="footer-col-title">Liên hệ</h6>
                <ul class="footer-contact-list">
                    <li>
                        <span class="contact-icon"><i class="fas fa-map-marker-alt"></i></span>
                        123 Đường vào tim em, Quận cafe, Đông Á
                    </li>
                    <li>
                        <span class="contact-icon"><i class="fas fa-phone"></i></span>
                        086 522 460 5
                    </li>
                    <li>
                        <span class="contact-icon"><i class="fas fa-envelope"></i></span>
                        gachip443h@gmail.com
                    </li>
                    <li>
                        <span class="contact-icon"><i class="fas fa-clock"></i></span>
                        T2–T6: 8:00–18:00 · T7–CN: 8:00–16:00
                    </li>
                </ul>
            </div>

        </div>

        <hr class="footer-divider">

        <div class="footer-bottom">
            <span>© 2026 PetShop. Designed with <span class="footer-heart">❤️</span></span>
            <span>
                <a href="#">Chính sách bảo mật</a> ·
                <a href="#">Điều khoản sử dụng</a>
            </span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js?v=2"></script>
</body>
</html>
