<?php
/**
 * blog-detail_ui.php
 * Chỉ chứa HTML, CSS — không có logic, không có SQL.
 * Nhận biến từ blog-detail_control.php:
 *   $post      BlogDetail      bài viết chi tiết
 *   $featured  FeaturedPost[]  danh sách bài nổi bật
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

.detail-page {
    background: var(--cream); min-height: 100vh;
    font-family: 'DM Sans', sans-serif; color: var(--ink);
}
.detail-hero {
    background: linear-gradient(135deg, #3d5c3d 0%, #5a7a5a 50%, #7a9e6a 100%);
    padding: 56px 24px 48px; position: relative; overflow: hidden;
}
.detail-hero::before {
    content: '📖'; position: absolute; font-size: 180px; opacity: 0.07;
    top: -20px; right: -10px; pointer-events: none;
}
.detail-hero-inner { max-width: 800px; margin: 0 auto; }
.back-link {
    display: inline-flex; align-items: center; gap: 6px;
    color: rgba(255,255,255,.72); text-decoration: none;
    font-size: .85rem; font-weight: 500; margin-bottom: 16px;
    transition: color var(--trans);
}
.back-link:hover { color: var(--white); }
.back-link svg {
    width: 14px; height: 14px; stroke: currentColor; fill: none;
    stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;
}
.detail-hero h1 {
    font-family: 'Fraunces', serif;
    font-size: clamp(1.6rem, 4vw, 2.6rem);
    color: var(--white); line-height: 1.2; margin-bottom: 14px;
}
.detail-meta { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
.meta-badge {
    background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25);
    color: var(--white); padding: 4px 14px; border-radius: 99px;
    font-size: .78rem; font-weight: 500; backdrop-filter: blur(6px);
}
.detail-wrap {
    max-width: 1200px; margin: 0 auto;
    padding: 48px 24px 72px;
    display: grid; grid-template-columns: 1fr 300px;
    gap: 32px; align-items: start;
}
@media (max-width: 900px) {
    .detail-wrap { grid-template-columns: 1fr; }
    .detail-sidebar { order: -1; }
}
.article-card {
    background: var(--white); border-radius: var(--r);
    border: 1px solid var(--stone); box-shadow: var(--shadow); overflow: hidden;
}
.article-cover { width: 100%; height: 340px; overflow: hidden; position: relative; }
.article-cover img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s ease; }
.article-cover:hover img { transform: scale(1.03); }
.article-body { padding: 36px 40px; }
.content-section {
    margin-bottom: 32px; padding-bottom: 32px; border-bottom: 1px solid var(--stone);
}
.content-section:last-of-type { border-bottom: none; margin-bottom: 0; }
.content-label {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: .72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1.4px; color: var(--sage); margin-bottom: 12px;
    padding: 4px 12px; background: var(--sage-pale); border-radius: 99px;
}
.content-section h3 {
    font-family: 'Fraunces', serif; font-size: 1.3rem;
    color: var(--ink); margin-bottom: 12px; line-height: 1.3;
}
.content-section p, .article-lead {
    font-size: .95rem; color: var(--muted); line-height: 1.85;
}
.article-lead { font-size: 1rem; margin-bottom: 0; color: #555; }
.content-img {
    width: 100%; border-radius: var(--r-sm); margin-top: 16px;
    object-fit: cover; max-height: 260px; border: 1px solid var(--stone);
}
.detail-sidebar { position: sticky; top: 24px; display: flex; flex-direction: column; gap: 20px; }
.sidebar-card {
    background: var(--white); border-radius: var(--r);
    border: 1px solid var(--stone); box-shadow: var(--shadow); overflow: hidden;
}
.sidebar-card-header {
    padding: 16px 20px; border-bottom: 1px solid var(--stone);
    font-family: 'Fraunces', serif; font-size: 1rem; color: var(--ink);
    display: flex; align-items: center; gap: 8px;
}
.featured-item {
    display: flex; gap: 12px; align-items: flex-start;
    padding: 14px 16px; border-bottom: 1px solid var(--stone);
    text-decoration: none; transition: background var(--trans);
}
.featured-item:last-child { border-bottom: none; }
.featured-item:hover { background: var(--sage-pale); }
.featured-thumb {
    width: 62px; height: 62px; border-radius: var(--r-sm);
    object-fit: cover; flex-shrink: 0; border: 1px solid var(--stone);
}
.featured-info { flex: 1; min-width: 0; }
.featured-title {
    font-size: .83rem; font-weight: 600; color: var(--ink); line-height: 1.35;
    display: -webkit-box; -webkit-line-clamp: 2;
    -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 4px;
}
.featured-date { font-size: .72rem; color: var(--muted); }
.newsletter-section {
    border-radius: var(--r); overflow: hidden; position: relative;
    background: linear-gradient(135deg, #3d5c3d, #5a7a5a, #7a9e6a);
    padding: 48px 36px; text-align: center;
    margin-top: 32px; grid-column: 1 / -1;
}
.newsletter-section::before {
    content: '✉️'; position: absolute; font-size: 140px; opacity: 0.07;
    top: -10px; right: 10px; pointer-events: none;
}
.newsletter-section h3 {
    font-family: 'Fraunces', serif; font-size: 1.7rem;
    color: var(--white); margin-bottom: 8px;
}
.newsletter-section p {
    color: rgba(255,255,255,.72); font-size: .9rem;
    margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto;
}
.newsletter-form { display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }
.newsletter-form input[type="email"] {
    flex: 1; min-width: 200px; max-width: 340px; padding: 11px 18px;
    border: none; border-radius: var(--r-sm);
    font-family: 'DM Sans', sans-serif; font-size: .9rem;
    background: rgba(255,255,255,.95); color: var(--ink); outline: none;
}
.newsletter-form input::placeholder { color: var(--muted); }
.newsletter-submit {
    padding: 11px 26px; background: var(--clay); color: var(--white);
    border: none; border-radius: var(--r-sm);
    font-family: 'DM Sans', sans-serif; font-size: .9rem; font-weight: 600;
    cursor: pointer; transition: background var(--trans), transform var(--trans);
}
.newsletter-submit:hover { background: #d4924f; transform: scale(1.04); }
@media (max-width: 768px) {
    .article-body { padding: 24px 20px; }
    .detail-wrap { padding: 28px 16px 56px; }
    .newsletter-section { padding: 36px 20px; }
    .newsletter-form { flex-direction: column; align-items: center; }
    .newsletter-form input[type="email"] { width: 100%; max-width: 100%; }
    .newsletter-submit { width: 100%; }
}
</style>

<div class="detail-page">

    <div class="detail-hero">
        <div class="detail-hero-inner">
            <a href="blog.php" class="back-link">
                <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Quay lại Blog
            </a>
            <h1><?= htmlspecialchars($post->title) ?></h1>
            <div class="detail-meta">
                <span class="meta-badge">📅 <?= htmlspecialchars($post->date) ?></span>
                <span class="meta-badge">🐾 PetShop Blog</span>
            </div>
        </div>
    </div>

    <div class="detail-wrap">

        <article class="article-card">
            <?php if (!empty($post->img)): ?>
            <div class="article-cover">
                <img src="<?= htmlspecialchars($post->img) ?>"
                     alt="<?= htmlspecialchars($post->title) ?>"
                     onerror="this.closest('.article-cover').style.display='none'">
            </div>
            <?php endif; ?>

            <div class="article-body">

                <?php if (!empty($post->content)): ?>
                <div class="content-section">
                    <span class="content-label">📄 Nội dung</span>
                    <p class="article-lead"><?= nl2br(htmlspecialchars($post->content)) ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($post->nguyen_nhan_pho_bien)): ?>
                <div class="content-section">
                    <span class="content-label">🔍 Nguyên nhân</span>
                    <h3>Nguyên nhân phổ biến</h3>
                    <p><?= nl2br(htmlspecialchars($post->nguyen_nhan_pho_bien)) ?></p>
                    <?php if (!empty($post->hinh_anh_2)): ?>
                        <img src="<?= htmlspecialchars($post->hinh_anh_2) ?>"
                             alt="Minh họa nguyên nhân" class="content-img"
                             onerror="this.style.display='none'">
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($post->huong_dan)): ?>
                <div class="content-section">
                    <span class="content-label">📋 Hướng dẫn</span>
                    <h3>Hướng dẫn thực hiện</h3>
                    <p><?= nl2br(htmlspecialchars($post->huong_dan)) ?></p>
                    <?php if (!empty($post->hinh_anh_3)): ?>
                        <img src="<?= htmlspecialchars($post->hinh_anh_3) ?>"
                             alt="Minh họa hướng dẫn" class="content-img"
                             onerror="this.style.display='none'">
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($post->cach_cham)): ?>
                <div class="content-section">
                    <span class="content-label">💚 Chăm sóc</span>
                    <h3>Cách chăm sóc</h3>
                    <p><?= nl2br(htmlspecialchars($post->cach_cham)) ?></p>
                    <?php if (!empty($post->hinh_anh_4)): ?>
                        <img src="<?= htmlspecialchars($post->hinh_anh_4) ?>"
                             alt="Minh họa chăm sóc" class="content-img"
                             onerror="this.style.display='none'">
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </div>
        </article>

        <aside class="detail-sidebar">
            <div class="sidebar-card">
                <div class="sidebar-card-header">⭐ Bài viết nổi bật</div>
                <?php foreach ($featured as $f): ?>
                <a href="blog-detail.php?id=<?= $f->id ?>" class="featured-item">
                    <img src="<?= htmlspecialchars($f->img) ?>"
                         alt="<?= htmlspecialchars($f->title) ?>"
                         class="featured-thumb"
                         onerror="this.style.background='var(--sage-pale)';this.src=''">
                    <div class="featured-info">
                        <div class="featured-title"><?= htmlspecialchars($f->title) ?></div>
                        <div class="featured-date">📅 <?= htmlspecialchars($f->date) ?></div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </aside>

        <div class="newsletter-section">
            <h3>📬 Đăng ký nhận tin</h3>
            <p>Nhận những bài viết và mẹo chăm sóc thú cưng mới nhất qua email</p>
            <form class="newsletter-form">
                <input type="email" placeholder="Email của bạn...">
                <button type="submit" class="newsletter-submit">Đăng ký ngay</button>
            </form>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>