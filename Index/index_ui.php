<?php
/**
 * index_ui.php
 * Nhận biến từ index_control.php:
 *   $topProducts      array
 *   $featuredServices array
 *   $topBlogPosts     array
 */

include __DIR__ . '/../includes/header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root {
    --sage:         #5a7a5a;
    --sage-light:   #7fa87f;
    --sage-pale:    #edf4ed;
    --clay:         #c17f4f;
    --clay-light:   #fdf0e6;
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
*, *::before, *::after { box-sizing: border-box; }

.home-page { background: var(--cream); font-family: 'DM Sans', sans-serif; color: var(--ink); }

/* ── SECTION LABEL ── */
.sec-eyebrow {
    display: flex; align-items: center; gap: 10px; margin-bottom: 6px;
}
.sec-eyebrow span {
    font-size: .72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1.5px; color: var(--sage);
}
.sec-eyebrow::before, .sec-eyebrow::after {
    content: ''; flex: 0 0 24px; height: 1.5px;
    background: var(--sage-light); border-radius: 99px;
}
.sec-heading {
    font-family: 'Fraunces', serif;
    font-size: clamp(1.5rem, 3vw, 2rem); color: var(--ink); margin-bottom: 4px;
}
.sec-sub { color: var(--muted); font-size: .88rem; margin-bottom: 32px; }

/* ────────────────────────────────────────────
   HERO
──────────────────────────────────────────── */
.hero-section {
    background: linear-gradient(135deg, #2d4a2d 0%, #3d5c3d 40%, #5a7a5a 75%, #7a9e6a 100%);
    min-height: 88vh;
    display: flex; align-items: center;
    position: relative; overflow: hidden;
}
.hero-section::before {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 60% 70% at 70% 50%, rgba(122,158,106,0.25) 0%, transparent 60%),
        radial-gradient(ellipse 40% 40% at 20% 80%, rgba(193,127,79,0.12) 0%, transparent 50%);
    pointer-events: none;
}
/* Decorative paw prints */
.hero-section::after {
    content: '🐾';
    position: absolute; font-size: 320px; opacity: 0.04;
    bottom: -60px; right: -40px; pointer-events: none;
    animation: floatPaw 8s ease-in-out infinite;
}
@keyframes floatPaw {
    0%,100% { transform: rotate(-8deg) translateY(0); }
    50%      { transform: rotate(-8deg) translateY(-20px); }
}

.hero-inner {
    max-width: 1200px; margin: 0 auto;
    padding: 80px 40px;
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 60px; align-items: center;
    position: relative; z-index: 1;
    width: 100%;
}
@media (max-width: 900px) {
    .hero-inner { grid-template-columns: 1fr; text-align: center; gap: 36px; padding: 60px 24px; }
    .hero-img-col { order: -1; }
    .hero-btns { justify-content: center; }
}

.hero-tag {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.22);
    color: rgba(255,255,255,.85); padding: 6px 16px;
    border-radius: 99px; font-size: .78rem; font-weight: 600;
    letter-spacing: .5px; margin-bottom: 20px;
    backdrop-filter: blur(6px);
    animation: fadeUp .6s ease both;
}
.hero-title {
    font-family: 'Fraunces', serif;
    font-size: clamp(2.2rem, 5vw, 3.6rem);
    color: var(--white); line-height: 1.12; letter-spacing: -1px;
    margin-bottom: 18px;
    animation: fadeUp .6s .1s ease both;
}
.hero-title em { font-style: italic; color: #a8d5a8; }
.hero-desc {
    color: rgba(255,255,255,.7); font-size: 1rem; line-height: 1.75;
    max-width: 440px; margin-bottom: 32px; font-weight: 300;
    animation: fadeUp .6s .2s ease both;
}
.hero-btns {
    display: flex; gap: 12px; flex-wrap: wrap;
    animation: fadeUp .6s .3s ease both;
}
.btn-hero-primary {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 14px 28px; background: var(--white); color: var(--sage);
    border-radius: var(--r-sm); font-weight: 700; font-size: .95rem;
    text-decoration: none; border: none; cursor: pointer;
    transition: transform var(--trans), box-shadow var(--trans);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}
.btn-hero-primary:hover { transform: translateY(-3px); box-shadow: 0 10px 32px rgba(0,0,0,0.2); color: var(--sage); }
.btn-hero-outline {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 14px 28px;
    background: transparent; color: var(--white);
    border: 1.5px solid rgba(255,255,255,.45);
    border-radius: var(--r-sm); font-weight: 600; font-size: .95rem;
    text-decoration: none; cursor: pointer;
    transition: background var(--trans), border-color var(--trans);
    backdrop-filter: blur(4px);
}
.btn-hero-outline:hover { background: rgba(255,255,255,.12); border-color: rgba(255,255,255,.7); color: var(--white); }

.hero-stats {
    display: flex; gap: 28px; margin-top: 40px; flex-wrap: wrap;
    animation: fadeUp .6s .4s ease both;
}
.hero-stat { text-align: left; }
.hero-stat .num {
    font-family: 'Fraunces', serif; font-size: 1.7rem; font-weight: 700;
    color: var(--white); line-height: 1;
}
.hero-stat .lbl { font-size: .72rem; color: rgba(255,255,255,.55); font-weight: 500; margin-top: 2px; }

/* Hero image */
.hero-img-col { position: relative; }
.hero-img-wrap {
    position: relative; border-radius: 24px; overflow: hidden;
    box-shadow: 0 32px 80px rgba(0,0,0,0.35);
    animation: fadeUp .7s .2s ease both;
}
.hero-img-wrap img { width: 100%; height: 480px; object-fit: cover; display: block; }
.hero-badge-float {
    position: absolute; bottom: 20px; left: 20px;
    background: rgba(255,255,255,.95); backdrop-filter: blur(10px);
    padding: 10px 18px; border-radius: var(--r-sm);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    display: flex; align-items: center; gap: 10px;
}
.hero-badge-float .badge-icon { font-size: 1.4rem; }
.hero-badge-float .badge-text strong {
    display: block; font-size: .85rem; color: var(--ink); font-weight: 700;
}
.hero-badge-float .badge-text span { font-size: .72rem; color: var(--muted); }

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(22px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ────────────────────────────────────────────
   PRODUCTS
──────────────────────────────────────────── */
.products-section {
    padding: 72px 0 64px;
    max-width: 1200px; margin: 0 auto; padding-left: 24px; padding-right: 24px;
}
.products-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}
@media (max-width: 1100px) { .products-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 560px)  { .products-grid { grid-template-columns: 1fr; } }

.product-card {
    background: var(--white); border-radius: var(--r);
    border: 1px solid var(--stone); box-shadow: var(--shadow);
    overflow: hidden; display: flex; flex-direction: column;
    transition: transform var(--trans), box-shadow var(--trans);
    animation: fadeUp .5s ease both;
}
.product-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-hover); }

.pc-img {
    position: relative; height: 180px; overflow: hidden;
    background: var(--sage-pale);
}
.pc-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s ease; }
.product-card:hover .pc-img img { transform: scale(1.07); }
.pc-badge {
    position: absolute; top: 10px; left: 10px;
    background: var(--clay); color: var(--white);
    padding: 3px 10px; border-radius: 99px;
    font-size: .68rem; font-weight: 700;
    display: flex; align-items: center; gap: 4px;
}
.pc-body { padding: 14px 16px; flex: 1; display: flex; flex-direction: column; gap: 6px; }
.pc-name {
    font-family: 'Fraunces', serif; font-size: .95rem; color: var(--ink);
    line-height: 1.3;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.pc-price { font-family: 'Fraunces', serif; font-size: 1.1rem; color: var(--clay); font-weight: 700; }
.pc-footer {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 16px; border-top: 1px solid var(--stone);
}
.pc-sold { font-size: .72rem; color: var(--muted); }
.pc-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px; background: var(--sage); color: var(--white);
    border-radius: var(--r-sm); font-size: .78rem; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer;
    transition: background var(--trans);
}
.pc-btn:hover { background: var(--sage-light); color: var(--white); }
.pc-btn svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

.view-all-wrap { text-align: center; margin-top: 32px; }
.view-all-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 28px;
    border: 1.5px solid var(--sage); color: var(--sage);
    background: transparent; border-radius: var(--r-sm);
    font-family: 'DM Sans', sans-serif; font-size: .9rem; font-weight: 600;
    text-decoration: none; cursor: pointer;
    transition: background var(--trans), color var(--trans);
}
.view-all-btn:hover { background: var(--sage); color: var(--white); }
.view-all-btn svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

/* ────────────────────────────────────────────
   SERVICES STRIP
──────────────────────────────────────────── */
.services-strip {
    background: linear-gradient(135deg, #3d5c3d, #5a7a5a);
    padding: 64px 24px;
}
.services-strip-inner { max-width: 1200px; margin: 0 auto; }
.services-strip .sec-heading { color: var(--white); }
.services-strip .sec-sub { color: rgba(255,255,255,.6); }
.services-strip .sec-eyebrow span { color: #a8d5a8; }
.services-strip .sec-eyebrow::before, .services-strip .sec-eyebrow::after { background: rgba(168,213,168,.4); }

.services-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
}
@media (max-width: 900px) { .services-grid { grid-template-columns: 1fr; } }
@media (max-width: 1100px) and (min-width: 901px) { .services-grid { grid-template-columns: repeat(2,1fr); } }

.svc-card {
    background: rgba(255,255,255,.1); backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: var(--r); padding: 24px 20px;
    display: flex; flex-direction: column; gap: 10px;
    transition: background var(--trans), transform var(--trans);
}
.svc-card:hover { background: rgba(255,255,255,.18); transform: translateY(-4px); }
.svc-icon {
    width: 48px; height: 48px;
    background: rgba(255,255,255,.15); border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
}
.svc-icon i { font-size: 1.2rem; color: #a8d5a8; }
.svc-name { font-family: 'Fraunces', serif; font-size: 1rem; color: var(--white); }
.svc-desc { font-size: .8rem; color: rgba(255,255,255,.6); line-height: 1.6; flex: 1; }
.svc-footer {
    display: flex; align-items: center; justify-content: space-between;
    padding-top: 12px; border-top: 1px solid rgba(255,255,255,.12);
}
.svc-price { font-family: 'Fraunces', serif; font-size: 1.05rem; color: #f4c06f; font-weight: 700; }
.svc-book-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px;
    background: rgba(255,255,255,.15); color: var(--white);
    border: 1px solid rgba(255,255,255,.3); border-radius: var(--r-sm);
    font-size: .78rem; font-weight: 600; text-decoration: none;
    transition: background var(--trans);
}
.svc-book-btn:hover { background: rgba(255,255,255,.28); color: var(--white); }

/* ────────────────────────────────────────────
   BLOG
──────────────────────────────────────────── */
.blog-section {
    max-width: 1200px; margin: 0 auto;
    padding: 72px 24px 64px;
}
.blog-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
}
@media (max-width: 900px) { .blog-grid { grid-template-columns: 1fr; } }
@media (max-width: 1100px) and (min-width: 901px) { .blog-grid { grid-template-columns: repeat(2,1fr); } }

.blog-card {
    background: var(--white); border-radius: var(--r);
    border: 1px solid var(--stone); box-shadow: var(--shadow);
    overflow: hidden; display: flex; flex-direction: column;
    transition: transform var(--trans), box-shadow var(--trans);
    animation: fadeUp .5s ease both;
}
.blog-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-hover); }
.bc-img { position: relative; height: 180px; overflow: hidden; background: var(--sage-pale); }
.bc-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s ease; }
.blog-card:hover .bc-img img { transform: scale(1.06); }
.bc-read-badge {
    position: absolute; top: 10px; right: 10px;
    background: rgba(255,255,255,.92); backdrop-filter: blur(4px);
    padding: 3px 10px; border-radius: 99px;
    font-size: .68rem; font-weight: 700; color: var(--sage);
    display: flex; align-items: center; gap: 4px;
}
.bc-body { padding: 16px 18px; flex: 1; display: flex; flex-direction: column; gap: 7px; }
.bc-date { font-size: .72rem; color: var(--muted); }
.bc-title {
    font-family: 'Fraunces', serif; font-size: 1rem; color: var(--ink);
    line-height: 1.35;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.bc-excerpt {
    font-size: .8rem; color: var(--muted); line-height: 1.6; flex: 1;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.bc-footer { padding: 10px 18px; border-top: 1px solid var(--stone); }
.bc-link {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .8rem; font-weight: 600; color: var(--sage);
    text-decoration: none;
    transition: gap var(--trans);
}
.bc-link:hover { gap: 8px; color: var(--sage-light); }
.bc-link svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

/* ────────────────────────────────────────────
   WHY US
──────────────────────────────────────────── */
.why-section {
    background: var(--white);
    padding: 64px 24px;
    border-top: 1px solid var(--stone);
    border-bottom: 1px solid var(--stone);
}
.why-inner { max-width: 1200px; margin: 0 auto; }
.why-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
@media (max-width: 700px) { .why-grid { grid-template-columns: 1fr; } }

.why-card {
    padding: 28px 24px; background: var(--cream);
    border-radius: var(--r); border: 1px solid var(--stone);
    display: flex; flex-direction: column; align-items: flex-start; gap: 12px;
    transition: background var(--trans), transform var(--trans);
}
.why-card:hover { background: var(--sage-pale); transform: translateY(-3px); }
.why-icon {
    width: 50px; height: 50px; background: var(--white);
    border-radius: 12px; display: flex; align-items: center; justify-content: center;
    box-shadow: var(--shadow);
}
.why-icon i { font-size: 1.2rem; color: var(--sage); }
.why-card h5 { font-family: 'Fraunces', serif; font-size: 1.05rem; color: var(--ink); margin: 0; }
.why-card p { font-size: .84rem; color: var(--muted); line-height: 1.65; margin: 0; }

/* ────────────────────────────────────────────
   CTA BANNER
──────────────────────────────────────────── */
.cta-banner {
    background: linear-gradient(135deg, #3d5c3d, #5a7a5a, #7a9e6a);
    padding: 72px 24px; text-align: center;
    position: relative; overflow: hidden;
}
.cta-banner::before {
    content: '🐾'; font-size: 220px; opacity: 0.06;
    position: absolute; top: -40px; right: -30px; pointer-events: none;
}
.cta-banner h2 {
    font-family: 'Fraunces', serif;
    font-size: clamp(1.7rem, 4vw, 2.5rem);
    color: var(--white); margin-bottom: 12px;
}
.cta-banner p { color: rgba(255,255,255,.7); font-size: .95rem; margin-bottom: 28px; max-width: 480px; margin-left: auto; margin-right: auto; }
.cta-btns { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
.cta-btn-white {
    padding: 13px 28px; background: var(--white); color: var(--sage);
    border-radius: var(--r-sm); font-weight: 700; font-size: .9rem;
    text-decoration: none; transition: transform var(--trans), box-shadow var(--trans);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}
.cta-btn-white:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.18); color: var(--sage); }
.cta-btn-ghost {
    padding: 13px 28px; background: transparent; color: var(--white);
    border: 1.5px solid rgba(255,255,255,.4); border-radius: var(--r-sm);
    font-weight: 600; font-size: .9rem; text-decoration: none;
    transition: background var(--trans);
    backdrop-filter: blur(4px);
}
.cta-btn-ghost:hover { background: rgba(255,255,255,.14); color: var(--white); }
</style>

<div class="home-page">

<!-- ══ HERO ══════════════════════════════════════ -->
<section class="hero-section">
    <div class="hero-inner">
        <div class="hero-text-col">
            <div class="hero-tag">🐾 PetShop — Yêu thương tận tâm</div>
            <h1 class="hero-title">
                Chăm sóc thú cưng<br>
                <em>như gia đình</em>
            </h1>
            <p class="hero-desc">
                Sản phẩm chất lượng cao và dịch vụ chuyên nghiệp dành riêng cho thú cưng yêu quý của bạn. Từ thức ăn, phụ kiện đến spa cao cấp.
            </p>
            <div class="hero-btns">
                <a href="services.php" class="btn-hero-primary">
                    Xem dịch vụ
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
                <a href="products.php" class="btn-hero-outline">🛒 Mua sắm ngay</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat"><div class="num">1000+</div><div class="lbl">Khách hàng hài lòng</div></div>
                <div class="hero-stat"><div class="num">50+</div><div class="lbl">Sản phẩm chất lượng</div></div>
                <div class="hero-stat"><div class="num">6</div><div class="lbl">Dịch vụ chuyên nghiệp</div></div>
            </div>
        </div>
        <div class="hero-img-col">
            <div class="hero-img-wrap">
                <img src="assets/images/catgiaodienchinh.jpg" alt="Thú cưng dễ thương"
                     onerror="this.closest('.hero-img-wrap').style.background='var(--sage-pale)'">
                <div class="hero-badge-float">
                    <span class="badge-icon">⭐</span>
                    <div class="badge-text">
                        <strong>Đánh giá 4.9/5</strong>
                        <span>Từ 500+ đánh giá thực</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══ SẢN PHẨM NỔI BẬT ═════════════════════════ -->
<section class="products-section">
    <div class="sec-eyebrow"><span>Sản phẩm</span></div>
    <h2 class="sec-heading">Bán chạy nhất</h2>
    <p class="sec-sub">Những sản phẩm được yêu thích và mua nhiều nhất tại PetShop</p>

    <div class="products-grid">
        <?php foreach ($topProducts as $i => $p): ?>
        <div class="product-card" style="animation-delay:<?= $i * 0.08 ?>s">
            <div class="pc-img">
                <img src="assets/images/<?= htmlspecialchars($p['img']) ?>"
                     alt="<?= htmlspecialchars($p['name']) ?>"
                     onerror="this.closest('.pc-img').style.background='var(--sage-pale)'">
                <span class="pc-badge">
                    🔥 <?= number_format($p['luot_mua']) ?> đã mua
                </span>
            </div>
            <div class="pc-body">
                <div class="pc-name"><?= htmlspecialchars($p['name']) ?></div>
                <div class="pc-price"><?= number_format($p['price']) ?>đ</div>
            </div>
            <div class="pc-footer">
                <span class="pc-sold">Đã bán: <?= number_format($p['luot_mua']) ?></span>
                <a href="shopcard.php?add=<?= $p['id'] ?>" class="pc-btn">
                    <svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    Thêm
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="view-all-wrap">
        <a href="products.php" class="view-all-btn">
            Xem tất cả sản phẩm
            <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>
</section>

<!-- ══ DỊCH VỤ ══════════════════════════════════ -->
<section class="services-strip">
    <div class="services-strip-inner">
        <div class="sec-eyebrow"><span>Dịch vụ</span></div>
        <h2 class="sec-heading">Dịch vụ nổi bật</h2>
        <p class="sec-sub" style="margin-bottom:28px;">Chăm sóc toàn diện cho thú cưng với đội ngũ chuyên nghiệp</p>

        <div class="services-grid">
            <?php foreach ($featuredServices as $i => $s): ?>
            <div class="svc-card">
                <div class="svc-icon"><i class="fas <?= htmlspecialchars($s['icon']) ?>"></i></div>
                <div class="svc-name"><?= htmlspecialchars($s['name']) ?></div>
                <div class="svc-desc"><?= htmlspecialchars($s['desc']) ?></div>
                <div class="svc-footer">
                    <span class="svc-price"><?= number_format($s['price']) ?>đ</span>
                    <a href="booking.php?service=<?= $s['id'] ?>" class="svc-book-btn">Đặt lịch →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="view-all-wrap" style="margin-top:28px;">
            <a href="services.php" class="view-all-btn" style="border-color:rgba(255,255,255,.4);color:var(--white);">
                Xem tất cả dịch vụ
                <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>
</section>

<!-- ══ TIN TỨC ══════════════════════════════════ -->
<section class="blog-section">
    <div class="sec-eyebrow"><span>Blog</span></div>
    <h2 class="sec-heading">Bài viết nổi bật</h2>
    <p class="sec-sub">Những bài viết được đọc nhiều nhất từ đội ngũ chuyên gia PetShop</p>

    <div class="blog-grid">
        <?php foreach ($topBlogPosts as $i => $post): ?>
        <div class="blog-card" style="animation-delay:<?= $i * 0.08 ?>s">
            <div class="bc-img">
                <img src="<?= htmlspecialchars($post['img'] ?? '') ?>"
                     alt="<?= htmlspecialchars($post['title']) ?>"
                     onerror="this.closest('.bc-img').style.background='var(--sage-pale)'">
                <span class="bc-read-badge">
                    👁️ <?= number_format($post['luot_doc']) ?> lượt đọc
                </span>
            </div>
            <div class="bc-body">
                <span class="bc-date">📅 <?= htmlspecialchars($post['date']) ?></span>
                <div class="bc-title"><?= htmlspecialchars($post['title']) ?></div>
                <div class="bc-excerpt"><?= htmlspecialchars($post['excerpt'] ?? '') ?></div>
            </div>
            <div class="bc-footer">
                <a href="blog-detail.php?id=<?= $post['id'] ?>" class="bc-link">
                    Đọc tiếp
                    <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="view-all-wrap">
        <a href="blog.php" class="view-all-btn">
            Xem tất cả bài viết
            <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>
</section>

<!-- ══ TẠI SAO CHỌN CHÚNG TÔI ═══════════════════ -->
<section class="why-section">
    <div class="why-inner">
        <div style="text-align:center; margin-bottom:36px;">
            <div class="sec-eyebrow" style="justify-content:center;"><span>Cam kết</span></div>
            <h2 class="sec-heading" style="text-align:center;">Tại sao chọn PetShop?</h2>
        </div>
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
                <div class="why-icon"><i class="fas fa-users"></i></div>
                <h5>Tận tâm 24/7</h5>
                <p>Tư vấn miễn phí mọi lúc, hỗ trợ sau dịch vụ, đồng hành cùng bạn và thú cưng.</p>
            </div>
        </div>
    </div>
</section>

<!-- ══ CTA ═══════════════════════════════════════ -->
<section class="cta-banner">
    <h2>Bắt đầu chăm sóc thú cưng ngay hôm nay 🐾</h2>
    <p>Đặt lịch dịch vụ hoặc khám phá hàng trăm sản phẩm chất lượng cao tại PetShop</p>
    <div class="cta-btns">
        <a href="booking.php" class="cta-btn-white">📅 Đặt lịch ngay</a>
        <a href="products.php" class="cta-btn-ghost">🛒 Xem sản phẩm</a>
    </div>
</section>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
