<?php
/**
 * blog_ui.php
 * Nhận biến từ blog_control.php:
 *   $posts  BlogPost[]  danh sách bài viết
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
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.blog-page {
    background: var(--cream);
    min-height: 100vh;
    font-family: 'DM Sans', sans-serif;
    color: var(--ink);
}

/* ── HERO ── */
.blog-hero {
    background: linear-gradient(135deg, #3d5c3d 0%, #5a7a5a 50%, #7a9e6a 100%);
    padding: 64px 24px 56px;
    text-align: center;
    position: relative; overflow: hidden;
}
.blog-hero::before {
    content: '📰';
    position: absolute; font-size: 200px; opacity: 0.06;
    top: -30px; right: -20px; pointer-events: none;
}
.blog-hero::after {
    content: '🐾';
    position: absolute; font-size: 160px; opacity: 0.05;
    bottom: -20px; left: 0; pointer-events: none;
}
.blog-hero h1 {
    font-family: 'Fraunces', serif;
    font-size: clamp(2rem, 5vw, 3.2rem);
    color: var(--white); letter-spacing: -0.5px; line-height: 1.2;
}
.blog-hero p {
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
.blog-wrap {
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
    color: var(--muted); font-size: .9rem; margin-bottom: 28px;
}

/* ── SEARCH ── */
.blog-search-wrap {
    position: relative;
    max-width: 540px;
    margin: 0 auto 40px;
}
.blog-search-row {
    display: flex; align-items: center;
    background: var(--white);
    border: 1.5px solid var(--stone);
    border-radius: var(--r);
    transition: border-color var(--trans), box-shadow var(--trans);
}
.blog-search-row:focus-within {
    border-color: var(--sage);
    box-shadow: 0 0 0 3px rgba(90,122,90,.12);
}
.blog-search-icon {
    padding: 0 14px; color: var(--sage);
    display: flex; align-items: center; flex-shrink: 0;
}
.blog-search-icon svg {
    width: 18px; height: 18px; stroke: currentColor; fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}
.blog-search-input {
    flex: 1; border: none; outline: none;
    padding: 13px 0;
    font-family: 'DM Sans', sans-serif;
    font-size: .93rem; background: transparent; color: var(--ink);
}
.blog-search-input::placeholder { color: var(--muted); }
.blog-search-clear {
    padding: 0 14px; background: none; border: none;
    color: var(--muted); cursor: pointer; font-size: 1rem;
    display: none; transition: color var(--trans);
}
.blog-search-clear:hover { color: var(--ink); }

/* dropdown gợi ý */
.blog-search-dropdown {
    display: none; position: absolute;
    top: calc(100% + 6px); left: 0; right: 0;
    background: var(--white);
    border: 1.5px solid var(--stone);
    border-radius: var(--r-sm);
    box-shadow: var(--shadow-hover);
    z-index: 100; overflow: hidden;
}
.blog-search-dropdown.open { display: block; }
.search-suggestion {
    display: flex; align-items: center; gap: 10px;
    padding: 11px 16px; cursor: pointer;
    border-bottom: 1px solid var(--stone);
    text-decoration: none; color: var(--ink);
    transition: background var(--trans);
}
.search-suggestion:last-child { border-bottom: none; }
.search-suggestion:hover { background: var(--sage-pale); }
.search-suggestion svg {
    width: 14px; height: 14px; flex-shrink: 0;
    stroke: var(--sage); fill: none;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}
.search-suggestion-title { font-size: .87rem; line-height: 1.3; }
.search-suggestion-title mark { background: none; color: var(--sage); font-weight: 600; }
.search-no-result {
    padding: 14px 16px; font-size: .87rem;
    color: var(--muted); text-align: center;
}

/* ── BLOG GRID ── */
.blog-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 24px; margin-bottom: 72px;
}
.blog-card {
    background: var(--white);
    border-radius: var(--r); border: 1px solid var(--stone);
    box-shadow: var(--shadow); overflow: hidden;
    display: flex; flex-direction: column;
    transition: transform var(--trans), box-shadow var(--trans), border-color var(--trans);
    animation: cardIn .45s ease both;
}
.blog-card:hover {
    transform: translateY(-6px);
    box-shadow: var(--shadow-hover);
    border-color: #c8dbc8;
}
@keyframes cardIn {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}
.blog-card-img {
    position: relative; overflow: hidden;
    height: 210px; background: var(--sage-pale);
}
.blog-card-img img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .45s ease;
}
.blog-card:hover .blog-card-img img { transform: scale(1.06); }
.blog-card-date {
    position: absolute; bottom: 12px; left: 12px;
    background: rgba(255,255,255,.92);
    backdrop-filter: blur(6px);
    padding: 3px 12px; border-radius: 99px;
    font-size: .72rem; font-weight: 600; color: var(--sage);
}
.blog-card-body {
    padding: 22px 20px; flex: 1;
    display: flex; flex-direction: column; gap: 10px;
}
.blog-card-title {
    font-family: 'Fraunces', serif;
    font-size: 1.08rem; color: var(--ink); line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.blog-card-excerpt {
    font-size: .84rem; color: var(--muted); line-height: 1.65; flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
}
.blog-card-footer {
    padding-top: 14px; border-top: 1px solid var(--stone); margin-top: 4px;
}
.read-more-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; background: transparent;
    color: var(--sage); border: 1.5px solid var(--sage);
    border-radius: var(--r-sm); font-family: 'DM Sans', sans-serif;
    font-size: .83rem; font-weight: 600; text-decoration: none;
    transition: background var(--trans), color var(--trans), transform var(--trans);
}
.read-more-btn:hover { background: var(--sage); color: var(--white); transform: scale(1.04); }
.read-more-btn svg {
    width: 13px; height: 13px; stroke: currentColor; fill: none;
    stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;
    transition: transform var(--trans);
}
.read-more-btn:hover svg { transform: translateX(3px); }

/* ── EMPTY STATE ── */
.empty-state {
    text-align: center; padding: 80px 20px;
    color: var(--muted); grid-column: 1/-1;
}
.empty-state .emoji { font-size: 3.5rem; }
.empty-state h3 {
    font-family: 'Fraunces', serif;
    font-size: 1.3rem; margin: 12px 0 6px; color: var(--ink);
}

/* ── NEWSLETTER ── */
.newsletter-section {
    border-radius: var(--r); overflow: hidden; position: relative;
    background: linear-gradient(135deg, #3d5c3d, #5a7a5a, #7a9e6a);
    padding: 56px 40px; text-align: center;
}
.newsletter-section::before {
    content: '✉️';
    position: absolute; font-size: 160px; opacity: 0.07;
    top: -20px; right: 10px; pointer-events: none;
}
.newsletter-section h3 {
    font-family: 'Fraunces', serif;
    font-size: 1.8rem; color: var(--white); margin-bottom: 8px;
}
.newsletter-section p {
    color: rgba(255,255,255,.72); font-size: .92rem;
    margin-bottom: 28px; max-width: 440px; margin-left: auto; margin-right: auto;
}
.newsletter-form {
    display: flex; justify-content: center;
    gap: 10px; flex-wrap: wrap;
}
.newsletter-form input[type="email"] {
    flex: 1; min-width: 220px; max-width: 360px;
    padding: 12px 20px; border: none; border-radius: var(--r-sm);
    font-family: 'DM Sans', sans-serif; font-size: .92rem;
    background: rgba(255,255,255,.95); color: var(--ink); outline: none;
}
.newsletter-form input[type="email"]::placeholder { color: var(--muted); }
.newsletter-submit {
    padding: 12px 28px; background: var(--clay); color: var(--white);
    border: none; border-radius: var(--r-sm);
    font-family: 'DM Sans', sans-serif; font-size: .92rem; font-weight: 600;
    cursor: pointer; transition: background var(--trans), transform var(--trans);
}
.newsletter-submit:hover { background: #d4924f; transform: scale(1.04); }

@media (max-width: 768px) {
    .blog-wrap { padding: 36px 16px 56px; }
    .blog-grid { grid-template-columns: 1fr; }
    .newsletter-section { padding: 40px 20px; }
    .newsletter-form { flex-direction: column; align-items: center; }
    .newsletter-form input[type="email"] { width: 100%; max-width: 100%; }
    .newsletter-submit { width: 100%; }
    .blog-search-wrap { max-width: 100%; }
}
</style>

<div class="blog-page">

    <!-- HERO -->
    <div class="blog-hero">
        <h1>📰 Tin Tức & Blog</h1>
        <p>Cập nhật kiến thức và mẹo chăm sóc thú cưng hữu ích nhất</p>
        <div class="hero-badges">
            <span class="hero-badge">🐶 Chó & Mèo</span>
            <span class="hero-badge">🐹 Hamster</span>
            <span class="hero-badge">💡 Mẹo hay</span>
        </div>
    </div>

    <div class="blog-wrap">

        <div class="section-eyebrow"><span>Bài viết</span></div>
        <h2 class="section-heading">Kiến thức thú cưng</h2>
        <p class="section-sub">Những bài viết bổ ích từ đội ngũ chuyên gia của PetShop</p>

        <!-- SEARCH -->
        <div class="blog-search-wrap">
            <div class="blog-search-row">
                <span class="blog-search-icon">
                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="22" y2="22"/></svg>
                </span>
                <input id="blogSearchInput" class="blog-search-input"
                       type="text" placeholder="Tìm kiếm bài viết..." autocomplete="off">
                <button id="blogSearchClear" class="blog-search-clear" aria-label="Xóa">✕</button>
            </div>
            <div id="blogSearchDropdown" class="blog-search-dropdown"></div>
        </div>

        <div class="blog-grid">
            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <div class="emoji">📭</div>
                    <h3>Chưa có bài viết nào</h3>
                    <p>Hãy quay lại sau nhé!</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $i => $post): ?>
                <div class="blog-card" style="animation-delay:<?= $i * 0.08 ?>s">
                    <div class="blog-card-img">
                        <img src="<?= htmlspecialchars($post->img) ?>"
                             alt="<?= htmlspecialchars($post->title) ?>"
                             onerror="this.closest('.blog-card-img').style.background='var(--sage-pale)'">
                        <span class="blog-card-date">📅 <?= htmlspecialchars($post->date) ?></span>
                    </div>
                    <div class="blog-card-body">
                        <h3 class="blog-card-title"><?= htmlspecialchars($post->title) ?></h3>
                        <p class="blog-card-excerpt"><?= htmlspecialchars($post->excerpt) ?></p>
                        <div class="blog-card-footer">
                            <a href="blog-detail.php?id=<?= $post->id ?>" class="read-more-btn">
                                Xem thêm
                                <svg viewBox="0 0 24 24">
                                    <line x1="5" y1="12" x2="19" y2="12"/>
                                    <polyline points="12 5 19 12 12 19"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- NEWSLETTER -->
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

<script>
(function () {
    const POSTS = <?= json_encode(array_map(fn($p) => [
        'id'    => $p->id,
        'title' => $p->title,
        'url'   => 'blog-detail.php?id=' . $p->id,
    ], $posts), JSON_UNESCAPED_UNICODE) ?>;

    const input    = document.getElementById('blogSearchInput');
    const dropdown = document.getElementById('blogSearchDropdown');
    const clearBtn = document.getElementById('blogSearchClear');
    const allCards = [...document.querySelectorAll('.blog-card')];

    function highlight(text, kw) {
        const re = new RegExp(`(${kw.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
        return text.replace(re, '<mark>$1</mark>');
    }

    function renderDropdown(kw) {
        const q = kw.trim().toLowerCase();
        if (!q) { dropdown.classList.remove('open'); dropdown.innerHTML = ''; return; }

        const matched = POSTS.filter(p => p.title.toLowerCase().includes(q)).slice(0, 6);
        dropdown.innerHTML = matched.length
            ? matched.map(p => `
                <a class="search-suggestion" href="${p.url}">
                    <svg viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    <span class="search-suggestion-title">${highlight(p.title, kw.trim())}</span>
                </a>`).join('')
            : '<div class="search-no-result">Không tìm thấy bài viết phù hợp</div>';

        dropdown.classList.add('open');
    }

    function filterCards(kw) {
        const q = kw.trim().toLowerCase();
        allCards.forEach(card => {
            const title = card.querySelector('.blog-card-title').textContent.toLowerCase();
            card.style.display = (!q || title.includes(q)) ? '' : 'none';
        });
    }

    input.addEventListener('input', () => {
        clearBtn.style.display = input.value ? 'block' : 'none';
        renderDropdown(input.value);
        filterCards(input.value);
    });

    clearBtn.addEventListener('click', () => {
        input.value = '';
        clearBtn.style.display = 'none';
        dropdown.classList.remove('open');
        dropdown.innerHTML = '';
        filterCards('');
        input.focus();
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('.blog-search-wrap')) dropdown.classList.remove('open');
    });

    input.addEventListener('keydown', e => {
        if (e.key === 'Escape') { dropdown.classList.remove('open'); input.blur(); }
    });
})();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>