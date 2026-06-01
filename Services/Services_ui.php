<?php

include __DIR__ . '/../includes/header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root {
    --sage:         #5a7a5a;
    --sage-light:   #7fa87f;
    --sage-pale:    #edf4ed;
    --clay:         #c17f4f;
    --cream:        #faf7f2;
    --stone:        #e8e0d5;
    --ink:          #1e1e1e;
    --muted:        #7a7267;
    --white:        #ffffff;
    --r:            16px;
    --r-sm:         10px;
    --shadow:       0 4px 20px rgba(90,122,90,0.10);
    --shadow-hover: 0 14px 44px rgba(90,122,90,0.18);
    --trans:        0.25s cubic-bezier(.4,0,.2,1);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.services-page {
    background: var(--cream);
    min-height: 100vh;
    font-family: 'DM Sans', sans-serif;
    color: var(--ink);
}

/* ── HERO ── */
.services-hero {
    background: linear-gradient(135deg, #3d5c3d 0%, #5a7a5a 50%, #7a9e6a 100%);
    padding: 64px 24px 56px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.services-hero::before {
    content: '✂️';
    position: absolute; font-size: 200px; opacity: 0.06;
    top: -30px; right: -20px; pointer-events: none;
}
.services-hero::after {
    content: '🛁';
    position: absolute; font-size: 160px; opacity: 0.05;
    bottom: -20px; left: 0; pointer-events: none;
}
.services-hero h1 {
    font-family: 'Fraunces', serif;
    font-size: clamp(2rem, 5vw, 3.2rem);
    color: var(--white); letter-spacing: -0.5px; line-height: 1.2;
}
.services-hero p {
    color: rgba(255,255,255,.72); font-size: 1rem;
    margin: 10px auto 0; font-weight: 300; max-width: 500px;
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
.services-wrap {
    max-width: 1200px; margin: 0 auto;
    padding: 56px 24px 72px;
}

/* ── SECTION LABELS ── */
.section-eyebrow {
    display: flex; align-items: center;
    gap: 10px; margin-bottom: 6px;
}
.section-eyebrow span {
    font-size: .75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 1.5px;
    color: var(--sage);
}
.section-eyebrow::before,
.section-eyebrow::after {
    content: ''; flex: 0 0 28px; height: 1.5px;
    background: var(--sage-light); border-radius: 99px;
}
.section-heading {
    font-family: 'Fraunces', serif;
    font-size: clamp(1.5rem, 3vw, 2.1rem);
    color: var(--ink); margin-bottom: 6px;
}
.section-sub {
    color: var(--muted); font-size: .9rem; margin-bottom: 36px;
}

/* ── SERVICES GRID ── */
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 22px; margin-bottom: 72px;
}
.service-card {
    background: var(--white);
    border-radius: var(--r); border: 1px solid var(--stone);
    box-shadow: var(--shadow); padding: 32px 28px 28px;
    display: flex; flex-direction: column;
    align-items: flex-start; gap: 12px;
    position: relative; overflow: hidden;
    transition: transform var(--trans), box-shadow var(--trans), border-color var(--trans);
    animation: cardIn .45s ease both;
}
.service-card::before {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--sage), var(--sage-light));
    transform: scaleX(0); transform-origin: left;
    transition: transform var(--trans);
    border-radius: var(--r) var(--r) 0 0;
}
.service-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-hover); border-color: #c8dbc8; }
.service-card:hover::before { transform: scaleX(1); }
@keyframes cardIn {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}
.service-icon-wrap {
    width: 56px; height: 56px; background: var(--sage-pale);
    border-radius: 14px; display: flex;
    align-items: center; justify-content: center;
    transition: background var(--trans), transform var(--trans);
}
.service-card:hover .service-icon-wrap {
    background: var(--sage); transform: rotate(-6deg) scale(1.1);
}
.service-icon-wrap i { font-size: 1.3rem; color: var(--sage); transition: color var(--trans); }
.service-card:hover .service-icon-wrap i { color: var(--white); }
.service-name {
    font-family: 'Fraunces', serif;
    font-size: 1.1rem; color: var(--ink); line-height: 1.3;
}
.service-desc {
    font-size: .85rem; color: var(--muted);
    line-height: 1.6; flex: 1;
}
.service-footer {
    display: flex; align-items: center;
    justify-content: space-between; width: 100%;
    margin-top: 4px; padding-top: 14px;
    border-top: 1px solid var(--stone);
}
.service-price {
    font-family: 'Fraunces', serif;
    font-size: 1.2rem; font-weight: 700; color: var(--clay);
}
.service-price small {
    font-family: 'DM Sans', sans-serif;
    font-size: .72rem; color: var(--muted);
    font-weight: 400; margin-left: 2px;
}
.booking-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; background: var(--sage); color: var(--white);
    border: none; border-radius: var(--r-sm);
    font-family: 'DM Sans', sans-serif; font-size: .83rem;
    font-weight: 600; text-decoration: none; cursor: pointer;
    transition: background var(--trans), transform var(--trans);
}
.booking-btn:hover { background: var(--sage-light); transform: scale(1.04); color: var(--white); }
.booking-btn svg {
    width: 13px; height: 13px; stroke: currentColor; fill: none;
    stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;
}

/* ── WHY US ── */
.why-section {
    background: var(--white); border-radius: var(--r);
    border: 1px solid var(--stone); box-shadow: var(--shadow);
    overflow: hidden;
}
.why-inner { padding: 48px 40px; }
.why-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px; margin-top: 36px;
}
.why-card {
    display: flex; flex-direction: column;
    align-items: flex-start; gap: 12px; padding: 24px;
    background: var(--cream); border-radius: var(--r-sm);
    border: 1px solid var(--stone);
    transition: background var(--trans), transform var(--trans);
}
.why-card:hover { background: var(--sage-pale); transform: translateY(-3px); }
.why-icon {
    width: 48px; height: 48px; background: var(--white);
    border-radius: 12px; display: flex;
    align-items: center; justify-content: center;
    box-shadow: var(--shadow);
}
.why-icon i { font-size: 1.1rem; color: var(--sage); }
.why-card h5 { font-family: 'Fraunces', serif; font-size: 1rem; color: var(--ink); }
.why-card p { font-size: .83rem; color: var(--muted); line-height: 1.6; }

/* ── BANNER ── */
.banner-wrap {
    margin-top: 32px; border-radius: 0 0 var(--r) var(--r);
    overflow: hidden; max-height: 280px; position: relative;
}
.banner-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
.banner-wrap::before {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(to bottom, transparent 50%, rgba(45,106,79,.35));
    z-index: 1;
}
.banner-text {
    position: absolute; bottom: 20px; left: 50%;
    transform: translateX(-50%); z-index: 2;
    text-align: center; white-space: nowrap;
}
.banner-text span {
    background: rgba(255,255,255,.92); backdrop-filter: blur(8px);
    padding: 8px 24px; border-radius: 99px;
    font-family: 'Fraunces', serif; font-size: .95rem;
    color: var(--sage); font-weight: 700;
}

@media (max-width: 768px) {
    .services-wrap { padding: 36px 16px 56px; }
    .why-inner { padding: 28px 20px; }
    .services-grid { grid-template-columns: 1fr; }
}
</style>

<div class="services-page">

    <!-- HERO -->
    <div class="services-hero">
        <h1>🛁 Dịch Vụ Chăm Sóc<br>Thú Cưng</h1>
        <p>Đội ngũ chuyên nghiệp, tận tâm – nơi thú cưng được yêu thương như gia đình</p>
        <div class="hero-badges">
            <span class="hero-badge">✨ Chuyên nghiệp</span>
            <span class="hero-badge">🛡️ An toàn</span>
            <span class="hero-badge">💬 Tư vấn 24/7</span>
        </div>
    </div>

    <div class="services-wrap">

        <!-- SERVICES -->
        <div class="section-eyebrow"><span>Dịch vụ</span></div>
        <h2 class="section-heading">Chúng tôi cung cấp</h2>
        <p class="section-sub">Lựa chọn dịch vụ phù hợp và đặt lịch ngay hôm nay</p>

        <div class="services-grid">
            <?php foreach ($services as $i => $s): ?>
            <div class="service-card" style="animation-delay:<?= $i * 0.07 ?>s">
                <div class="service-icon-wrap">
                    <i class="fas <?= htmlspecialchars($s->icon) ?>"></i>
                </div>
                <h4 class="service-name"><?= htmlspecialchars($s->name) ?></h4>
                <p class="service-desc"><?= htmlspecialchars($s->desc) ?></p>
                <div class="service-footer">
                    <div class="service-price">
                        <?= number_format($s->price) ?>đ
                        <small>/ lần</small>
                    </div>
                    <a href="booking.php?service=<?= $s->id ?>" class="booking-btn">
                        <svg viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        Đặt lịch
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- WHY US -->
        <div class="why-section">
            <div class="why-inner">
                <div class="section-eyebrow"><span>Tại sao chọn chúng tôi</span></div>
                <h2 class="section-heading">Cam kết của PetShop</h2>
                <p class="section-sub">Chúng tôi không chỉ cung cấp dịch vụ – chúng tôi yêu thương thú cưng của bạn</p>

                <div class="why-grid">
                    <div class="why-card">
                        <div class="why-icon"><i class="fas fa-heart"></i></div>
                        <h5>Chuyên nghiệp</h5>
                        <p>Đội ngũ giàu kinh nghiệm, được đào tạo bài bản, chăm sóc tận tâm như gia đình.</p>
                    </div>
                    <div class="why-card">
                        <div class="why-icon"><i class="fas fa-shield-alt"></i></div>
                        <h5>An toàn tuyệt đối</h5>
                        <p>Sản phẩm chất lượng cao, dịch vụ vệ sinh an toàn, không gây hại cho thú cưng.</p>
                    </div>
                    <div class="why-card">
                        <div class="why-icon"><i class="fas fa-star"></i></div>
                        <h5>Tận tâm 24/7</h5>
                        <p>Tư vấn miễn phí mọi lúc, hỗ trợ sau dịch vụ, đồng hành cùng bạn và thú cưng.</p>
                    </div>
                    <div class="why-card">
                        <div class="why-icon"><i class="fas fa-tag"></i></div>
                        <h5>Giá cả hợp lý</h5>
                        <p>Dịch vụ cao cấp với mức giá phù hợp mọi gia đình, không phát sinh chi phí ẩn.</p>
                    </div>
                </div>
            </div>

            <div class="banner-wrap">
                <img src="assets/images/services/banner.png"
                     alt="Banner thú cưng"
                     onerror="this.closest('.banner-wrap').style.display='none'">
                <div class="banner-text">
                    <span>🐾 Thú cưng hạnh phúc – Chủ nhân vui lòng</span>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>