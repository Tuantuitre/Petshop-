<?php
// ============================================================
// Contact_ui.php
// Tầng UI — Chỉ render HTML/CSS/JS, không có logic
// Flow: [UI] ← Controller ← Service ← DAO ← DB
// ============================================================
include __DIR__ . '/../includes/header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root {
    --sage:       #5a7a5a;
    --sage-light: #7fa87f;
    --sage-pale:  #edf4ed;
    --clay:       #c17f4f;
    --cream:      #faf7f2;
    --stone:      #e8e0d5;
    --ink:        #1e1e1e;
    --muted:      #7a7267;
    --white:      #ffffff;
    --r:          16px;
    --r-sm:       10px;
    --shadow:     0 4px 20px rgba(90,122,90,0.10);
    --shadow-md:  0 8px 32px rgba(90,122,90,0.14);
    --trans:      0.25s cubic-bezier(.4,0,.2,1);
}
*, *::before, *::after { box-sizing: border-box; }

.contact-page {
    background: var(--cream);
    min-height: 100vh;
    font-family: 'DM Sans', sans-serif;
}

/* ── HERO ── */
.contact-hero {
    background: linear-gradient(135deg, #3d5c3d 0%, #5a7a5a 50%, #7a9e6a 100%);
    padding: 64px 24px 56px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.contact-hero::before {
    content: '💬';
    position: absolute; font-size: 200px; opacity: 0.06;
    top: -30px; right: -20px; pointer-events: none;
}
.contact-hero::after {
    content: '🐾';
    position: absolute; font-size: 160px; opacity: 0.05;
    bottom: -20px; left: 0; pointer-events: none;
}
.contact-hero h1 {
    font-family: 'Fraunces', serif;
    font-size: clamp(2rem, 5vw, 3.2rem);
    color: var(--white);
    letter-spacing: -0.5px;
    line-height: 1.2;
    margin: 0 0 10px;
}
.contact-hero p {
    color: rgba(255,255,255,.72);
    font-size: 1rem;
    font-weight: 300;
    margin: 0 auto;
    max-width: 480px;
}
.hero-badges {
    display: flex; justify-content: center;
    gap: 10px; flex-wrap: wrap; margin-top: 20px;
}
.hero-badge {
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    color: var(--white); padding: 5px 18px;
    border-radius: 99px; font-size: .82rem;
    font-weight: 500; backdrop-filter: blur(6px);
}

/* ── LAYOUT ── */
.contact-content {
    max-width: 1100px;
    margin: 0 auto;
    padding: 48px 24px 72px;
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 28px;
    align-items: start;
}
@media (max-width: 900px) {
    .contact-content { grid-template-columns: 1fr; }
    .contact-sidebar { order: -1; }
}

/* ── MAIN CARD ── */
.contact-card {
    background: var(--white);
    border-radius: var(--r);
    border: 1px solid var(--stone);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}
.contact-card-header {
    padding: 18px 28px;
    border-bottom: 1px solid var(--stone);
    background: var(--cream);
    display: flex; align-items: center; gap: 10px;
}
.contact-card-header h2 {
    font-family: 'Fraunces', serif;
    font-size: 1.15rem;
    color: var(--ink);
    margin: 0;
}

/* ── MAP + FORM GRID ── */
.contact-card-body {
    padding: 28px 32px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 28px;
}
@media (max-width: 700px) {
    .contact-card-body { grid-template-columns: 1fr; padding: 20px 18px; }
}

/* ── MAP SIDE ── */
.map-wrap iframe {
    width: 100%;
    height: 340px;
    border: 0;
    border-radius: var(--r-sm);
    display: block;
}
.map-address {
    margin-top: 14px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 14px 16px;
    background: var(--sage-pale);
    border-radius: var(--r-sm);
    border: 1px solid #c8dfc8;
    font-size: .85rem;
    color: var(--muted);
    line-height: 1.55;
}
.map-address .pin { font-size: 1.2rem; flex-shrink: 0; margin-top: 1px; }
.map-address strong { display: block; color: var(--ink); margin-bottom: 2px; font-size: .88rem; }

/* ── FORM SIDE ── */
.fg { margin-bottom: 16px; }
.fg label {
    display: block;
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .7px;
    color: var(--ink);
    margin-bottom: 7px;
}
.fg label .req { color: #e63946; margin-left: 2px; }
.fg input,
.fg textarea {
    width: 100%;
    padding: 11px 14px;
    border: 1.5px solid var(--stone);
    border-radius: var(--r-sm);
    background: var(--cream);
    font-family: 'DM Sans', sans-serif;
    font-size: .9rem;
    color: var(--ink);
    transition: border-color var(--trans), box-shadow var(--trans), background var(--trans);
    outline: none;
    box-sizing: border-box;
}
.fg input:focus,
.fg textarea:focus {
    border-color: var(--sage-light);
    box-shadow: 0 0 0 3px rgba(90,122,90,0.12);
    background: var(--white);
}
.fg input::placeholder,
.fg textarea::placeholder { color: var(--muted); }
.fg textarea { resize: vertical; min-height: 110px; line-height: 1.65; }

.contact-submit-btn {
    width: 100%;
    padding: 13px;
    background: var(--sage);
    color: var(--white);
    border: none;
    border-radius: var(--r-sm);
    font-family: 'DM Sans', sans-serif;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: background var(--trans), transform var(--trans), box-shadow var(--trans);
    box-shadow: 0 4px 16px rgba(45,106,79,0.22);
}
.contact-submit-btn:hover {
    background: var(--sage-light);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(45,106,79,0.28);
}
.contact-submit-btn:disabled {
    opacity: .65; cursor: not-allowed; transform: none;
}

/* ── SIDEBAR ── */
.contact-sidebar { display: flex; flex-direction: column; gap: 20px; }
.side-card {
    background: var(--white);
    border-radius: var(--r);
    border: 1px solid var(--stone);
    box-shadow: var(--shadow);
    overflow: hidden;
}
.side-card-header {
    padding: 14px 18px;
    border-bottom: 1px solid var(--stone);
    background: var(--cream);
    font-family: 'Fraunces', serif;
    font-size: .95rem;
    color: var(--ink);
    display: flex; align-items: center; gap: 8px;
}
.side-card-body { padding: 16px 18px; }

.info-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; }
.info-list li {
    display: flex; align-items: flex-start; gap: 12px;
    font-size: .85rem; color: var(--muted); line-height: 1.55;
}
.info-list .ico {
    width: 34px; height: 34px; border-radius: 9px;
    background: var(--sage-pale);
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}
.info-list strong { display: block; color: var(--ink); font-size: .87rem; margin-bottom: 1px; }

.social-row {
    display: flex; gap: 10px; flex-wrap: wrap; margin-top: 4px;
}
.social-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px;
    border: 1.5px solid var(--stone);
    border-radius: 8px;
    font-size: .8rem; font-weight: 600;
    color: var(--muted); text-decoration: none;
    transition: all var(--trans);
}
.social-btn:hover {
    border-color: var(--sage);
    color: var(--sage);
    background: var(--sage-pale);
}

/* ── MODAL ── */
#successModal {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
.modal-inner {
    background: var(--white);
    border-radius: 20px;
    padding: 48px 44px;
    max-width: 460px;
    width: 90%;
    text-align: center;
    box-shadow: 0 16px 50px rgba(0,0,0,0.2);
    animation: popIn .3s ease;
}
@keyframes popIn {
    from { transform: scale(.88); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}
.modal-icon {
    width: 72px; height: 72px;
    background: var(--sage);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 18px;
    font-size: 2rem; color: var(--white);
}
.modal-inner h4 {
    font-family: 'Fraunces', serif;
    font-size: 1.3rem; font-weight: 700;
    color: var(--ink); margin-bottom: 10px;
}
.modal-inner p {
    font-size: .9rem; color: var(--muted);
    line-height: 1.65; margin-bottom: 24px;
}
.modal-close-btn {
    background: var(--sage); color: var(--white);
    border: none; padding: 11px 32px;
    border-radius: 9px; font-family: 'DM Sans', sans-serif;
    font-size: .95rem; font-weight: 600;
    cursor: pointer; transition: background var(--trans);
}
.modal-close-btn:hover { background: var(--sage-light); }

/* ── FOOTER STRIP ── */
.scallop-divider { line-height: 0; margin-top: 20px; }
.scallop-divider svg { display: block; width: 100%; }
.contact-footer-strip {
    background: #ffa726;
    text-align: center;
    padding: 16px 20px;
    font-family: 'Fraunces', serif;
    font-size: 1rem;
    color: var(--white);
    letter-spacing: .3px;
    margin: 0;
}
.contact-footer-strip p { margin: 0; }
</style>

<div class="contact-page">

    <!-- HERO -->
    <div class="contact-hero">
        <h1>💬 Liên Hệ Với Chúng Tôi</h1>
        <p>Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn bất cứ lúc nào!</p>
        <div class="hero-badges">
            <span class="hero-badge">⚡ Phản hồi nhanh</span>
            <span class="hero-badge">📞 Hỗ trợ 24/7</span>
            <span class="hero-badge">🐾 Tư vấn miễn phí</span>
        </div>
    </div>

    <div class="contact-content">

        <!-- MAIN CARD -->
        <div class="contact-card">
            <div class="contact-card-header">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="var(--sage)" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <h2>Gửi tin nhắn cho chúng tôi</h2>
            </div>
            <div class="contact-card-body">

                <!-- MAP -->
                <div class="map-wrap">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.868147689468!2d105.832944515003!3d21.037127985993!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab0a0a0a0a0a%3A0xa0a0a0a0a0a0a0a0!2s266%20%C4%90%C3%B4i%20C%E1%BA%A7n%2C%20Ba%20%C4%90%C3%ACnh%2C%20H%C3%A0%20N%E1%BB%99i!5e0!3m2!1svi!2svn!4v1708940000000!5m2!1svi!2svn"
                        allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                    <div class="map-address">
                        <span class="pin">📍</span>
                        <div>
                            <strong>PetShop – Chi nhánh Hà Nội</strong>
                            Tầng 6 tòa nhà Ladeco, 266 Đôi Cấn, phường Liễu Giai, Ba Đình, Hà Nội
                        </div>
                    </div>
                </div>

                <!-- FORM: POST đến chính contact.php (controller xử lý) -->
                <div>
                    <form id="contact-form" method="POST" action="contact.php">
                        <div class="fg">
                            <label for="name">Họ và tên <span class="req">*</span></label>
                            <input type="text" id="name" name="name"
                                   placeholder="Nguyễn Văn A" required>
                        </div>
                        <div class="fg">
                            <label for="phone">Số điện thoại <span class="req">*</span></label>
                            <input type="tel" id="phone" name="phone"
                                   placeholder="0123 456 789"
                                   required pattern="[0-9]{10,11}">
                        </div>
                        <div class="fg">
                            <label for="email">Email <span class="req">*</span></label>
                            <input type="email" id="email" name="email"
                                   placeholder="email@example.com" required>
                        </div>
                        <div class="fg">
                            <label for="message">Nội dung <span class="req">*</span></label>
                            <textarea id="message" name="message" rows="5"
                                      placeholder="Nội dung liên hệ..." required></textarea>
                        </div>
                        <button type="submit" id="submitBtn" class="contact-submit-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2.5"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"/>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                            Gửi tin nhắn
                        </button>
                    </form>
                </div>

            </div>
        </div>

        <!-- SIDEBAR -->
        <aside class="contact-sidebar">

            <div class="side-card">
                <div class="side-card-header">📋 Thông tin liên hệ</div>
                <div class="side-card-body">
                    <ul class="info-list">
                        <li>
                            <div class="ico">📍</div>
                            <div>
                                <strong>Địa chỉ</strong>
                                266 Đôi Cấn, Liễu Giai, Ba Đình, Hà Nội
                            </div>
                        </li>
                        <li>
                            <div class="ico">📞</div>
                            <div>
                                <strong>Điện thoại</strong>
                                0123 456 789
                            </div>
                        </li>
                        <li>
                            <div class="ico">✉️</div>
                            <div>
                                <strong>Email</strong>
                                hello@petshop.vn
                            </div>
                        </li>
                        <li>
                            <div class="ico">🕐</div>
                            <div>
                                <strong>Giờ làm việc</strong>
                                T2–T6: 8:00 – 18:00<br>T7–CN: 8:00 – 16:00
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="side-card">
                <div class="side-card-header">🌐 Mạng xã hội</div>
                <div class="side-card-body">
                    <p style="font-size:.83rem;color:var(--muted);margin-bottom:14px">
                        Theo dõi chúng tôi để nhận thông tin mới nhất về sản phẩm và dịch vụ!
                    </p>
                    <div class="social-row">
                        <a href="#" class="social-btn">📘 Facebook</a>
                        <a href="#" class="social-btn">📸 Instagram</a>
                        <a href="#" class="social-btn">🎵 TikTok</a>
                        <a href="#" class="social-btn">▶️ YouTube</a>
                    </div>
                </div>
            </div>

            <div class="side-card">
                <div class="side-card-header">❓ Câu hỏi thường gặp</div>
                <div class="side-card-body">
                    <ul class="info-list">
                        <li>
                            <div class="ico">💬</div>
                            <div>
                                <strong>Phản hồi trong bao lâu?</strong>
                                Chúng tôi phản hồi trong vòng 1–2 giờ trong giờ làm việc.
                            </div>
                        </li>
                        <li>
                            <div class="ico">🐾</div>
                            <div>
                                <strong>Có tư vấn miễn phí không?</strong>
                                Có! Tư vấn chăm sóc thú cưng hoàn toàn miễn phí.
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

        </aside>
    </div>
</div>

<div class="contact-footer-strip">
    <p>🐾 PetShop – Yêu thương thú cưng như gia đình 🐾</p>
</div>

<!-- SUCCESS MODAL -->
<div id="successModal">
    <div class="modal-inner">
        <div class="modal-icon" id="modalIcon">✓</div>
        <h4 id="modalTitle">Gửi thành công! 🎉</h4>
        <p id="modalMessage">Vui lòng chờ phản hồi trong vài ngày tới.</p>
        <button onclick="closeModal()" class="modal-close-btn">OK, đã hiểu</button>
    </div>
</div>

<script>
// ── AJAX submit form ──────────────────────────────────────────
const form      = document.getElementById('contact-form');
const submitBtn = document.getElementById('submitBtn');

form.addEventListener('submit', function(e) {
    e.preventDefault();
    submitBtn.disabled    = true;
    submitBtn.textContent = '⏳ Đang gửi...';

    fetch('contact.php', {
        method: 'POST',
        body:   new FormData(form),
    })
    .then(res => res.json())
    .then(data => {
        const icon  = document.getElementById('modalIcon');
        const title = document.getElementById('modalTitle');
        const msg   = document.getElementById('modalMessage');

        if (data.success) {
            icon.textContent  = '✓';
            icon.style.background = 'var(--sage)';
            title.textContent = 'Gửi thành công! 🎉';
            msg.textContent   = data.msg || 'Chúng tôi sẽ phản hồi sớm nhất có thể.';
            form.reset();
        } else {
            icon.textContent  = '✕';
            icon.style.background = '#e74c3c';
            title.textContent = 'Có lỗi xảy ra!';
            msg.textContent   = data.msg || 'Vui lòng thử lại.';
        }

        document.getElementById('successModal').style.display = 'flex';
    })
    .catch(() => {
        alert('Lỗi kết nối! Vui lòng thử lại.');
    })
    .finally(() => {
        submitBtn.disabled    = false;
        submitBtn.innerHTML   = `
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"/>
                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
            </svg>
            Gửi tin nhắn`;
    });
});

function closeModal() {
    document.getElementById('successModal').style.display = 'none';
}
// Đóng modal khi click ngoài
document.getElementById('successModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
