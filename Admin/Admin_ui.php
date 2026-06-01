<?php
// ============================================================
// Nhận $data từ Controller, render ra HTML thuần.
// Flow: [UI] ← Controller ← Service ← DAO ← DB
// ============================================================

// ── Giải nén $data ra biến cục bộ ────────────────────────────
$tab          = $data['tab']          ?? 'dashboard';
$page         = $data['page']         ?? 1;
$pages        = $data['pages']        ?? 1;
$search       = $data['search']       ?? '';
$year_filter  = $data['year_filter']  ?? date('Y');
$loai_filter  = $data['loai_filter']  ?? '';
$msg          = $data['msg']          ?? '';
$msg_type     = $data['msg_type']     ?? 'success';
$loai_list    = $data['loai_list']    ?? [];

// Stats chung (sidebar badge + dashboard)
$total_lich    = $data['total_lich']    ?? 0;
$total_dh_cho  = $data['total_dh_cho']  ?? 0;
$total_dh_hoan = $data['total_dh_hoan'] ?? 0;
$total_revenue = $data['total_revenue'] ?? 0;
$total_sp      = $data['total_sp']      ?? 0;
$total_bv      = $data['total_bv']      ?? 0;

// Dữ liệu theo từng tab
$lich_cho       = $data['lich_cho']       ?? [];
$rows           = $data['rows']           ?? [];   // lichsu / donhang / sanpham / baiviet
$articles_full  = $data['articles_full']  ?? [];   // baiviet full content
$don_hang_recent= $data['don_hang_recent']?? [];   // dashboard

// Thống kê / charts
$revenue_month  = $data['revenue_month']  ?? array_fill(1, 12, 0);
$revenue_year   = $data['revenue_year']   ?? [];
$orders_month   = $data['orders_month']   ?? array_fill(1, 12, 0);
$dv_stats       = $data['dv_stats']       ?? [];
$monthly_detail = $data['monthly_detail'] ?? [];
$total_rev_yr   = $data['total_rev_yr']   ?? 0;
$so_don_yr      = $data['so_don_yr']      ?? 0;
$avg_order      = $data['avg_order']      ?? 0;
$best_month     = $data['best_month']     ?? 1;
$rev_this_month = $data['rev_this_month'] ?? 0;

// ─────────────────────────────────────────────────────────────
// TAB TITLE MAP
// ─────────────────────────────────────────────────────────────
$tabTitles = [
    'dashboard' => '📊 Dashboard',
    'lich'      => '📅 Quản lý lịch hẹn',
    'lichsu'    => '🕐 Lịch sử đặt lịch',
    'donhang'   => '🛒 Quản lý đơn hàng',
    'sanpham'   => '📦 Quản lý sản phẩm',
    'baiviet'   => '📰 Quản lý bài viết',
    'thongke'   => '📈 Thống kê & Doanh thu',
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - PetShop</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
/* ── RESET & VARS ── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --sage:#5a7a5a; --sage-l:#7fa87f; --sage-pale:#edf4ed;
    --clay:#c17f4f; --cream:#faf7f2; --stone:#e8e0d5;
    --ink:#1e1e1e;  --muted:#7a7267; --white:#ffffff;
    --red:#e74c3c;  --yellow:#f39c12; --blue:#2980b9; --green:#27ae60;
    --sidebar-w:240px; --r:12px; --shadow:0 4px 20px rgba(90,122,90,.10);
}
body { font-family:'DM Sans',sans-serif; background:var(--cream); color:var(--ink); display:flex; min-height:100vh; }

/* ── SIDEBAR ── */
.admin-sidebar { width:var(--sidebar-w); background:linear-gradient(180deg,#2d4a2d,#3d5c3d 60%,#4a6e4a); min-height:100vh; position:fixed; top:0; left:0; display:flex; flex-direction:column; z-index:100; box-shadow:4px 0 20px rgba(0,0,0,.15); }
.sidebar-brand { padding:28px 20px 20px; border-bottom:1px solid rgba(255,255,255,.1); }
.sidebar-brand h2 { font-family:'Fraunces',serif; font-size:1.3rem; color:#fff; }
.sidebar-brand span { font-size:.78rem; color:rgba(255,255,255,.55); }
.sidebar-nav { padding:16px 0; flex:1; overflow-y:auto; }
.nav-section-title { padding:12px 20px 4px; font-size:.68rem; font-weight:700; letter-spacing:1px; color:rgba(255,255,255,.35); text-transform:uppercase; }
.sidebar-nav a { display:flex; align-items:center; gap:10px; padding:11px 20px; color:rgba(255,255,255,.75); text-decoration:none; font-size:.88rem; font-weight:500; transition:background .2s,color .2s; border-left:3px solid transparent; }
.sidebar-nav a:hover { background:rgba(255,255,255,.08); color:#fff; }
.sidebar-nav a.active { background:rgba(255,255,255,.14); color:#fff; border-left-color:#a8d5a2; }
.sidebar-nav .icon { font-size:1rem; width:20px; text-align:center; }
.sidebar-footer { padding:16px 20px; border-top:1px solid rgba(255,255,255,.1); }
.sidebar-footer a { display:flex; align-items:center; gap:8px; color:rgba(255,255,255,.6); text-decoration:none; font-size:.83rem; transition:color .2s; }
.sidebar-footer a:hover { color:#fff; }

/* ── MAIN LAYOUT ── */
.admin-main { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; min-height:100vh; }
.admin-topbar { background:var(--white); border-bottom:1px solid var(--stone); padding:0 32px; height:60px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:50; box-shadow:0 2px 10px rgba(90,122,90,.06); }
.topbar-title { font-family:'Fraunces',serif; font-size:1.1rem; color:var(--sage); }
.topbar-user { display:flex; align-items:center; gap:10px; font-size:.85rem; color:var(--muted); }
.topbar-avatar { width:34px; height:34px; background:var(--sage); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:.85rem; }
.admin-content { padding:28px 32px; flex:1; }

/* ── TOAST ── */
.toast-msg { padding:13px 20px; border-radius:var(--r); margin-bottom:22px; font-weight:500; font-size:.9rem; display:flex; align-items:center; gap:10px; animation:slideDown .3s ease; }
@keyframes slideDown { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }
.toast-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
.toast-warning { background:#fff3cd; color:#856404; border:1px solid #ffeeba; }
.toast-error   { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }

/* ── STAT CARDS ── */
.stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:18px; margin-bottom:28px; }
.stat-card { background:var(--white); border-radius:var(--r); padding:22px 24px; border:1px solid var(--stone); box-shadow:var(--shadow); display:flex; align-items:center; gap:16px; }
.stat-icon { width:52px; height:52px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0; }
.stat-num { font-family:'Fraunces',serif; font-size:1.9rem; font-weight:700; color:var(--ink); line-height:1; }
.stat-label { font-size:.8rem; color:var(--muted); margin-top:4px; }

/* ── CARD ── */
.card { background:var(--white); border-radius:var(--r); border:1px solid var(--stone); box-shadow:var(--shadow); margin-bottom:24px; overflow:hidden; }
.card-header { padding:18px 22px; border-bottom:1px solid var(--stone); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
.card-header h3 { font-family:'Fraunces',serif; font-size:1rem; color:var(--sage); }
.card-body { padding:20px 22px; }

/* ── TABLE ── */
.data-table { width:100%; border-collapse:collapse; font-size:.875rem; }
.data-table th { padding:10px 12px; background:var(--cream); text-align:left; color:var(--muted); font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; border-bottom:2px solid var(--stone); }
.data-table td { padding:12px; border-bottom:1px solid #f4f0ea; vertical-align:middle; }
.data-table tr:last-child td { border-bottom:none; }
.data-table tr:hover td { background:#fdf9f5; }

/* ── BADGES ── */
.badge { display:inline-block; padding:3px 10px; border-radius:99px; font-size:.74rem; font-weight:700; letter-spacing:.3px; }
.badge-pending { background:#fff3cd; color:#856404; }
.badge-done    { background:#d4edda; color:#155724; }
.badge-cancel  { background:#f8d7da; color:#721c24; }

/* ── BUTTONS ── */
.btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px; border:none; cursor:pointer; font-family:'DM Sans',sans-serif; font-size:.83rem; font-weight:600; text-decoration:none; transition:all .2s; white-space:nowrap; }
.btn-sage   { background:var(--sage); color:#fff; }      .btn-sage:hover   { background:var(--sage-l); }
.btn-red    { background:var(--red);  color:#fff; }      .btn-red:hover    { background:#c0392b; }
.btn-yellow { background:var(--yellow);color:#fff; }     .btn-yellow:hover { background:#d68910; }
.btn-outline{ background:transparent; border:1.5px solid var(--stone); color:var(--muted); }
.btn-outline:hover { border-color:var(--sage); color:var(--sage); background:var(--sage-pale); }
.btn-sm { padding:5px 11px; font-size:.78rem; }

/* ── FORMS ── */
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px 22px; }
.form-row.triple { grid-template-columns:1fr 1fr 1fr; }
.form-group { margin-bottom:14px; }
.form-group label { display:block; font-size:.82rem; font-weight:600; margin-bottom:5px; color:#555; }
.form-group input, .form-group select, .form-group textarea { width:100%; padding:9px 13px; border:1.5px solid var(--stone); border-radius:8px; font-family:'DM Sans',sans-serif; font-size:.875rem; background:var(--cream); color:var(--ink); transition:border-color .2s; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--sage-l); outline:none; background:#fff; }
.form-group textarea { resize:vertical; min-height:90px; }
.img-preview { width:90px; height:90px; object-fit:cover; border-radius:8px; border:1px solid var(--stone); margin-top:6px; display:block; }

/* ── SEARCH BAR ── */
.search-bar { display:flex; gap:8px; }
.search-bar input { flex:1; padding:8px 14px; border:1.5px solid var(--stone); border-radius:8px; font-family:inherit; font-size:.875rem; background:var(--cream); }
.search-bar input:focus { border-color:var(--sage-l); outline:none; }

/* ── CHARTS ── */
.chart-grid { display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-bottom:24px; }
.chart-wrap    { position:relative; height:300px; }
.chart-wrap-sm { position:relative; height:300px; }
.year-filter { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
.year-filter select { padding:7px 12px; border:1.5px solid var(--stone); border-radius:8px; font-family:inherit; font-size:.83rem; background:var(--cream); }

/* ── PRODUCT GRID ── */
.product-admin-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(190px,1fr)); gap:16px; }
.product-admin-card { border:1px solid var(--stone); border-radius:var(--r); overflow:hidden; background:var(--white); transition:box-shadow .2s; }
.product-admin-card:hover { box-shadow:var(--shadow); }
.product-admin-card img { width:100%; height:140px; object-fit:cover; background:var(--sage-pale); }
.product-admin-card .pad { padding:12px; }
.product-admin-card .name { font-weight:600; font-size:.85rem; margin-bottom:4px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.product-admin-card .price { color:var(--clay); font-weight:700; font-size:.92rem; }
.product-admin-card .actions { display:flex; gap:6px; margin-top:10px; }

/* ── ARTICLE LIST ── */
.article-list { display:flex; flex-direction:column; gap:12px; }
.article-item { display:flex; align-items:center; gap:14px; padding:14px 16px; border:1px solid var(--stone); border-radius:var(--r); background:var(--white); transition:box-shadow .2s; }
.article-item:hover { box-shadow:var(--shadow); }
.article-item img { width:72px; height:60px; object-fit:cover; border-radius:8px; border:1px solid var(--stone); background:var(--sage-pale); flex-shrink:0; }
.article-item .info { flex:1; }
.article-item .title { font-weight:600; font-size:.9rem; margin-bottom:4px; }
.article-item .date { font-size:.77rem; color:var(--muted); }
.article-item .actions { display:flex; gap:6px; flex-shrink:0; }

/* ── PAGINATION ── */
.pagination { display:flex; gap:6px; justify-content:center; margin:20px 0; }
.pagination a, .pagination span { display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:8px; border:1.5px solid var(--stone); text-decoration:none; font-size:.83rem; font-weight:600; color:var(--muted); transition:all .2s; }
.pagination a:hover { border-color:var(--sage); color:var(--sage); }
.pagination .active { background:var(--sage); color:#fff; border-color:var(--sage); }

/* ── MODAL ── */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:900; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box { background:var(--white); border-radius:16px; padding:32px; width:90%; max-width:680px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.25); animation:popIn .25s ease; }
@keyframes popIn { from{transform:scale(.93);opacity:0} to{transform:scale(1);opacity:1} }
.modal-box h3 { font-family:'Fraunces',serif; font-size:1.1rem; color:var(--sage); margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid var(--stone); }
.modal-footer { margin-top:20px; display:flex; gap:10px; justify-content:flex-end; }

/* ── CONFIRM MODAL ── */
.confirm-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:999; align-items:center; justify-content:center; }
.confirm-overlay.open { display:flex; }
.confirm-box { background:#fff; border-radius:16px; padding:36px 32px; max-width:400px; width:90%; text-align:center; box-shadow:0 16px 50px rgba(0,0,0,.2); animation:popIn .25s ease; }
.confirm-box .confirm-icon { font-size:3rem; margin-bottom:12px; }
.confirm-box h4 { font-size:1.1rem; font-weight:700; margin-bottom:8px; }
.confirm-box p  { color:var(--muted); font-size:.9rem; margin-bottom:22px; }
.confirm-actions { display:flex; gap:10px; justify-content:center; }

/* ── RESPONSIVE ── */
@media(max-width:1100px) { .stat-grid { grid-template-columns:repeat(2,1fr); } .chart-grid { grid-template-columns:1fr; } }
@media(max-width:768px)  { .admin-sidebar { transform:translateX(-100%); transition:transform .3s; } .admin-sidebar.open { transform:translateX(0); } .admin-main { margin-left:0; } .admin-content { padding:20px 16px; } .form-row { grid-template-columns:1fr; } .stat-grid { grid-template-columns:repeat(2,1fr); } }

/* ── AUTOCOMPLETE ── */
.bv-suggest-item { padding:10px 16px; font-size:.875rem; cursor:pointer; color:var(--ink); transition:background .15s; border-bottom:1px solid #f4f0ea; }
.bv-suggest-item:last-child { border-bottom:none; }
.bv-suggest-item:hover { background:var(--sage-pale); color:var(--sage); }
#bvDropdown { max-height: 250px; overflow-y: auto; } 
</style>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- CONFIRM MODAL                                             -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <div class="confirm-icon" id="confirmIcon">❓</div>
        <h4 id="confirmTitle">Xác nhận</h4>
        <p  id="confirmText">Bạn có chắc chắn không?</p>
        <div class="confirm-actions">
            <a id="confirmYes" href="#" class="btn btn-sage">✅ Có, xác nhận</a>
            <button class="btn btn-outline" onclick="closeConfirm()">❌ Hủy</button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- MODAL CHI TIẾT ĐƠN HÀNG                                  -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="orderDetailModal">
    <div class="modal-box" style="max-width:600px">
        <h3 id="orderDetailTitle">📋 Chi tiết đơn hàng</h3>
        <div id="orderDetailContent"></div>
        <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('orderDetailModal')">Đóng</button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- MODAL SẢN PHẨM                                           -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="productModal">
    <div class="modal-box">
        <h3 id="productModalTitle">➕ Thêm sản phẩm mới</h3>
        <form method="POST" action="admin.php?tab=sanpham" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="product_id" value="0">
            <div class="form-row">
                <div class="form-group"><label>Tên sản phẩm *</label><input type="text" name="ten_san_pham" id="inp_ten" required placeholder="Nhập tên sản phẩm"></div>
                <div class="form-group"><label>Giá (đ) *</label><input type="number" name="gia" id="inp_gia" required min="0" step="1000" placeholder="0"></div>
            </div>
            <div class="form-row triple">
                <div class="form-group">
                    <label>Loại thú cưng</label>
                    <select name="loai_id" id="inp_loai">
                        <?php foreach ($loai_list as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['ten_loai']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Danh mục</label>
                    <select name="danh_muc" id="inp_danh_muc">
                        <option value="dog-food">Thức ăn chó</option>
                        <option value="cat-food">Thức ăn mèo</option>
                        <option value="hamster-food">Thức ăn hamster</option>
                        <option value="dog-accessories">Phụ kiện chó</option>
                        <option value="cat-accessories">Phụ kiện mèo</option>
                        <option value="hamster-accessories">Phụ kiện hamster</option>
                    </select>
                </div>
                <div class="form-group"><label>Số lượng</label><input type="number" name="so_luong" id="inp_sl" min="0" value="100"></div>
            </div>
            <div class="form-group"><label>Mô tả</label><textarea name="mo_ta" id="inp_mota" rows="3" placeholder="Mô tả sản phẩm..."></textarea></div>
            <div class="form-group">
                <label>Hình ảnh</label>
                <input type="file" name="hinh_anh" accept="image/*" onchange="previewImg(this,'prev_product')">
                <img id="prev_product" class="img-preview" src="" style="display:none">
            </div>
            <div class="modal-footer">
                <button type="submit" name="save_product" class="btn btn-sage">💾 Lưu sản phẩm</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('productModal')">Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- MODAL BÀI VIẾT                                           -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="articleModal">
    <div class="modal-box" style="max-width:800px">
        <h3 id="articleModalTitle">📝 Thêm bài viết mới</h3>
        <form method="POST" action="admin.php?tab=baiviet" enctype="multipart/form-data">
            <input type="hidden" name="article_id" id="article_id" value="0">
            <?php for ($i = 1; $i <= 4; $i++): ?>
            <input type="hidden" name="old_hinh_anh_<?= $i ?>" id="old_img_<?= $i ?>" value="">
            <?php endfor; ?>
            <div class="form-group"><label>Tiêu đề bài viết *</label><input type="text" name="tieu_de" id="inp_tieu_de" required placeholder="Nhập tiêu đề..."></div>
            <?php
            $articleFields = [
                ['name'=>'noi_dung_chinh',       'id'=>'inp_nd1', 'label'=>'Nội dung chính',        'img'=>1],
                ['name'=>'nguyen_nhan_pho_bien',  'id'=>'inp_nd2', 'label'=>'Nguyên nhân phổ biến',  'img'=>2],
                ['name'=>'huong_dan',             'id'=>'inp_nd3', 'label'=>'Hướng dẫn',             'img'=>3],
                ['name'=>'cach_cham',             'id'=>'inp_nd4', 'label'=>'Cách chăm sóc',         'img'=>4],
            ];
            foreach ($articleFields as $f): ?>
            <div class="form-row">
                <div class="form-group"><label><?= $f['label'] ?></label><textarea name="<?= $f['name'] ?>" id="<?= $f['id'] ?>" rows="3"></textarea></div>
                <div class="form-group">
                    <label>Hình ảnh <?= $f['img'] ?></label>
                    <input type="file" name="hinh_anh_<?= $f['img'] ?>" accept="image/*" onchange="previewImg(this,'prev_img<?= $f['img'] ?>')">
                    <img id="prev_img<?= $f['img'] ?>" class="img-preview" src="" style="display:none">
                </div>
            </div>
            <?php endforeach; ?>
            <div class="form-group"><label>Email nhận thông báo</label><input type="email" name="email_nhan_thong_bao" id="inp_email_bv" placeholder="admin@petshop.vn"></div>
            <div class="modal-footer">
                <button type="submit" name="save_article" class="btn btn-sage">💾 Lưu bài viết</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('articleModal')">Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- LAYOUT CHÍNH                                              -->
<!-- ══════════════════════════════════════════════════════════ -->
<aside class="admin-sidebar" id="sidebar">
    <div class="sidebar-brand">
        <h2>🐾 PetShop</h2>
        <span>Bảng điều khiển Admin</span>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-title">Tổng quan</div>
        <a href="admin.php?tab=dashboard" class="<?= $tab==='dashboard'?'active':'' ?>">
            <span class="icon">📊</span> Dashboard
        </a>
        <div class="nav-section-title">Quản lý</div>
        <a href="admin.php?tab=lich" class="<?= $tab==='lich'?'active':'' ?>">
            <span class="icon">📅</span> Lịch hẹn
            <?php if ($total_lich > 0): ?>
            <span style="margin-left:auto;background:var(--red);color:#fff;border-radius:99px;padding:1px 8px;font-size:.7rem"><?= $total_lich ?></span>
            <?php endif; ?>
        </a>
        <a href="admin.php?tab=lichsu"  class="<?= $tab==='lichsu'?'active':'' ?>"><span class="icon">🕐</span> Lịch sử đặt lịch</a>
        <a href="admin.php?tab=donhang" class="<?= $tab==='donhang'?'active':'' ?>">
            <span class="icon">🛒</span> Đơn hàng
            <?php if ($total_dh_cho > 0): ?>
            <span style="margin-left:auto;background:var(--clay);color:#fff;border-radius:99px;padding:1px 8px;font-size:.7rem"><?= $total_dh_cho ?></span>
            <?php endif; ?>
        </a>
        <a href="admin.php?tab=sanpham" class="<?= $tab==='sanpham'?'active':'' ?>"><span class="icon">📦</span> Sản phẩm</a>
        <a href="admin.php?tab=baiviet" class="<?= $tab==='baiviet'?'active':'' ?>"><span class="icon">📰</span> Bài viết</a>
        <div class="nav-section-title">Báo cáo</div>
        <a href="admin.php?tab=thongke" class="<?= $tab==='thongke'?'active':'' ?>"><span class="icon">📈</span> Thống kê & Doanh thu</a>
    </nav>
    <div class="sidebar-footer">
        <a href="index.php">🏠 Về trang chủ</a>
    </div>
</aside>

<div class="admin-main">
    <div class="admin-topbar">
        <div class="topbar-title"><?= $tabTitles[$tab] ?? 'Admin' ?></div>
        <div class="topbar-user">
            <span>Xin chào, <strong>Admin</strong></span>
            <div class="topbar-avatar">A</div>
        </div>
    </div>

    <div class="admin-content">

        <?php if ($msg): ?>
        <div class="toast-msg toast-<?= $msg_type === 'warning' ? 'warning' : ($msg_type === 'error' ? 'error' : 'success') ?>">
            <?= htmlspecialchars($msg) ?>
        </div>
        <?php endif; ?>

        <?php /* ═══════════════════════════════════════════════
               DASHBOARD
        ════════════════════════════════════════════════════ */ ?>
        <?php if ($tab === 'dashboard'): ?>
        <div class="stat-grid">
            <div class="stat-card"><div class="stat-icon" style="background:#edf4ed">📅</div><div><div class="stat-num"><?= $total_lich ?></div><div class="stat-label">Lịch chờ xử lý</div></div></div>
            <div class="stat-card"><div class="stat-icon" style="background:#fff3cd">🛒</div><div><div class="stat-num"><?= $total_dh_cho ?></div><div class="stat-label">Đơn hàng chờ</div></div></div>
            <div class="stat-card"><div class="stat-icon" style="background:#fdf0e6">💰</div><div><div class="stat-num" style="font-size:1.3rem"><?= number_format($total_revenue,0,',','.') ?>đ</div><div class="stat-label">Tổng doanh thu</div></div></div>
            <div class="stat-card"><div class="stat-icon" style="background:#d4edda">✅</div><div><div class="stat-num"><?= $total_dh_hoan ?></div><div class="stat-label">Đơn hoàn thành</div></div></div>
        </div>
        <div class="chart-grid">
            <div class="card">
                <div class="card-header"><h3>💰 Doanh thu theo tháng (<?= date('Y') ?>)</h3></div>
                <div class="card-body"><div class="chart-wrap"><canvas id="dashChart"></canvas></div></div>
            </div>
            <div class="card">
                <div class="card-header"><h3>🔧 Dịch vụ phổ biến</h3></div>
                <div class="card-body"><div class="chart-wrap-sm"><canvas id="dvChart"></canvas></div></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3>🛒 Đơn hàng mới nhất</h3>
                <a href="admin.php?tab=donhang" class="btn btn-outline btn-sm">Xem tất cả →</a>
            </div>
            <div class="card-body" style="padding:0">
                <table class="data-table">
                    <thead><tr><th>#</th><th>Khách hàng</th><th>SĐT</th><th>Tổng tiền</th><th>Ngày đặt</th><th>Trạng thái</th></tr></thead>
                    <tbody>
                    <?php foreach (array_slice($don_hang_recent, 0, 6) as $dh): ?>
                    <tr>
                        <td style="color:var(--muted)">#<?= $dh['id'] ?></td>
                        <td><strong><?= htmlspecialchars($dh['ho_ten']) ?></strong></td>
                        <td><?= htmlspecialchars($dh['sdt']) ?></td>
                        <td style="font-weight:700;color:var(--clay)"><?= number_format($dh['tong_tien'],0,',','.') ?>đ</td>
                        <td style="font-size:.78rem"><?= date('d/m/Y H:i', strtotime($dh['ngay_dat'])) ?></td>
                        <?php if (isset($p)): ?>
<button class="btn btn-yellow btn-sm"
onclick='openEditProduct(<?= json_encode($p, JSON_HEX_APOS) ?>)'>
✏️ Sửa
</button>
<?php endif; ?>
<td>
<?php
$status = strtolower(trim($dh['trang_thai'] ?? ''));

$map = [
    'cho' => '⏳ Chờ xử lý',
    'xacnhan' => '✅ Đã xác nhận',
    'hoan_thanh' => '🎉 Hoàn thành',
    'huy' => '❌ Đã hủy'
];

echo $map[$status] ?? '❓ Không rõ';
?>
</td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($don_hang_recent)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:30px">Chưa có đơn hàng nào.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php /* ═══════════════════════════════════════════════
               LỊCH HẸN
        ════════════════════════════════════════════════════ */ ?>
        <?php elseif ($tab === 'lich'): ?>
        <div class="card">
            <div class="card-header">
                <h3>📅 Lịch hẹn đang xử lý (<?= $data['total'] ?? count($lich_cho) ?>)</h3>
                <form method="GET" action="admin.php" class="search-bar">
                    <input type="hidden" name="tab" value="lich">
                    <input type="date" name="search_lich" value="<?= htmlspecialchars($search) ?>" style="padding:8px 12px;border:1.5px solid var(--stone);border-radius:8px;font-family:inherit;font-size:.875rem;background:var(--cream)">
                    <button type="submit" class="btn btn-sage btn-sm">🔍 Lọc</button>
                    <?php if ($search): ?><a href="admin.php?tab=lich" class="btn btn-outline btn-sm">✕ Xóa lọc</a><?php endif; ?>
                </form>
            </div>
            <div class="card-body" style="padding:0">
                <table class="data-table">
                    <thead><tr><th>#</th><th>Họ tên</th><th>SĐT</th><th>Email</th><th>Loại thú cưng</th><th>Dịch vụ</th><th>Ngày hẹn</th><th>Giờ</th><th>Trạng thái</th><th>Ghi chú</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    <?php foreach ($lich_cho as $l): ?>
                    <?php
                        $lichStatus = $l['trang_thai'] ?? 'cho_xu_ly';
                        $isDue = !empty($l['ngay_hen']) && date('Y-m-d') >= date('Y-m-d', strtotime($l['ngay_hen']));
                    ?>
                    <tr>
                        <td style="color:var(--muted)"><?= $l['id'] ?></td>
                        <td><strong><?= htmlspecialchars($l['ho_ten']) ?></strong></td>
                        <td><?= htmlspecialchars($l['sdt']) ?></td>
                        <td style="font-size:.78rem"><?= htmlspecialchars($l['email'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($l['ten_loai'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($l['ten_dich_vu'] ?? '—') ?></td>
                        <td><?= date('d/m/Y', strtotime($l['ngay_hen'])) ?></td>
                        <td><?= substr($l['gio_hen'], 0, 5) ?></td>
                        <td>
                            <?php if ($lichStatus === 'cho_xu_ly'): ?>
                                <span class="badge badge-pending">Cho xu ly</span>
                            <?php elseif ($lichStatus === 'dang_cho_lich'): ?>
                                <span class="badge badge-done">Dang cho lich</span>
                            <?php else: ?>
                                <span class="badge badge-cancel"><?= htmlspecialchars($lichStatus) ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:.78rem;max-width:140px"><?= htmlspecialchars($l['ghi_chu'] ?? '—') ?></td>
                        <td>
                            <div style="display:flex;gap:6px;flex-wrap:wrap">
                                <?php if ($lichStatus === 'cho_xu_ly'): ?>
                                    <button class="btn btn-sage btn-sm" onclick="showConfirm('dong_y',<?= $l['id'] ?>,'<?= htmlspecialchars($l['ho_ten'],ENT_QUOTES) ?>')">Đồng ý</button>
                                <?php elseif ($lichStatus === 'dang_cho_lich'): ?>
                                    <?php if ($isDue): ?>
                                        <button class="btn btn-sage btn-sm" onclick="showConfirm('hoan_thanh',<?= $l['id'] ?>,'<?= htmlspecialchars($l['ho_ten'],ENT_QUOTES) ?>')">Hoàn thành</button>
                                    <?php else: ?>
                                        <button class="btn btn-outline btn-sm" disabled title="Chưa tới ngày hẹn">Chưa tới ngày</button>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <button class="btn btn-red btn-sm"  onclick="showConfirm('huy',<?= $l['id'] ?>,'<?= htmlspecialchars($l['ho_ten'],ENT_QUOTES) ?>')">🚫 Hủy</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($lich_cho)): ?>
                    <tr><td colspan="11" style="text-align:center;color:var(--muted);padding:50px">🎉 Không có lịch hẹn nào<?= $search ? ' trong ngày '.date('d/m/Y',strtotime($search)) : ' đang chờ' ?>!</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?= renderPagination($page, $pages, $tab, $search) ?>

        <?php /* ═══════════════════════════════════════════════
               LỊCH SỬ ĐẶT LỊCH
        ════════════════════════════════════════════════════ */ ?>
        <?php elseif ($tab === 'lichsu'): ?>
        <div class="card">
            <div class="card-header">
                <h3>🕐 Lịch sử đặt lịch (<?= $data['total'] ?? 0 ?>)</h3>
                <form method="GET" action="admin.php" class="search-bar">
                    <input type="hidden" name="tab" value="lichsu">
                    <input type="text" name="search_lichsu" placeholder="🔍 Tìm tên, SĐT, ngày..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-sage btn-sm">Tìm</button>
                    <?php if ($search): ?><a href="admin.php?tab=lichsu" class="btn btn-outline btn-sm">✕ Xóa</a><?php endif; ?>
                </form>
            </div>
            <div class="card-body" style="padding:0">
                <table class="data-table">
                    <thead><tr><th>#</th><th>Họ tên</th><th>SĐT</th><th>Dịch vụ</th><th>Ngày hẹn</th><th>Ngày đặt</th><th>Trạng thái</th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $l): ?>
                    <tr>
                        <td style="color:var(--muted)"><?= $l['id'] ?></td>
                        <td><strong><?= htmlspecialchars($l['ho_ten']) ?></strong></td>
                        <td><?= htmlspecialchars($l['sdt']) ?></td>
                        <td><?= htmlspecialchars($l['ten_dich_vu'] ?? '—') ?></td>
                        <td><?= date('d/m/Y', strtotime($l['ngay_hen'])) ?></td>
                        <td style="font-size:.78rem"><?= date('d/m/Y H:i', strtotime($l['ngay_tao'])) ?></td>
                        <td>
                            <?php if ($l['trang_thai'] === 'hoan_thanh'): ?>
                                <span class="badge badge-done">✅ Hoàn thành</span>
                            <?php else: ?>
                                <span class="badge badge-cancel">🚫 Đã hủy</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?>
                    <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:50px">Chưa có lịch sử nào.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?= renderPagination($page, $pages, $tab, $search) ?>

        <?php /* ═══════════════════════════════════════════════
               ĐƠN HÀNG
        ════════════════════════════════════════════════════ */ ?>
        <?php elseif ($tab === 'donhang'): ?>
        <div class="stat-grid" style="grid-template-columns:repeat(3,1fr)">
            <div class="stat-card"><div class="stat-icon" style="background:#fff3cd">🛒</div><div><div class="stat-num"><?= $total_dh_cho ?></div><div class="stat-label">Đơn chờ xử lý</div></div></div>
            <div class="stat-card"><div class="stat-icon" style="background:#d4edda">✅</div><div><div class="stat-num"><?= $total_dh_hoan ?></div><div class="stat-label">Đơn hoàn thành</div></div></div>
            <div class="stat-card"><div class="stat-icon" style="background:#fdf0e6">💰</div><div><div class="stat-num" style="font-size:1.2rem"><?= number_format($total_revenue,0,',','.') ?>đ</div><div class="stat-label">Tổng doanh thu</div></div></div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3>🛒 Tất cả đơn hàng (<?= $data['total'] ?? 0 ?>)</h3>
                <form method="GET" action="admin.php" class="search-bar">
                    <input type="hidden" name="tab" value="donhang">
                    <input type="text" name="search_donhang" placeholder="🔍 Tìm tên, SĐT..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-sage btn-sm">Tìm</button>
                    <?php if ($search): ?><a href="admin.php?tab=donhang" class="btn btn-outline btn-sm">✕ Xóa</a><?php endif; ?>
                </form>
            </div>
            <div class="card-body" style="padding:0">
                <table class="data-table">
                    <thead><tr><th>#</th><th>Khách hàng</th><th>SĐT</th><th>Tổng tiền</th><th>Thanh toán</th><th>Ngày đặt</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $dh): ?>
                    <tr>
                        <td style="color:var(--muted)">#<?= $dh['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($dh['ho_ten']) ?></strong><br>
                            <span style="font-size:.76rem;color:var(--muted)"><?= htmlspecialchars(mb_substr($dh['dia_chi'] ?? '', 0, 30)) ?></span>
                        </td>
                        <td><?= htmlspecialchars($dh['sdt']) ?></td>
                        <td style="font-weight:700;color:var(--clay)"><?= number_format($dh['tong_tien'],0,',','.') ?>đ</td>
                        <td>
                            <?php $isCod = strtoupper($dh['phuong_thuc_tt']) === 'COD'; ?>
                            <span class="badge" style="background:<?= $isCod?'#e8f4fd':'#edf4ed' ?>;color:<?= $isCod?'#1a5276':'#155724' ?>">
                                <?= $isCod ? '💵 COD' : '🏦 CK' ?>
                            </span>
                        </td>
                        <td style="font-size:.78rem"><?= date('d/m/Y H:i', strtotime($dh['ngay_dat'])) ?></td>
                        <td>
                            <?php if ($dh['trang_thai'] === 'cho_xu_ly'): ?>
                                <span class="badge badge-pending">⏳ Chờ xử lý</span>
                            <?php elseif ($dh['trang_thai'] === 'hoan_thanh'): ?>
                                <span class="badge badge-done">✅ Hoàn thành</span>
                            <?php else: ?>
                                <span class="badge badge-cancel">🚫 Đã hủy</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex;gap:5px;flex-wrap:wrap">
                                <button class="btn btn-outline btn-sm" onclick="viewOrderDetail(<?= $dh['id'] ?>)">👁️ Chi tiết</button>
                                <?php if ($dh['trang_thai'] === 'cho_xu_ly'): ?>
                                <a href="admin.php?tab=donhang&action_dh=hoan_thanh&dh_id=<?= $dh['id'] ?>" class="btn btn-sage btn-sm" onclick="return confirm('Xác nhận hoàn thành?')">✅</a>
                                <a href="admin.php?tab=donhang&action_dh=huy&dh_id=<?= $dh['id'] ?>"         class="btn btn-red btn-sm"  onclick="return confirm('Xác nhận hủy?')">🚫</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?>
                    <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:50px">Chưa có đơn hàng nào.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?= renderPagination($page, $pages, $tab, $search) ?>

        <?php /* ═══════════════════════════════════════════════
               SẢN PHẨM
        ════════════════════════════════════════════════════ */ ?>
        <?php elseif ($tab === 'sanpham'): ?>
        <div class="card">
            <div class="card-header">
                <h3>📦 Sản phẩm (<?= $data['total'] ?? 0 ?>)</h3>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                    <?php
                    $filters = ['' => '🐾 Tất cả', 'cho' => '🐶 Chó', 'meo' => '🐱 Mèo', 'hamster' => '🐹 Hamster'];
                    foreach ($filters as $val => $label):
                        $active = ($loai_filter === $val);
                        $qs = 'admin.php?tab=sanpham' . ($val ? '&loai_filter='.$val : '');
                    ?>
                    <a href="<?= $qs ?>" class="btn btn-sm <?= $active ? 'btn-sage' : 'btn-outline' ?>"><?= $label ?></a>
                    <?php endforeach; ?>
                    <button class="btn btn-sage" onclick="openAddProduct()">➕ Thêm sản phẩm</button>
                </div>
            </div>
            <div class="card-body">
                <div class="product-admin-grid">
                <?php foreach ($rows as $p): ?>
                <div class="product-admin-card">
                    <img src="assets/images/<?= htmlspecialchars($p['hinh_anh'] ?? '') ?>" alt="" onerror="this.src='assets/images/placeholder.jpg'">
                    <div class="pad">
                        <div class="name"><?= htmlspecialchars($p['ten_san_pham']) ?></div>
                        <div class="price"><?= number_format($p['gia'],0,',','.') ?>đ</div>
                        <div style="font-size:.74rem;color:var(--muted);margin-top:3px"><?= htmlspecialchars($p['ten_loai'] ?? '') ?> · SL: <?= $p['so_luong'] ?? 0 ?></div>
                        <div class="actions">
                            <button class="btn btn-yellow btn-sm" onclick='openEditProduct(<?= json_encode($p, JSON_HEX_APOS) ?>)'>✏️ Sửa</button>
                            <button class="btn btn-red btn-sm"    onclick="showConfirmDelete('product',<?= $p['id'] ?>,'<?= htmlspecialchars($p['ten_san_pham'],ENT_QUOTES) ?>')">🗑️ Xóa</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                <div style="text-align:center;color:var(--muted);padding:50px;grid-column:1/-1">Chưa có sản phẩm nào.</div>
                <?php endif; ?>
                </div>
            </div>
        </div>
        <form method="POST" id="deleteProductForm" style="display:none">
            <input type="hidden" name="product_id" id="del_product_id">
            <input type="hidden" name="delete_product" value="1">
        </form>
        <?= renderPagination($page, $pages, $tab, $search, $loai_filter) ?>

        <?php /* ═══════════════════════════════════════════════
               BÀI VIẾT
        ════════════════════════════════════════════════════ */ ?>
        <?php elseif ($tab === 'baiviet'): ?>
        <div class="card">
            <div class="card-header">
                <h3>📰 Bài viết (<?= $data['total'] ?? 0 ?>)</h3>
                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
                    <div style="position:relative" id="bvSearchWrap">
                        <form method="GET" action="admin.php" class="search-bar" id="bvSearchForm" autocomplete="off">
                            <input type="hidden" name="tab" value="baiviet">
                            <input type="text" name="search_baiviet" id="bvSearchInput"
                                placeholder="🔍 Tìm tiêu đề bài viết..."
                                value="<?= htmlspecialchars($search) ?>"
                                oninput="bvSuggest(this.value)">
                            <button type="submit" class="btn btn-sage btn-sm">Tìm</button>
                            <?php if ($search): ?><a href="admin.php?tab=baiviet" class="btn btn-outline btn-sm">✕ Xóa</a><?php endif; ?>
                        </form>
                        <div id="bvDropdown" style="display:none;position:absolute;top:100%;left:0;right:0;background:#fff;border:1.5px solid var(--stone);border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:200;overflow:hidden"></div>
                    </div>
                    <button class="btn btn-sage" onclick="openAddArticle()">➕ Thêm bài viết</button>
                </div>
            </div>
            <div class="card-body">
                <div class="article-list">
             <?php foreach ($articles_full as $a): ?>
    <?php if (!$a) continue; ?>
                <div class="article-item">
                    <img src="assets/images/<?= htmlspecialchars($a['hinh_anh_1'] ?? '') ?>" alt="" onerror="this.src='assets/images/placeholder.jpg'">
                    <div class="info">
                        <div class="title"><?= htmlspecialchars($a['tieu_de']) ?></div>
                        <div class="date">📅 <?= date('d/m/Y H:i', strtotime($a['ngay_dang'])) ?></div>
                    </div>
                    <div class="actions">
                        <button class="btn btn-yellow btn-sm" onclick='openEditArticle(<?= json_encode($a, JSON_HEX_APOS) ?>)'>✏️ Sửa</button>
                        <button class="btn btn-red btn-sm"    onclick="showConfirmDelete('article',<?= $a['id'] ?>,'<?= htmlspecialchars($a['tieu_de'],ENT_QUOTES) ?>')">🗑️ Xóa</button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($articles_full)): ?>
                <div style="text-align:center;color:var(--muted);padding:50px">Chưa có bài viết nào.</div>
                <?php endif; ?>
                </div>
            </div>
        </div>
        <form method="POST" id="deleteArticleForm" style="display:none">
            <input type="hidden" name="article_id" id="del_article_id">
            <input type="hidden" name="delete_article" value="1">
        </form>
        <?= renderPagination($page, $pages, $tab, $search) ?>

        <?php /* ═══════════════════════════════════════════════
               THỐNG KÊ & DOANH THU
        ════════════════════════════════════════════════════ */ ?>
        <?php elseif ($tab === 'thongke'): ?>
        <form method="GET" action="admin.php" style="margin-bottom:20px">
            <input type="hidden" name="tab" value="thongke">
            <div class="year-filter">
                <label style="font-weight:600;color:var(--muted)">📅 Năm:</label>
                <select name="year" onchange="this.form.submit()">
                    <?php for ($y = date('Y'); $y >= date('Y') - 4; $y--): ?>
                    <option value="<?= $y ?>" <?= $year_filter == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </form>
        <!-- KPI -->
        <div class="stat-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px">
            <div class="stat-card"><div class="stat-icon" style="background:#fdf0e6">💰</div><div><div class="stat-num" style="font-size:1.15rem"><?= number_format($total_rev_yr,0,',','.') ?>đ</div><div class="stat-label">Tổng DT năm <?= $year_filter ?></div></div></div>
            <div class="stat-card"><div class="stat-icon" style="background:#edf4ed">🏆</div><div><div class="stat-num" style="font-size:1rem"><?= number_format(max($revenue_month ?: [0]),0,',','.') ?>đ</div><div class="stat-label">DT cao nhất — Tháng <?= $best_month ?></div></div></div>
            <div class="stat-card"><div class="stat-icon" style="background:#e8f4fd">📅</div><div><div class="stat-num" style="font-size:1rem"><?= number_format($rev_this_month,0,',','.') ?>đ</div><div class="stat-label">DT tháng <?= date('n/Y') ?></div></div></div>
            <div class="stat-card"><div class="stat-icon" style="background:#d4edda">🛒</div><div><div class="stat-num" style="font-size:1rem"><?= number_format($avg_order,0,',','.') ?>đ</div><div class="stat-label">TB/đơn (<?= $so_don_yr ?> đơn)</div></div></div>
        </div>
        <!-- Biểu đồ theo tháng -->
        <div class="card" style="margin-bottom:24px">
            <div class="card-header"><h3>📊 Doanh thu theo tháng — Năm <?= $year_filter ?></h3></div>
            <div class="card-body"><div style="position:relative;height:340px"><canvas id="monthChart"></canvas></div></div>
        </div>
        <div class="chart-grid">
            <div class="card">
                <div class="card-header"><h3>📈 Doanh thu theo năm</h3></div>
                <div class="card-body"><div style="position:relative;height:280px"><canvas id="yearChart"></canvas></div></div>
            </div>
            <div class="card">
                <div class="card-header"><h3>🔧 Dịch vụ phổ biến</h3></div>
                <div class="card-body">
                    <div style="position:relative;height:280px"><canvas id="dvChart2"></canvas></div>
                    <?php if (empty($dv_stats)): ?><p style="text-align:center;color:var(--muted);margin-top:20px">Chưa có dữ liệu.</p><?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Bảng chi tiết -->
        <div class="card">
            <div class="card-header"><h3>📋 Chi tiết doanh thu theo tháng — Năm <?= $year_filter ?></h3></div>
            <div class="card-body" style="padding:0">
                <table class="data-table">
                    <thead><tr><th>Tháng</th><th>Doanh thu</th><th>Số đơn</th><th>So với tháng trước</th><th>Tỉ lệ</th></tr></thead>
                    <tbody>
                    <?php foreach ($monthly_detail as $m => $detail): ?>
                    <tr>
                        <td><strong>Tháng <?= $m ?></strong></td>
                        <td style="font-weight:700;color:var(--clay)"><?= number_format($detail['revenue'],0,',','.') ?>đ</td>
                        <td style="color:var(--sage);font-weight:600"><?= $detail['orders'] ?> đơn</td>
                        <td>
                            <?php if ($m === 1): ?><span style="color:var(--muted)">—</span>
                            <?php elseif ($detail['diff'] > 0): ?><span style="color:var(--green)">▲ +<?= number_format($detail['diff'],0,',','.') ?>đ</span>
                            <?php elseif ($detail['diff'] < 0): ?><span style="color:var(--red)">▼ <?= number_format($detail['diff'],0,',','.') ?>đ</span>
                            <?php else: ?><span style="color:var(--muted)">= 0</span><?php endif; ?>
                        </td>
                        <td>
                            <div style="background:#f0ebe3;border-radius:99px;height:8px;width:100%;max-width:200px">
                                <div style="background:var(--clay);height:8px;border-radius:99px;width:<?= $detail['pct'] ?>%;transition:width .4s"></div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background:#fdf9f5">
                            <td style="font-weight:700;padding:12px">Tổng cộng</td>
                            <td style="font-weight:700;color:var(--clay);font-size:1rem;padding:12px"><?= number_format($total_rev_yr,0,',','.') ?>đ</td>
                            <td style="font-weight:700;color:var(--sage);padding:12px"><?= $so_don_yr ?> đơn</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /admin-content -->
</div><!-- /admin-main -->

<!-- ══════════════════════════════════════════════════════════ -->
<!-- JAVASCRIPT                                                -->
<!-- ══════════════════════════════════════════════════════════ -->
<script>
// ── Dữ liệu PHP → JS ─────────────────────────────────────────
const MONTHS        = ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'];
const REV_MONTH     = <?= json_encode(array_values($revenue_month)) ?>;
const REV_YEAR      = <?= json_encode(array_values($revenue_year)) ?>;
const YEAR_LABELS   = <?= json_encode(array_keys($revenue_year)) ?>;
const DV_LABELS     = <?= json_encode(array_column($dv_stats,'ten_dich_vu')) ?>;
const DV_DATA       = <?= json_encode(array_column($dv_stats,'cnt')) ?>;
const DV_COLORS     = ['#5a7a5a','#c17f4f','#2980b9','#e74c3c','#f39c12'];
const CLAY          = 'rgba(193,127,79,0.82)';
const CLAY_FULL     = 'rgba(193,127,79,1)';
const CUR_MONTH_IDX = new Date().getMonth(); // 0-based

// ── Format VNĐ ───────────────────────────────────────────────
function fmtVND(v) {
    if (v >= 1000000) return (v/1000000).toFixed(1) + 'M đ';
    if (v >= 1000)    return (v/1000).toFixed(0)    + 'K đ';
    return v + ' đ';
}

// ── Tạo bar chart ────────────────────────────────────────────
function makeBarChart(id, data) {
    const el = document.getElementById(id);
    if (!el) return;
    new Chart(el, {
        type: 'bar',
        data: {
            labels: MONTHS,
            datasets: [{
                label: 'Doanh thu (đ)',
                data,
                backgroundColor: MONTHS.map((_,i) => i === CUR_MONTH_IDX ? CLAY_FULL : CLAY),
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend:{ display:false }, tooltip:{ callbacks:{ label: ctx => ' ' + ctx.parsed.y.toLocaleString('vi-VN') + 'đ' } } },
            scales: {
                y: { beginAtZero:true, grid:{ color:'#f0ebe3' }, ticks:{ callback: fmtVND } },
                x: { grid:{ display:false } }
            }
        }
    });
}

// ── Charts init ───────────────────────────────────────────────
makeBarChart('dashChart',  REV_MONTH);
makeBarChart('monthChart', REV_MONTH);

const dvEl = document.getElementById('dvChart');
if (dvEl && DV_DATA.length) {
    new Chart(dvEl, { type:'doughnut', data:{ labels:DV_LABELS, datasets:[{ data:DV_DATA, backgroundColor:DV_COLORS, borderWidth:2, borderColor:'#fff' }] }, options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } } } });
}
const dvEl2 = document.getElementById('dvChart2');
if (dvEl2 && DV_DATA.length) {
    new Chart(dvEl2, { type:'pie', data:{ labels:DV_LABELS, datasets:[{ data:DV_DATA, backgroundColor:DV_COLORS, borderWidth:2, borderColor:'#fff' }] }, options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom' } } } });
}
const yearEl = document.getElementById('yearChart');
if (yearEl) {
    new Chart(yearEl, { type:'line', data:{ labels:YEAR_LABELS, datasets:[{ label:'Doanh thu', data:REV_YEAR, borderColor:'#c17f4f', backgroundColor:'rgba(193,127,79,.12)', pointBackgroundColor:'#c17f4f', pointRadius:6, tension:.35, fill:true }] }, options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false }, tooltip:{ callbacks:{ label: ctx => ' ' + ctx.parsed.y.toLocaleString('vi-VN') + 'đ' } } }, scales:{ y:{ beginAtZero:true, grid:{ color:'#f0ebe3' }, ticks:{ callback:fmtVND } }, x:{ grid:{ display:false } } } } });
}

// ══════════════════════════════════════════════════════════════
// CONFIRM MODAL
// ══════════════════════════════════════════════════════════════
function showConfirm(action, id, name) {
    const yes   = document.getElementById('confirmYes');
    const isHT  = action === 'hoan_thanh';
    const isConfirm = action === 'dong_y';
    document.getElementById('confirmIcon').textContent  = isHT ? '✅' : '🚫';
    document.getElementById('confirmTitle').textContent = isHT ? 'Xác nhận hoàn thành' : 'Xác nhận hủy lịch';
    document.getElementById('confirmText').textContent  = isHT
        ? `Đánh dấu lịch hẹn của "${name}" là đã hoàn thành?`
        : `Hủy lịch hẹn của "${name}"? Không thể hoàn tác!`;
    yes.href      = `admin.php?tab=lich&action=${action}&id=${id}`;
    yes.className = isHT ? 'btn btn-sage' : 'btn btn-red';
    if (isConfirm) {
        document.getElementById('confirmIcon').textContent = '📅';
        document.getElementById('confirmTitle').textContent = 'Đồng ý lịch hẹn';
        document.getElementById('confirmText').textContent = `Đồng ý lịch hẹn của "${name}" và chuyển sang trạng thái đang chờ lịch?`;
        yes.className = 'btn btn-sage';
    }
    document.getElementById('confirmOverlay').classList.add('open');
}
function closeConfirm() {
    document.getElementById('confirmOverlay').classList.remove('open');
}
document.getElementById('confirmOverlay').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeConfirm();
});

function showConfirmDelete(type, id, name) {
    const yes = document.getElementById('confirmYes');
    document.getElementById('confirmIcon').textContent  = '🗑️';
    document.getElementById('confirmTitle').textContent = 'Xác nhận xóa';
    document.getElementById('confirmText').textContent  = `Xóa "${name}"? Không thể hoàn tác!`;
    yes.className = 'btn btn-red';
    yes.removeAttribute('href');
    yes.onclick = e => {
        e.preventDefault();
        if (type === 'product') {
            document.getElementById('del_product_id').value = id;
            document.getElementById('deleteProductForm').submit();
        } else if (type === 'article') {
            document.getElementById('del_article_id').value = id;
            document.getElementById('deleteArticleForm').submit();
        }
        closeConfirm();
    };
    document.getElementById('confirmOverlay').classList.add('open');
}

// ══════════════════════════════════════════════════════════════
// MODAL HELPERS
// ══════════════════════════════════════════════════════════════
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});

// ══════════════════════════════════════════════════════════════
// SẢN PHẨM MODAL
// ══════════════════════════════════════════════════════════════
function openAddProduct() {
    document.getElementById('productModalTitle').textContent = '➕ Thêm sản phẩm mới';
    document.getElementById('product_id').value = 0;
    ['inp_ten','inp_gia','inp_mota'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('inp_sl').value = 100;
    document.getElementById('prev_product').style.display = 'none';
    openModal('productModal');
}
function openEditProduct(p) {
    document.getElementById('productModalTitle').textContent = '✏️ Chỉnh sửa sản phẩm';
    document.getElementById('product_id').value   = p.id;
    document.getElementById('inp_ten').value      = p.ten_san_pham;
    document.getElementById('inp_gia').value      = p.gia;
    document.getElementById('inp_mota').value     = p.mo_ta || '';
    document.getElementById('inp_sl').value       = p.so_luong || 0;
    document.getElementById('inp_loai').value     = p.loai_id;
    document.getElementById('inp_danh_muc').value = p.danh_muc || 'dog-food';
    const prev = document.getElementById('prev_product');
    if (p.hinh_anh) { prev.src = 'assets/images/' + p.hinh_anh; prev.style.display = 'block'; }
    else { prev.style.display = 'none'; }
    openModal('productModal');
}

// ══════════════════════════════════════════════════════════════
// BÀI VIẾT MODAL
// ══════════════════════════════════════════════════════════════
function openAddArticle() {
    document.getElementById('articleModalTitle').textContent = '📝 Thêm bài viết mới';
    document.getElementById('article_id').value = 0;
    ['inp_tieu_de','inp_nd1','inp_nd2','inp_nd3','inp_nd4','inp_email_bv'].forEach(id => document.getElementById(id).value = '');
    [1,2,3,4].forEach(i => {
        document.getElementById('old_img_'+i).value = '';
        const p = document.getElementById('prev_img'+i);
        if (p) p.style.display = 'none';
    });
    openModal('articleModal');
}
function openEditArticle(a) {
    document.getElementById('articleModalTitle').textContent = '✏️ Chỉnh sửa bài viết';
    document.getElementById('article_id').value    = a.id;
    document.getElementById('inp_tieu_de').value   = a.tieu_de || '';
    document.getElementById('inp_nd1').value       = a.noi_dung_chinh || '';
    document.getElementById('inp_nd2').value       = a.nguyen_nhan_pho_bien || '';
    document.getElementById('inp_nd3').value       = a.huong_dan || '';
    document.getElementById('inp_nd4').value       = a.cach_cham || '';
    document.getElementById('inp_email_bv').value  = a.email_nhan_thong_bao || '';
    [1,2,3,4].forEach(i => {
        const key  = 'hinh_anh_' + i;
        document.getElementById('old_img_'+i).value = a[key] || '';
        const prev = document.getElementById('prev_img'+i);
        if (prev && a[key]) { prev.src = 'assets/images/' + a[key]; prev.style.display = 'block'; }
        else if (prev)      { prev.style.display = 'none'; }
    });
    openModal('articleModal');
}

// ══════════════════════════════════════════════════════════════
// CHI TIẾT ĐƠN HÀNG (AJAX)
// ══════════════════════════════════════════════════════════════
function viewOrderDetail(id) {
    fetch(`admin.php?ajax=order_detail&id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (data.error) { alert(data.error); return; }
            const o    = data.order;
            const isHT = o.trang_thai === 'hoan_thanh';
            const isCho= o.trang_thai === 'cho_xu_ly';
            let statusBadge = isCho ? '⏳ Chờ xử lý' : (isHT ? '✅ Hoàn thành' : '🚫 Đã hủy');
            let html = `
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px 20px;margin-bottom:20px;font-size:.875rem">
                <div><strong>👤 Khách hàng:</strong><br>${o.ho_ten}</div>
                <div><strong>📱 SĐT:</strong><br>${o.sdt}</div>
                <div><strong>📍 Địa chỉ:</strong><br>${o.dia_chi || '—'}</div>
                <div><strong>💳 Thanh toán:</strong><br>${o.phuong_thuc_tt === 'COD' ? '💵 COD' : '🏦 Chuyển khoản'}</div>
                <div><strong>📅 Ngày đặt:</strong><br>${o.ngay_dat}</div>
                <div><strong>🔖 Trạng thái:</strong><br>${statusBadge}</div>
            </div>
            <h4 style="color:#5a7a5a;margin-bottom:10px;font-family:Fraunces,serif">🧾 Sản phẩm đặt</h4>
            <table style="width:100%;border-collapse:collapse;font-size:.84rem">
                <thead><tr style="background:#faf7f2">
                    <th style="padding:8px 10px;text-align:left;border-bottom:2px solid #e8e0d5">Sản phẩm</th>
                    <th style="padding:8px 10px;text-align:center;border-bottom:2px solid #e8e0d5">SL</th>
                    <th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e8e0d5">Đơn giá</th>
                    <th style="padding:8px 10px;text-align:right;border-bottom:2px solid #e8e0d5">Thành tiền</th>
                </tr></thead><tbody>`;
            data.items.forEach(item => {
                html += `<tr>
                    <td style="padding:8px 10px;border-bottom:1px solid #f4f0ea">${item.ten_san_pham}</td>
                    <td style="padding:8px 10px;border-bottom:1px solid #f4f0ea;text-align:center">${item.so_luong}</td>
                    <td style="padding:8px 10px;border-bottom:1px solid #f4f0ea;text-align:right">${Number(item.gia).toLocaleString('vi-VN')}đ</td>
                    <td style="padding:8px 10px;border-bottom:1px solid #f4f0ea;text-align:right;color:#c17f4f;font-weight:700">${Number(item.thanh_tien).toLocaleString('vi-VN')}đ</td>
                </tr>`;
            });
            html += `</tbody><tfoot><tr style="background:#faf7f2">
                <td colspan="3" style="padding:10px;font-weight:700;text-align:right">Tổng cộng:</td>
                <td style="padding:10px;font-weight:700;color:#c17f4f;font-size:1rem;text-align:right">${Number(o.tong_tien).toLocaleString('vi-VN')}đ</td>
            </tr></tfoot></table>`;
            document.getElementById('orderDetailTitle').textContent = `📋 Chi tiết đơn hàng #${id}`;
            document.getElementById('orderDetailContent').innerHTML = html;
            openModal('orderDetailModal');
        })
        .catch(() => alert('Không thể tải chi tiết đơn hàng!'));
}

// ── Image preview ─────────────────────────────────────────────
function previewImg(input, previewId) {
    const prev = document.getElementById(previewId);
    if (!prev || !input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => { prev.src = e.target.result; prev.style.display = 'block'; };
    reader.readAsDataURL(input.files[0]);
}

// ── Autocomplete bài viết ──────────────────────────────────────
let bvTimer = null;
function bvSuggest(val) {
    const dd = document.getElementById('bvDropdown');
    if (!dd) return;
    clearTimeout(bvTimer);
    if (val.trim().length < 1) { dd.style.display = 'none'; return; }
    bvTimer = setTimeout(() => {
        fetch(`admin.php?ajax=suggest_baiviet&q=${encodeURIComponent(val)}`)
            .then(r => r.json())
            .then(list => {
                if (!list.length) { dd.style.display = 'none'; return; }
                dd.innerHTML = list.map(item =>
                    `<div class="bv-suggest-item" onclick="bvPick(${JSON.stringify(item.tieu_de)})">${item.tieu_de}</div>`
                ).join('');
                dd.style.display = 'block';
            });
    }, 220);
}
function bvPick(title) {
    const inp = document.getElementById('bvSearchInput');
    if (inp) { inp.value = title; }
    const dd = document.getElementById('bvDropdown');
    if (dd) dd.style.display = 'none';
    document.getElementById('bvSearchForm').submit();
}
document.addEventListener('click', e => {
    const wrap = document.getElementById('bvSearchWrap');
    if (wrap && !wrap.contains(e.target)) {
        const dd = document.getElementById('bvDropdown');
        if (dd) dd.style.display = 'none';
    }
});

// ── Auto-hide toast ───────────────────────────────────────────
setTimeout(() => {
    const t = document.querySelector('.toast-msg');
    if (t) { t.style.transition = 'opacity .5s'; t.style.opacity = '0'; setTimeout(() => t.remove(), 500); }
}, 4000);
</script>
<?php

// ── Helper: render pagination HTML ───────────────────────────
function renderPagination(int $page, int $pages, string $tab, string $search, string $loaiFilter = ''): string {
    if ($pages <= 1) return '';
    $html = '<div class="pagination">';
    for ($i = 1; $i <= $pages; $i++) {
        $qs    = "?tab=$tab&page=$i"
               . ($search     ? "&search_{$tab}=" . urlencode($search) : '')
               . ($loaiFilter ? "&loai_filter="   . urlencode($loaiFilter) : '');
        $class = ($i === $page) ? ' class="active"' : '';
        $html .= "<a href=\"admin.php$qs\"$class>$i</a>";
    }
    $html .= '</div>';
    return $html;
}
