<?php
/**
 * products_ui.php
 * Nhận biến từ products_control.php:
 *   $cat         string     danh mục hiện tại
 *   $sort        string     kiểu sắp xếp
 *   $price_min   int
 *   $price_max   int
 *   $paged       Product[]  sản phẩm trang hiện tại
 *   $total       int        tổng sản phẩm
 *   $total_pages int
 *   $page        int        trang hiện tại
 *   $categories  array      danh sách danh mục
 *   $detailProduct ?Product sản phẩm đang xem chi tiết
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
    --r:            14px;
    --r-sm:         8px;
    --shadow:       0 4px 20px rgba(90,122,90,0.10);
    --shadow-hover: 0 12px 40px rgba(90,122,90,0.18);
    --trans:        0.25s cubic-bezier(.4,0,.2,1);
}
*, *::before, *::after { box-sizing: border-box; margin:0; padding:0; }

/* ── PAGE ── */
.products-page {
    background: var(--cream);
    min-height: 100vh;
    font-family: 'DM Sans', sans-serif;
    color: var(--ink);
}

/* ── HERO BANNER ── */
.products-hero {
    background: linear-gradient(135deg, #3d5c3d 0%, #5a7a5a 50%, #7a9e6a 100%);
    padding: 60px 0 50px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.products-hero::before {
    content: '🐾';
    position: absolute; font-size: 200px; opacity: 0.06;
    top: -30px; right: -20px;
}
.products-hero h1 {
    font-family: 'Fraunces', serif;
    font-size: clamp(2rem, 5vw, 3.2rem);
    color: var(--white);
    letter-spacing: -0.5px;
}
.products-hero p {
    color: rgba(255,255,255,.72);
    font-size: 1rem; margin-top: 8px;
    font-weight: 300;
}
.products-hero .result-badge {
    display: inline-block;
    margin-top: 14px;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    color: var(--white);
    padding: 5px 18px;
    border-radius: 99px;
    font-size: .82rem;
    font-weight: 500;
    backdrop-filter: blur(6px);
}

/* ── LAYOUT ── */
.products-layout {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 28px;
    max-width: 1300px;
    margin: 0 auto;
    padding: 36px 24px 64px;
}
@media(max-width:900px){
    .products-layout { grid-template-columns: 1fr; }
    .sidebar-filters { display:none; }
    .mobile-filter-toggle { display:flex !important; }
}

/* ── SIDEBAR ── */
.sidebar-filters {
    position: sticky; top: 80px;
    align-self: start;
}
.filter-card {
    background: var(--white);
    border-radius: var(--r);
    padding: 24px 20px;
    box-shadow: var(--shadow);
    border: 1px solid var(--stone);
    margin-bottom: 16px;
}
.filter-card h3 {
    font-family: 'Fraunces', serif;
    font-size: 1rem;
    color: var(--sage);
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--stone);
    display:flex; align-items:center; gap:8px;
}

/* Category nav */
.cat-nav { list-style: none; display:flex; flex-direction:column; gap:4px; }
.cat-nav a {
    display: flex; align-items:center; gap:10px;
    padding: 9px 12px;
    border-radius: var(--r-sm);
    text-decoration: none;
    color: var(--muted);
    font-size: .88rem;
    font-weight: 500;
    transition: background var(--trans), color var(--trans);
}
.cat-nav a:hover  { background: var(--sage-pale); color: var(--sage); }
.cat-nav a.active { background: var(--sage); color: var(--white); }
.cat-nav .count {
    margin-left:auto;
    background: rgba(0,0,0,.07);
    padding: 1px 8px; border-radius:99px;
    font-size:.75rem;
}
.cat-nav a.active .count { background: rgba(255,255,255,.2); }

/* Sort */
.sort-select {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid var(--stone);
    border-radius: var(--r-sm);
    font-family: 'DM Sans', sans-serif;
    font-size: .88rem;
    color: var(--ink);
    background: var(--cream);
    outline: none;
    cursor: pointer;
    transition: border-color var(--trans);
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237a7267' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
}
.sort-select:focus { border-color: var(--sage-light); }

/* Price range */
.price-inputs {
    display: flex;
    gap: 8px;
    align-items: center;
    margin-top: 12px;
    width: 100%;
    overflow: hidden;
}
.price-inputs input {
    flex: 1;
    padding: 9px 10px;
    border: 1.5px solid var(--stone);
    border-radius: var(--r-sm);
    font-family: inherit; font-size: .85rem;
    color: var(--ink); background: var(--cream);
    outline: none; text-align: center;
    transition: border-color var(--trans);
    min-width: 0; width: 0;
}
.price-inputs input:focus { border-color: var(--sage-light); }
.price-inputs span { color: var(--muted); font-size:.8rem; }
.apply-price-btn {
    width:100%; margin-top:12px;
    padding:10px; border:none;
    background: var(--sage); color: var(--white);
    border-radius: var(--r-sm); cursor:pointer;
    font-family: 'DM Sans', sans-serif;
    font-size:.88rem; font-weight:600;
    transition: background var(--trans);
}
.apply-price-btn:hover { background: var(--sage-light); }
.reset-link {
    display:block; text-align:center;
    margin-top:10px; font-size:.8rem;
    color: var(--muted); text-decoration:none;
}
.reset-link:hover { color: var(--clay); }

/* ── MAIN CONTENT ── */
.products-topbar {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:20px; gap:12px; flex-wrap:wrap;
}
.topbar-info { font-size:.88rem; color: var(--muted); }
.topbar-info strong { color: var(--sage); }

.mobile-filter-toggle {
    display:none;
    align-items:center; gap:8px;
    padding:10px 18px;
    background: var(--white); border: 1.5px solid var(--stone);
    border-radius: var(--r-sm); cursor:pointer;
    font-family: inherit; font-size:.88rem; font-weight:600;
    color: var(--ink);
}

/* ── PRODUCT GRID ── */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
    gap: 20px;
}
.product-card {
    background: var(--white);
    border-radius: var(--r);
    overflow: hidden;
    border: 1px solid var(--stone);
    box-shadow: var(--shadow);
    transition: transform var(--trans), box-shadow var(--trans);
    display: flex; flex-direction: column;
    animation: cardIn .4s ease both;
}
@keyframes cardIn {
    from { opacity:0; transform:translateY(16px); }
    to   { opacity:1; transform:translateY(0); }
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}
.card-img-wrap {
    position: relative; overflow: hidden;
    height: 200px; background: var(--sage-pale);
}
.card-img-wrap img {
    width:100%; height:100%; object-fit:cover;
    transition: transform .4s ease;
}
.product-card:hover .card-img-wrap img { transform: scale(1.06); }
.card-cat-badge {
    position:absolute; top:10px; left:10px;
    background: rgba(255,255,255,.92);
    backdrop-filter: blur(4px);
    padding: 3px 10px; border-radius:99px;
    font-size:.72rem; font-weight:600;
    color: var(--sage);
}
.card-stock {
    position:absolute; top:10px; right:10px;
    background: rgba(193,127,79,.9);
    color:#fff; padding:3px 10px;
    border-radius:99px; font-size:.72rem; font-weight:600;
}
.card-stock.low { background: rgba(220,50,50,.9); }
.card-body {
    padding: 16px;
    flex:1; display:flex; flex-direction:column; gap:6px;
}
.card-title {
    font-family: 'Fraunces', serif;
    font-size: .97rem; line-height:1.3; color: var(--ink);
    display:-webkit-box;
    -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
}
.card-desc {
    font-size:.78rem; color: var(--muted); line-height:1.5;
    display:-webkit-box;
    -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
    flex:1;
}
.card-footer {
    display:flex; align-items:center; justify-content:space-between;
    gap:10px; flex-wrap:wrap;
    margin-top:8px; padding-top:12px; border-top:1px solid var(--stone);
}
.card-price {
    font-family:'Fraunces',serif;
    font-size:1.1rem; font-weight:700; color: var(--clay);
}
.card-actions {
    display:flex; align-items:center; gap:8px;
}
.add-cart-btn,
.detail-btn {
    display:flex; align-items:center; gap:6px;
    padding: 8px 11px;
    background: var(--sage); color: var(--white);
    border: none; border-radius: var(--r-sm);
    font-family:'DM Sans',sans-serif;
    font-size:.8rem; font-weight:600;
    cursor:pointer; text-decoration:none;
    transition: background var(--trans), transform var(--trans);
}
.add-cart-btn:hover { background: var(--sage-light); transform:scale(1.04); }
.detail-btn {
    background: var(--cream); color: var(--sage);
    border:1px solid var(--stone);
}
.detail-btn:hover { background: var(--sage-pale); transform:scale(1.04); }
.add-cart-btn svg,
.detail-btn svg { width:14px; height:14px; stroke:currentColor; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }

/* Product detail modal */
.product-detail-overlay {
    position: fixed; inset: 0; z-index: 1000;
    display:flex; align-items:center; justify-content:center;
    padding:24px; background: rgba(30,30,30,.56);
}
.product-detail-dialog {
    width:min(920px, 100%);
    max-height:90vh; overflow:auto;
    background: var(--white);
    border-radius: var(--r);
    box-shadow: 0 24px 70px rgba(0,0,0,.28);
    border:1px solid rgba(255,255,255,.5);
}
.detail-layout {
    display:grid;
    grid-template-columns: minmax(260px, 360px) 1fr;
    gap:26px;
    padding:24px;
}
.detail-image {
    background: var(--sage-pale);
    border-radius: var(--r-sm);
    overflow:hidden;
    aspect-ratio: 1 / 1;
}
.detail-image img {
    width:100%; height:100%; object-fit:cover; display:block;
}
.detail-content {
    display:flex; flex-direction:column; gap:14px;
}
.detail-kicker {
    color: var(--sage);
    font-size:.78rem;
    font-weight:700;
    text-transform:uppercase;
}
.detail-title {
    font-family:'Fraunces',serif;
    font-size:clamp(1.5rem, 3vw, 2.25rem);
    line-height:1.15;
}
.detail-price {
    font-family:'Fraunces',serif;
    color:var(--clay);
    font-size:1.55rem;
}
.detail-desc {
    color:var(--muted);
    line-height:1.65;
}
.detail-info-grid {
    display:grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap:10px;
}
.detail-info-item {
    border:1px solid var(--stone);
    border-radius:var(--r-sm);
    padding:10px 12px;
    background:var(--cream);
    min-width:0;
}
.detail-info-item span {
    display:block;
    color:var(--muted);
    font-size:.76rem;
    margin-bottom:4px;
}
.detail-info-item strong {
    display:block;
    color:var(--ink);
    font-size:.92rem;
    overflow-wrap:anywhere;
}
.detail-actions {
    display:flex; gap:10px; flex-wrap:wrap;
    padding-top:8px;
}
.detail-close-btn {
    display:flex; align-items:center; justify-content:center;
    padding:10px 16px;
    border:1px solid var(--stone);
    border-radius:var(--r-sm);
    color:var(--muted);
    background:var(--white);
    font-weight:700;
    font-size:.9rem;
    text-decoration:none;
}
.detail-close-btn:hover { color:var(--ink); background:var(--cream); }
.detail-add-btn {
    display:flex; align-items:center; justify-content:center; gap:8px;
    padding:10px 16px;
    border-radius:var(--r-sm);
    color:var(--white);
    background:var(--sage);
    font-weight:700;
    font-size:.9rem;
    text-decoration:none;
}
.detail-add-btn:hover { background:var(--sage-light); }
@media(max-width:720px){
    .product-detail-overlay { align-items:flex-start; padding:14px; }
    .detail-layout { grid-template-columns:1fr; padding:16px; }
    .detail-info-grid { grid-template-columns:1fr; }
}

/* ── EMPTY STATE ── */
.empty-state { text-align:center; padding:80px 20px; color: var(--muted); }
.empty-state .emoji { font-size:4rem; }
.empty-state h3 { font-family:'Fraunces',serif; font-size:1.3rem; margin:12px 0 6px; color:var(--ink); }

/* ── PAGINATION ── */
.pagination-wrap {
    display:flex; justify-content:center; align-items:center;
    gap:6px; margin-top:36px; flex-wrap:wrap;
}
.page-btn {
    width:38px; height:38px;
    display:flex; align-items:center; justify-content:center;
    border-radius: var(--r-sm);
    border: 1.5px solid var(--stone);
    background: var(--white); color: var(--muted);
    font-family:'DM Sans',sans-serif; font-size:.88rem; font-weight:600;
    text-decoration:none;
    transition: background var(--trans), border-color var(--trans), color var(--trans);
}
.page-btn:hover { background: var(--sage-pale); border-color: var(--sage-light); color: var(--sage); }
.page-btn.active { background: var(--sage); border-color: var(--sage); color: var(--white); }
.page-btn.disabled { opacity:.4; pointer-events:none; }
.page-btn.wide { width:auto; padding:0 14px; }
</style>

<div class="products-page">

    <!-- HERO -->
    <div class="products-hero">
        <h1>🛍️ Cửa Hàng Thú Cưng</h1>
        <p>Sản phẩm chất lượng cao cho người bạn đồng hành của bạn</p>
        <span class="result-badge">Tìm thấy <?= $total ?> sản phẩm</span>
    </div>

    <div class="products-layout">

        <!-- SIDEBAR -->
        <aside class="sidebar-filters">

            <!-- Danh mục -->
            <div class="filter-card">
                <h3><span>📂</span> Danh mục</h3>
                <ul class="cat-nav">
                    <?php foreach ($categories as $key => $info): ?>
                    <li>
                        <a href="<?= ProductService::buildUrl(['cat' => $key, 'page' => 1]) ?>"
                           class="<?= $cat === $key ? 'active' : '' ?>">
                            <span><?= $info['emoji'] ?></span>
                            <?= $info['label'] ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Sắp xếp -->
            <div class="filter-card">
                <h3><span>↕️</span> Sắp xếp</h3>
                <form method="GET" action="products.php">
                    <?php foreach ($_GET as $k => $v): if ($k === 'sort') continue; ?>
                        <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
                    <?php endforeach; ?>
                    <select name="sort" class="sort-select" onchange="this.form.submit()">
                        <option value="default"    <?= $sort === 'default'    ? 'selected' : '' ?>>Mặc định</option>
                        <option value="name_az"    <?= $sort === 'name_az'    ? 'selected' : '' ?>>Tên A → Z</option>
                        <option value="name_za"    <?= $sort === 'name_za'    ? 'selected' : '' ?>>Tên Z → A</option>
                        <option value="price_asc"  <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Giá tăng dần</option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                    </select>
                </form>
            </div>

            <!-- Lọc giá -->
            <div class="filter-card">
                <h3><span>💰</span> Lọc giá</h3>
                <form method="GET" action="products.php">
                    <?php foreach ($_GET as $k => $v): if (in_array($k, ['price_min', 'price_max', 'page'])) continue; ?>
                        <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars($v) ?>">
                    <?php endforeach; ?>
                    <input type="hidden" name="page" value="1">
                    <div class="price-inputs">
                        <input type="number" name="price_min" placeholder="Từ"
                               value="<?= $price_min > 0 ? $price_min : '' ?>" min="0" step="10000">
                        <span>—</span>
                        <input type="number" name="price_max" placeholder="Đến"
                               value="<?= $price_max < 9999999 ? $price_max : '' ?>" min="0" step="10000">
                    </div>
                    <button type="submit" class="apply-price-btn">Áp dụng</button>
                    <a href="<?= ProductService::buildUrl(['price_min' => null, 'price_max' => null, 'page' => 1]) ?>"
                       class="reset-link">✕ Xoá bộ lọc giá</a>
                </form>
            </div>

        </aside>

        <!-- MAIN -->
        <main class="products-main">
            <div class="products-topbar">
                <span class="topbar-info">
                    Hiển thị <strong><?= count($paged) ?></strong> / <?= $total ?> sản phẩm
                    <?php if ($cat !== 'all'): ?>
                        · <strong><?= $categories[$cat]['label'] ?? $cat ?></strong>
                    <?php endif; ?>
                </span>
                <button class="mobile-filter-toggle">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="4" y1="6" x2="20" y2="6"/>
                        <line x1="8" y1="12" x2="16" y2="12"/>
                        <line x1="11" y1="18" x2="13" y2="18"/>
                    </svg>
                    Bộ lọc
                </button>
            </div>

            <?php if (empty($paged)): ?>
                <div class="empty-state">
                    <div class="emoji">🔍</div>
                    <h3>Không tìm thấy sản phẩm</h3>
                    <p>Thử thay đổi bộ lọc hoặc <a href="products.php" style="color:var(--sage)">xem tất cả</a></p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($paged as $i => $p):
                        $cat_label = $categories[$p->cat]['label'] ?? $p->cat;
                    ?>
                    <div class="product-card" style="animation-delay:<?= $i * 0.05 ?>s">
                        <div class="card-img-wrap">
                            <img src="assets/images/<?= htmlspecialchars($p->img) ?>"
                                 alt="<?= htmlspecialchars($p->name) ?>"
                                 onerror="this.src='assets/images/placeholder.jpg'">
                            <span class="card-cat-badge"><?= $cat_label ?></span>
                            <?php if ($p->inStock()): ?>
                                <span class="card-stock <?= $p->isLowStock() ? 'low' : '' ?>">
                                    <?= $p->isLowStock() ? 'Sắp hết' : 'Còn hàng' ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($p->name) ?></h5>
                            <p class="card-desc"><?= htmlspecialchars($p->desc) ?></p>
                            <div class="card-footer">
                                <span class="card-price"><?= number_format($p->price) ?>đ</span>
                                <div class="card-actions">
                                    <a href="<?= ProductService::buildUrl(['detail' => $p->id]) ?>" class="detail-btn">
                                        <svg viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path d="M12 16v-4"/>
                                            <path d="M12 8h.01"/>
                                        </svg>
                                        Chi tiết
                                    </a>
                                    <a href="shopcard.php?add=<?= $p->id ?>" class="add-cart-btn">
                                        <svg viewBox="0 0 24 24">
                                            <circle cx="9" cy="21" r="1"/>
                                            <circle cx="20" cy="21" r="1"/>
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                        </svg>
                                        Thêm
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- PAGINATION -->
                <?php if ($total_pages > 1): ?>
                <nav class="pagination-wrap">
                    <a href="<?= ProductService::buildUrl(['page' => $page - 1]) ?>"
                       class="page-btn wide <?= $page <= 1 ? 'disabled' : '' ?>">← Trước</a>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="<?= ProductService::buildUrl(['page' => $i]) ?>"
                           class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <a href="<?= ProductService::buildUrl(['page' => $page + 1]) ?>"
                       class="page-btn wide <?= $page >= $total_pages ? 'disabled' : '' ?>">Sau →</a>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>

    <?php if ($detailProduct): ?>
        <?php
            $detail_cat_label = $categories[$detailProduct->cat]['label'] ?? $detailProduct->cat;
            $detail_stock_text = $detailProduct->inStock() ? $detailProduct->so_luong . ' sản phẩm' : 'Hết hàng';
        ?>
        <div class="product-detail-overlay" role="dialog" aria-modal="true" aria-labelledby="product-detail-title">
            <div class="product-detail-dialog">
                <div class="detail-layout">
                    <div class="detail-image">
                        <img src="assets/images/<?= htmlspecialchars($detailProduct->img) ?>"
                             alt="<?= htmlspecialchars($detailProduct->name) ?>"
                             onerror="this.src='assets/images/placeholder.jpg'">
                    </div>
                    <div class="detail-content">
                        <div class="detail-kicker">Mã sản phẩm #<?= $detailProduct->id ?></div>
                        <h2 class="detail-title" id="product-detail-title"><?= htmlspecialchars($detailProduct->name) ?></h2>
                        <div class="detail-price"><?= number_format($detailProduct->price) ?>đ</div>
                        <p class="detail-desc"><?= nl2br(htmlspecialchars($detailProduct->desc ?: 'Chưa có mô tả.')) ?></p>

                        <div class="detail-info-grid">
                            <div class="detail-info-item">
                                <span>Loại thú cưng</span>
                                <strong><?= htmlspecialchars($detailProduct->loai_name ?: 'Chưa phân loại') ?></strong>
                            </div>
                            <div class="detail-info-item">
                                <span>Mã loại</span>
                                <strong><?= $detailProduct->loai_id ?: 'N/A' ?></strong>
                            </div>
                            <div class="detail-info-item">
                                <span>Danh mục</span>
                                <strong><?= htmlspecialchars($detail_cat_label) ?></strong>
                            </div>
                            <div class="detail-info-item">
                                <span>Mã danh mục</span>
                                <strong><?= htmlspecialchars($detailProduct->cat ?: 'other') ?></strong>
                            </div>
                            <div class="detail-info-item">
                                <span>Số lượng kho</span>
                                <strong><?= htmlspecialchars($detail_stock_text) ?></strong>
                            </div>
                            <div class="detail-info-item">
                                <span>Lượt mua</span>
                                <strong><?= number_format($detailProduct->luot_mua) ?></strong>
                            </div>
                        </div>

                        <div class="detail-actions">
                            <a href="<?= ProductService::buildUrl(['detail' => null]) ?>" class="detail-close-btn">Thoát</a>
                            <a href="shopcard.php?add=<?= $detailProduct->id ?>" class="detail-add-btn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="9" cy="21" r="1"/>
                                    <circle cx="20" cy="21" r="1"/>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                </svg>
                                Thêm vào giỏ hàng
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
