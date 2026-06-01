<?php
/**
 * shopcard_ui.php
 * Nhận biến từ shopcard_control.php:
 *   $cart           array           giỏ hàng hiện tại
 *   $total          float           tổng tiền
 *   $order_success  bool
 *   $order_error    string
 *   $order_history  OrderHistory[]  lịch sử đơn hàng
 *   $userId         int|null        user đang đăng nhập
 *   $active_tab     string          'cart' | 'history'
 */

include __DIR__ . '/../includes/header.php';
?>

<style>
/* ── Base ── */
.shopcard-wrapper {
    max-width: 1100px; margin: 40px auto;
    padding: 0 20px;
    font-family: 'DM Sans', 'Segoe UI', sans-serif;
    color: #333;
}
h2.page-title {
    font-size: 28px; font-weight: 700;
    margin-bottom: 24px; color: #2c3e50;
    border-left: 5px solid #5a7a5a;
    padding-left: 14px;
}

/* ── Tab nav ── */
.tab-nav {
    display: flex; gap: 6px;
    margin-bottom: 28px;
    border-bottom: 2px solid #e8e0d5;
    padding-bottom: 0;
}
.tab-btn {
    padding: 10px 24px;
    font-size: 14px; font-weight: 600;
    border: none; background: none;
    cursor: pointer; color: #7a7267;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    font-family: inherit;
    transition: color .2s, border-color .2s;
    border-radius: 0;
}
.tab-btn:hover { color: #5a7a5a; }
.tab-btn.active { color: #5a7a5a; border-bottom-color: #5a7a5a; }

/* ── Section card ── */
.section-card {
    background: #fff; border: 1px solid #e8e0d5;
    border-radius: 14px; padding: 28px 32px;
    margin-bottom: 28px;
    box-shadow: 0 4px 20px rgba(90,122,90,0.08);
}
.section-card h3 {
    font-size: 18px; font-weight: 600;
    margin-bottom: 20px; color: #5a7a5a;
    padding-bottom: 10px;
    border-bottom: 1px solid #e8e0d5;
}

/* ── Form ── */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
.form-grid .full-width { grid-column: 1 / -1; }
.form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #555; }
.form-group input, .form-group select {
    width: 100%; padding: 10px 14px;
    border: 1.5px solid #e8e0d5; border-radius: 8px;
    font-size: 14px; box-sizing: border-box;
    font-family: inherit; transition: border-color .2s;
    background: #faf7f2;
}
.form-group input:focus, .form-group select:focus {
    border-color: #7fa87f; outline: none; background: #fff;
}

/* ── Cart table ── */
.cart-table { width: 100%; border-collapse: collapse; }
.cart-table th {
    background: #f4f1eb; padding: 12px 14px; text-align: left;
    font-size: 13px; color: #7a7267; border-bottom: 2px solid #e8e0d5;
}
.cart-table td { padding: 14px; border-bottom: 1px solid #f0ebe3; vertical-align: middle; }
.cart-table .prod-name { font-weight: 600; font-size: 14px; }
.cart-table .price { color: #c17f4f; font-weight: 700; }
.cart-img { width: 70px; height: 70px; object-fit: cover; border-radius: 10px; border: 1px solid #e8e0d5; background: #edf4ed; }
.qty-input { width: 70px; padding: 7px 8px; border: 1.5px solid #e8e0d5; border-radius: 8px; text-align: center; font-size: 14px; font-family: inherit; background: #faf7f2; }
.qty-input:focus { border-color: #7fa87f; outline: none; }
.btn-remove { background: none; border: none; color: #e74c3c; cursor: pointer; font-size: 20px; padding: 4px 8px; border-radius: 6px; }
.btn-remove:hover { background: #fff0f0; }
.btn-update { background: #f4f1eb; border: 1.5px solid #e8e0d5; padding: 9px 22px; border-radius: 8px; cursor: pointer; font-size: 13px; font-family: inherit; font-weight: 600; color: #5a7a5a; }
.btn-update:hover { background: #edf4ed; }
.cart-total-bar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; padding: 16px 4px 0; margin-top: 6px; }
.cart-total-text { font-size: 20px; font-weight: 700; color: #2c3e50; }
.cart-total-text span { color: #c17f4f; }

/* ── Checkout ── */
.checkout-section { display: none; }
.checkout-section.active { display: block; }
.payment-options { display: flex; gap: 16px; margin-top: 12px; flex-wrap: wrap; }
.payment-option { display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 12px 20px; border: 2px solid #e8e0d5; border-radius: 10px; font-size: 14px; font-weight: 500; transition: all .2s; user-select: none; }
.payment-option:hover, .payment-option.selected { border-color: #5a7a5a; background: #edf4ed; }
.payment-option input[type=radio] { accent-color: #5a7a5a; width: 16px; height: 16px; }
.qr-box { display: none; margin-top: 20px; text-align: center; background: #faf7f2; border: 2px dashed #5a7a5a; border-radius: 14px; padding: 28px; }
.qr-box img { max-width: 250px; border-radius: 12px; }
.qr-box p { margin-top: 14px; font-size: 13px; color: #7a7267; }

/* ── Buttons ── */
.btn-checkout { display: inline-flex; align-items: center; gap: 10px; background: #5a7a5a; color: #fff; border: none; padding: 13px 32px; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: inherit; transition: background .2s; }
.btn-checkout:hover { background: #7fa87f; }
.btn-place-order { background: #27ae60; color: #fff; border: none; padding: 13px 38px; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: inherit; }
.btn-place-order:hover { background: #1e8449; }

/* ── Alerts ── */
.error-msg { background: #fff3f3; border: 1px solid #e74c3c; color: #c0392b; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
.success-msg { background: #f0fff6; border: 1px solid #27ae60; color: #1e8449; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }

/* ── Success overlay ── */
.success-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 9999; display: flex; align-items: center; justify-content: center; }
.success-box { background: #fff; border-radius: 20px; padding: 50px 44px; max-width: 520px; width: 90%; text-align: center; box-shadow: 0 16px 50px rgba(0,0,0,0.2); animation: popIn .35s ease; }
@keyframes popIn { from { transform: scale(.82); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.success-icon { width: 82px; height: 82px; background: #27ae60; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 38px; color: #fff; }
.success-box h3 { font-size: 22px; font-weight: 700; margin-bottom: 12px; color: #2c3e50; }
.success-box .order-info { background: #f8fdf8; border: 1px solid #d4edda; border-radius: 10px; padding: 14px 18px; margin: 14px 0 22px; text-align: left; font-size: 14px; line-height: 1.85; color: #444; }
.success-box .order-info strong { color: #27ae60; }
.success-box p { font-size: 14px; color: #555; line-height: 1.75; margin-bottom: 22px; }
.success-box a.btn-back { background: #5a7a5a; color: #fff; padding: 11px 28px; border-radius: 9px; text-decoration: none; font-weight: 600; font-size: 15px; display: inline-block; }
.success-box a.btn-back:hover { background: #7fa87f; }

/* ── Empty cart ── */
.empty-cart { text-align: center; padding: 70px 20px; color: #aaa; }
.empty-cart .icon { font-size: 68px; margin-bottom: 18px; }
.empty-cart p { font-size: 17px; margin-bottom: 20px; }
.empty-cart a.btn-shop { background: #5a7a5a; color: #fff; padding: 11px 28px; border-radius: 9px; text-decoration: none; font-weight: 600; font-size: 15px; display: inline-block; }

/* ── Order summary ── */
.order-summary-table { width: 100%; font-size: 14px; border-collapse: collapse; margin-top: 14px; }
.order-summary-table td { padding: 9px 8px; border-bottom: 1px solid #f0ebe3; }
.order-summary-table .item-total { color: #c17f4f; font-weight: 700; text-align: right; }
.order-summary-table .grand-row td { padding-top: 14px; font-weight: 700; font-size: 16px; border-bottom: none; }
.order-summary-table .grand-total { color: #c17f4f; font-size: 18px; text-align: right; }

/* ═══════════════════════════════════════════════════════════
   LỊCH SỬ ĐƠN HÀNG
   ═══════════════════════════════════════════════════════════ */
.history-empty {
    text-align: center; padding: 60px 20px; color: #aaa;
}
.history-empty .icon { font-size: 56px; margin-bottom: 16px; }
.history-empty p { font-size: 16px; }

/* Card từng đơn hàng */
.order-history-card {
    border: 1px solid #e8e0d5; border-radius: 12px;
    margin-bottom: 20px; overflow: hidden;
    box-shadow: 0 2px 12px rgba(90,122,90,0.07);
}
.order-history-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 10px;
    padding: 16px 20px;
    background: #f8f5f0;
    border-bottom: 1px solid #e8e0d5;
    cursor: pointer;
    user-select: none;
}
.order-history-header:hover { background: #f2ede6; }
.order-id { font-weight: 700; color: #2c3e50; font-size: 15px; }
.order-date { font-size: 13px; color: #7a7267; }
.order-total { font-weight: 700; color: #c17f4f; font-size: 15px; }

/* Badge trạng thái */
.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 14px; border-radius: 99px;
    font-size: 12px; font-weight: 700;
}
.status-pending  { background: #fff8e1; color: #f59e0b; border: 1px solid #fde68a; }
.status-done     { background: #e6fff2; color: #27ae60; border: 1px solid #a7f3d0; }
.status-cancel   { background: #fff0f0; color: #e74c3c; border: 1px solid #fca5a5; }

/* Nội dung chi tiết đơn (accordion) */
.order-history-body {
    display: none; padding: 0;
}
.order-history-body.open { display: block; }

/* Bảng chi tiết sản phẩm */
.order-items-table { width: 100%; border-collapse: collapse; }
.order-items-table th {
    background: #faf7f2; padding: 10px 16px;
    text-align: left; font-size: 12px; color: #7a7267;
    border-bottom: 1px solid #e8e0d5; text-transform: uppercase; letter-spacing: .5px;
}
.order-items-table td { padding: 12px 16px; border-bottom: 1px solid #f0ebe3; font-size: 13px; vertical-align: middle; }
.order-items-table tr:last-child td { border-bottom: none; }
.item-img { width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid #e8e0d5; background: #edf4ed; }
.item-name { font-weight: 600; color: #2c3e50; }
.item-price { color: #c17f4f; font-weight: 600; }

/* Footer đơn hàng */
.order-history-footer {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; background: #faf7f2;
    border-top: 1px solid #e8e0d5; flex-wrap: wrap; gap: 10px;
}
.order-payment { font-size: 13px; color: #7a7267; }
.order-footer-total { font-weight: 700; font-size: 16px; color: #c17f4f; }
.order-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.history-action-btn {
    border: 1px solid #d9d0c4; background: #fff; color: #5a7a5a;
    padding: 6px 12px; border-radius: 8px; cursor: pointer;
    font-size: 12px; font-weight: 700; font-family: inherit;
}
.history-action-btn:hover { background: #edf4ed; border-color: #5a7a5a; }
.history-action-btn.primary { background: #5a7a5a; border-color: #5a7a5a; color: #fff; }
.history-action-btn.primary:hover { background: #7fa87f; }
.order-edit-box { display: none; padding: 18px 20px; border-top: 1px solid #e8e0d5; background: #fffdf9; }
.order-edit-box.open { display: block; }
.order-edit-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 16px; }
.order-edit-grid .full-width { grid-column: 1 / -1; }
.order-edit-grid label { display: block; font-size: 12px; font-weight: 700; color: #7a7267; margin-bottom: 5px; }
.order-edit-grid input {
    width: 100%; padding: 9px 12px; border: 1.5px solid #e8e0d5;
    border-radius: 8px; background: #fff; font-size: 13px; font-family: inherit;
}
.order-edit-grid input:focus { outline: none; border-color: #7fa87f; }
.order-edit-note { font-size: 12px; color: #7a7267; margin-top: 10px; }

/* Chevron icon */
.chevron { transition: transform .25s; font-style: normal; font-size: 14px; color: #7a7267; }
.order-history-header.open .chevron { transform: rotate(180deg); }

/* Login notice */
.login-notice {
    text-align: center; padding: 50px 20px; color: #7a7267;
    background: #faf7f2; border-radius: 12px; border: 1px dashed #e8e0d5;
}
.login-notice p { font-size: 15px; margin-bottom: 16px; }
.login-notice a { background: #5a7a5a; color: #fff; padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; }
.login-notice a:hover { background: #7fa87f; }

@media(max-width: 640px) {
    .form-grid { grid-template-columns: 1fr; }
    .order-edit-grid { grid-template-columns: 1fr; }
    .payment-options { flex-direction: column; }
    .section-card { padding: 20px 16px; }
    .order-history-header { flex-direction: column; align-items: flex-start; }
}
</style>

<div class="shopcard-wrapper">
    <h2 class="page-title">🛒 Giỏ Hàng</h2>

    <!-- ── TAB NAV ── -->
    <div class="tab-nav">
        <button class="tab-btn <?= $active_tab === 'cart' ? 'active' : '' ?>"
                onclick="switchTab('cart')">🛒 Giỏ hàng</button>
        <button class="tab-btn <?= $active_tab === 'history' ? 'active' : '' ?>"
                onclick="switchTab('history')">📦 Lịch sử đơn hàng</button>
    </div>

    <!-- ══════════════════════════════════════════
         TAB: GIỎ HÀNG
         ══════════════════════════════════════════ -->
    <div id="tab-cart" class="tab-content" style="display:<?= $active_tab==='cart' ? 'block' : 'none' ?>">

        <?php if ($order_success && isset($_SESSION['last_order'])): ?>
        <?php $lo = $_SESSION['last_order']; ?>
        <div class="success-overlay">
            <div class="success-box">
                <div class="success-icon">✓</div>
                <h3>Đặt hàng thành công! 🎉</h3>
                <div class="order-info">
                    <strong>🧾 Mã đơn:</strong> #<?= $lo['id'] ?><br>
                    <strong>👤 Khách hàng:</strong> <?= htmlspecialchars($lo['ho_ten']) ?><br>
                    <strong>📱 SĐT:</strong> <?= htmlspecialchars($lo['sdt']) ?><br>
                    <strong>📍 Địa chỉ:</strong> <?= htmlspecialchars($lo['dia_chi_tt']) ?><br>
                    <strong>💳 Thanh toán:</strong>
                        <?= $lo['phuong_thuc'] === 'COD' ? '🚚 Thanh toán khi nhận hàng' : '💳 Chuyển khoản online' ?><br>
                    <strong>💰 Tổng tiền:</strong>
                        <span style="color:#27ae60;font-weight:700"><?= number_format($lo['tong'],0,',','.') ?>đ</span>
                </div>
                <p>📱 <strong>Vui lòng để ý điện thoại</strong> — đội ngũ chúng tôi sẽ gọi xác nhận sớm nhất. 🐾</p>
                <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
                    <a href="index.php" class="btn-back">🏠 Tiếp tục mua sắm</a>
                    <a href="shopcard.php?tab=history" class="btn-back" style="background:#3498db">📦 Xem đơn hàng</a>
                </div>
            </div>
        </div>

        <?php elseif (empty($cart)): ?>
        <div class="empty-cart">
            <div class="icon">🛍️</div>
            <p>Giỏ hàng của bạn đang trống.</p>
            <a href="products.php" class="btn-shop">Khám phá sản phẩm ngay →</a>
        </div>

        <?php else: ?>

        <?php if ($order_error): ?>
        <div class="error-msg">⚠️ <?= htmlspecialchars($order_error) ?></div>
        <?php endif; ?>

        <!-- Thông tin cá nhân -->
        <div class="section-card">
            <h3>👤 Thông Tin Cá Nhân</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Họ và tên <span style="color:#e74c3c">*</span></label>
                    <input type="text" id="f_ho_ten" placeholder="Nguyễn Văn A"
                           value="<?= htmlspecialchars($_POST['ho_ten'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Ngày sinh</label>
                    <input type="date" id="f_ngay_sinh" value="<?= htmlspecialchars($_POST['ngay_sinh'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Giới tính</label>
                    <select id="f_gioi_tinh">
                        <option value="">-- Chọn --</option>
                        <option value="Nam"  <?= (($_POST['gioi_tinh']??'')==='Nam'  ?'selected':'') ?>>Nam</option>
                        <option value="Nu"   <?= (($_POST['gioi_tinh']??'')==='Nu'   ?'selected':'') ?>>Nữ</option>
                        <option value="Khac" <?= (($_POST['gioi_tinh']??'')==='Khac' ?'selected':'') ?>>Khác</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Số điện thoại <span style="color:#e74c3c">*</span></label>
                    <input type="tel" id="f_sdt" placeholder="0901 234 567"
                           value="<?= htmlspecialchars($_POST['sdt'] ?? '') ?>">
                </div>
                <div class="form-group full-width">
                    <label>Địa chỉ</label>
                    <input type="text" id="f_dia_chi"
                           placeholder="Số nhà, đường, phường/xã, tỉnh/thành"
                           value="<?= htmlspecialchars($_POST['dia_chi'] ?? '') ?>">
                </div>
            </div>
        </div>

        <!-- Giỏ hàng -->
        <div class="section-card">
            <h3>🛒 Sản Phẩm Trong Giỏ</h3>
            <form method="post" action="shopcard.php" id="cart-form">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Hình</th><th>Sản phẩm</th><th>Đơn giá</th>
                            <th>Số lượng</th><th>Thành tiền</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cart as $id => $item): ?>
                    <?php $stock = max(1, (int)($item['stock'] ?? 999)); ?>
                    <tr>
                        <td><img class="cart-img" src="assets/images/<?= htmlspecialchars($item['img']) ?>"
                                 alt="<?= htmlspecialchars($item['name']) ?>"
                                 onerror="this.src='assets/images/placeholder.jpg'"></td>
                        <td class="prod-name"><?= htmlspecialchars($item['name']) ?></td>
                        <td class="price"><?= number_format($item['price'],0,',','.') ?>đ</td>
                        <td>
                            <input class="qty-input row-qty" type="number"
                                   name="qty[<?= $id ?>]" value="<?= (int)$item['qty'] ?>"
                                   min="1" max="<?= $stock ?>" data-price="<?= (int)$item['price'] ?>" data-stock="<?= $stock ?>">
                        </td>
                        <td class="price row-subtotal"><?= number_format($item['price']*$item['qty'],0,',','.') ?>đ</td>
                        <td>
                            <a href="shopcard.php?remove=<?= $id ?>"
                               onclick="return confirm('Xóa sản phẩm này?')" style="text-decoration:none">
                                <button type="button" class="btn-remove">✕</button>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="cart-total-bar">
                    <button type="submit" name="update_qty" class="btn-update">🔄 Cập nhật giỏ hàng</button>
                    <div class="cart-total-text">Tổng cộng: <span id="cart-grand-total"><?= number_format($total,0,',','.') ?>đ</span></div>
                </div>
            </form>
            <div style="text-align:right; margin-top:24px;">
                <button class="btn-checkout" onclick="showCheckout()">✅ Thanh toán</button>
            </div>
        </div>

        <!-- Thanh toán -->
        <div class="section-card checkout-section" id="checkout-section">
            <h3>💳 Hoàn Tất Đơn Hàng</h3>
            <form method="post" action="shopcard.php" id="order-form">
                <input type="hidden" name="ho_ten"    id="h_ho_ten">
                <input type="hidden" name="ngay_sinh" id="h_ngay_sinh">
                <input type="hidden" name="gioi_tinh" id="h_gioi_tinh">
                <input type="hidden" name="sdt"       id="h_sdt">
                <input type="hidden" name="dia_chi"   id="h_dia_chi">

                <div class="form-group">
                    <label>Địa chỉ nhận hàng <span style="color:#e74c3c">*</span></label>
                    <input type="text" name="dia_chi_tt" id="inp_dia_chi_tt"
                           placeholder="Nhập địa chỉ giao hàng..."
                           value="<?= htmlspecialchars($_POST['dia_chi_tt'] ?? '') ?>" required>
                </div>
                <div class="form-group" style="margin-top:20px;">
                    <label>Phương thức thanh toán <span style="color:#e74c3c">*</span></label>
                    <div class="payment-options">
                        <label class="payment-option" id="opt-cod">
                            <input type="radio" name="phuong_thuc" value="COD"
                                   <?= (($_POST['phuong_thuc']??'')==='COD'?'checked':'') ?>
                                   onchange="onPaymentChange(this)">
                            🚚 Thanh toán khi nhận hàng (COD)
                        </label>
                        <label class="payment-option" id="opt-ck">
                            <input type="radio" name="phuong_thuc" value="ChuyenKhoan"
                                   <?= (($_POST['phuong_thuc']??'')==='ChuyenKhoan'?'checked':'') ?>
                                   onchange="onPaymentChange(this)">
                            💳 Chuyển khoản online
                        </label>
                    </div>
                </div>
                <div class="qr-box" id="qr-box">
                    <p style="font-weight:700;font-size:15px;color:#333;margin-bottom:16px;">📲 Quét mã QR để thanh toán</p>
                    <img src="assets/images/qr.jpg" alt="Mã QR">
                    <p>Sau khi chuyển khoản, nhấn <strong>"Đặt hàng ngay"</strong>.</p>
                </div>
                <div style="background:#faf7f2;border-radius:12px;padding:20px 22px;margin-top:22px;">
                    <strong style="color:#5a7a5a;">🧾 Chi tiết đơn hàng:</strong>
                    <table class="order-summary-table">
                    <?php foreach ($cart as $id => $item): ?>
                    <?php $stock = max(1, (int)($item['stock'] ?? 999)); ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td style="text-align:center;width:90px;">
                            <input class="qty-input order-qty" type="number"
                                   name="item_qty[<?= $id ?>]" value="<?= (int)$item['qty'] ?>"
                                   min="1" max="<?= $stock ?>" data-price="<?= (int)$item['price'] ?>" data-stock="<?= $stock ?>" style="width:62px;">
                        </td>
                        <td class="item-total" data-order-subtotal><?= number_format($item['price']*$item['qty'],0,',','.') ?>đ</td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="grand-row">
                        <td colspan="2" style="text-align:right;">Tổng cộng:</td>
                        <td class="grand-total" id="order-grand-total"><?= number_format($total,0,',','.') ?>đ</td>
                    </tr>
                    </table>
                </div>
                <div style="text-align:right;margin-top:24px;">
                    <button type="submit" name="place_order" class="btn-place-order"
                            onclick="syncPersonalFields()">🎉 Đặt hàng ngay</button>
                </div>
            </form>
        </div>

        <?php endif; ?>
    </div><!-- /tab-cart -->

    <!-- ══════════════════════════════════════════
         TAB: LỊCH SỬ ĐƠN HÀNG
         ══════════════════════════════════════════ -->
    <div id="tab-history" class="tab-content" style="display:<?= $active_tab==='history' ? 'block' : 'none' ?>">

        <?php if (!empty($history_success_msg)): ?>
        <div class="success-msg"><?= htmlspecialchars($history_success_msg) ?></div>
        <?php endif; ?>

        <?php if (!empty($history_error_msg)): ?>
        <div class="error-msg"><?= htmlspecialchars($history_error_msg) ?></div>
        <?php endif; ?>

        <?php if (!$userId): ?>
        <!-- Chưa đăng nhập -->
        <div class="login-notice">
            <p>🔒 Vui lòng đăng nhập để xem lịch sử đơn hàng của bạn.</p>
            <a href="dangnhap.php">Đăng nhập ngay</a>
        </div>

        <?php elseif (empty($order_history)): ?>
        <!-- Chưa có đơn nào -->
        <div class="history-empty">
            <div class="icon">📦</div>
            <p>Bạn chưa có đơn hàng nào.</p>
        </div>

        <?php else: ?>
        <!-- Danh sách đơn hàng -->
        <p style="color:#7a7267;font-size:14px;margin-bottom:20px;">
            Tìm thấy <strong><?= count($order_history) ?></strong> đơn hàng — nhấn vào đơn để xem chi tiết sản phẩm.
        </p>

        <?php foreach ($order_history as $i => $ord): ?>
        <div class="order-history-card">

            <!-- Header — click để mở/đóng chi tiết -->
            <div class="order-history-header" onclick="toggleOrder(<?= $i ?>)" id="hdr-<?= $i ?>">
                <div>
                    <span class="order-id">#<?= $ord->id ?></span>
                    <span class="order-date" style="margin-left:12px;">
                        📅 <?= date('d/m/Y H:i', strtotime($ord->ngay_dat)) ?>
                    </span>
                </div>
                <div class="order-actions">
                    <span class="status-badge <?= $ord->trangThaiClass() ?>">
                        <?= $ord->trangThaiLabel() ?>
                    </span>
                    <span class="order-total"><?= number_format($ord->tong_tien,0,',','.') ?>đ</span>
                    <button type="button" class="history-action-btn"
                            onclick="event.stopPropagation(); toggleOrder(<?= $i ?>)">Chi tiet</button>
                    <?php if ($ord->canEditInfo()): ?>
                    <button type="button" class="history-action-btn primary"
                            onclick="event.stopPropagation(); toggleEditOrder(<?= $i ?>)">Sua thong tin</button>
                    <?php endif; ?>
                    <i class="chevron">▼</i>
                </div>
            </div>

            <!-- Body — chi tiết sản phẩm -->
            <div class="order-history-body" id="body-<?= $i ?>">

                <?php if (empty($ord->items)): ?>
                <p style="padding:16px 20px;color:#aaa;font-size:13px;">Không có dữ liệu sản phẩm.</p>
                <?php else: ?>
                <table class="order-items-table">
                    <thead>
                        <tr>
                            <th style="width:60px;">Hình</th>
                            <th>Sản phẩm</th>
                            <th style="width:100px;text-align:center;">SL</th>
                            <th style="width:110px;text-align:right;">Đơn giá</th>
                            <th style="width:120px;text-align:right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ord->items as $item): ?>
                    <tr>
                        <td>
                            <?php if ($item->hinh_anh): ?>
                            <img class="item-img"
                                 src="assets/images/<?= htmlspecialchars($item->hinh_anh) ?>"
                                 alt="<?= htmlspecialchars($item->ten_san_pham) ?>"
                                 onerror="this.src='assets/images/placeholder.jpg'">
                            <?php else: ?>
                            <div class="item-img" style="display:flex;align-items:center;justify-content:center;font-size:22px;">🐾</div>
                            <?php endif; ?>
                        </td>
                        <td class="item-name"><?= htmlspecialchars($item->ten_san_pham) ?></td>
                        <td style="text-align:center;"><?= $item->so_luong ?></td>
                        <td class="item-price" style="text-align:right;"><?= number_format($item->gia,0,',','.') ?>đ</td>
                        <td class="item-price" style="text-align:right;"><?= number_format($item->thanh_tien,0,',','.') ?>đ</td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>

                <?php if ($ord->canEditInfo()): ?>
                <div class="order-edit-box" id="edit-<?= $i ?>">
                    <form method="post" action="shopcard.php?tab=history">
                        <input type="hidden" name="update_order_info" value="1">
                        <input type="hidden" name="order_id" value="<?= $ord->id ?>">
                        <div class="order-edit-grid">
                            <div>
                                <label>Ho ten nguoi nhan</label>
                                <input type="text" name="edit_ho_ten" value="<?= htmlspecialchars($ord->ho_ten) ?>" required>
                            </div>
                            <div>
                                <label>So dien thoai</label>
                                <input type="tel" name="edit_sdt" value="<?= htmlspecialchars($ord->sdt) ?>" required>
                            </div>
                            <div class="full-width">
                                <label>Dia chi nhan hang</label>
                                <input type="text" name="edit_dia_chi" value="<?= htmlspecialchars($ord->dia_chi) ?>" required>
                            </div>
                        </div>
                        <p class="order-edit-note">Chi sua duoc trong 24 gio sau khi dat hang.</p>
                        <div class="order-actions" style="justify-content:flex-end;margin-top:12px;">
                            <button type="button" class="history-action-btn" onclick="toggleEditOrder(<?= $i ?>)">Huy</button>
                            <button type="submit" class="history-action-btn primary">Luu thay doi</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Footer đơn -->
                <div class="order-history-footer">
                    <div>
                        <span class="order-payment">
                            <?= $ord->phuong_thuc === 'COD' ? '🚚 Thanh toán khi nhận hàng' : '💳 Chuyển khoản online' ?>
                        </span><br>
                        <span style="font-size:12px;color:#aaa;">📍 <?= htmlspecialchars($ord->dia_chi) ?></span>
                    </div>
                    <span class="order-footer-total">Tổng: <?= number_format($ord->tong_tien,0,',','.') ?>đ</span>
                </div>
            </div>

        </div><!-- /order-history-card -->
        <?php endforeach; ?>

        <?php endif; ?>
    </div><!-- /tab-history -->

</div><!-- /shopcard-wrapper -->

<script>
// ── Tab switching ─────────────────────────────────────────────────────────────
function switchTab(name) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + name).style.display = 'block';
    document.querySelectorAll('.tab-btn').forEach(btn => {
        if (btn.getAttribute('onclick').includes("'" + name + "'")) btn.classList.add('active');
    });
    history.replaceState(null, '', 'shopcard.php?tab=' + name);
}

// ── Accordion lịch sử đơn ────────────────────────────────────────────────────
function toggleOrder(i) {
    const body = document.getElementById('body-' + i);
    const hdr  = document.getElementById('hdr-' + i);
    const isOpen = body.classList.contains('open');
    body.classList.toggle('open', !isOpen);
    hdr.classList.toggle('open', !isOpen);
}

// ── Giỏ hàng ─────────────────────────────────────────────────────────────────
function toggleEditOrder(i) {
    const body = document.getElementById('body-' + i);
    const hdr  = document.getElementById('hdr-' + i);
    const edit = document.getElementById('edit-' + i);
    if (!body || !hdr || !edit) return;

    body.classList.add('open');
    hdr.classList.add('open');
    edit.classList.toggle('open');
    if (edit.classList.contains('open')) {
        setTimeout(() => edit.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 50);
    }
}
function showCheckout() {
    syncCartQtyToOrder();
    const s = document.getElementById('checkout-section');
    s.classList.add('active');
    setTimeout(() => s.scrollIntoView({ behavior: 'smooth', block: 'start' }), 50);
}
function onPaymentChange(radio) {
    document.getElementById('qr-box').style.display = (radio.value === 'ChuyenKhoan') ? 'block' : 'none';
    document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
    radio.closest('.payment-option').classList.add('selected');
}
function syncPersonalFields() {
    document.getElementById('h_ho_ten').value    = document.getElementById('f_ho_ten').value;
    document.getElementById('h_ngay_sinh').value = document.getElementById('f_ngay_sinh').value;
    document.getElementById('h_gioi_tinh').value = document.getElementById('f_gioi_tinh').value;
    document.getElementById('h_sdt').value       = document.getElementById('f_sdt').value;
    document.getElementById('h_dia_chi').value   = document.getElementById('f_dia_chi').value;
}
function formatVND(n) { return n.toLocaleString('vi-VN') + 'đ'; }
function clampQtyInput(input) {
    const min = parseInt(input.min) || 1;
    const max = parseInt(input.max) || 999;
    let qty = parseInt(input.value) || min;
    qty = Math.max(min, Math.min(qty, max));
    input.value = qty;
    return qty;
}
function productIdFromInputName(name) {
    const match = name.match(/\[(\d+)\]/);
    return match ? match[1] : '';
}
function syncCartQtyToOrder() {
    document.querySelectorAll('.row-qty').forEach(rowInput => {
        const qty = clampQtyInput(rowInput);
        const productId = productIdFromInputName(rowInput.name);
        const orderInput = document.querySelector('.order-qty[name="item_qty[' + productId + ']"]');
        if (orderInput) {
            orderInput.value = qty;
            clampQtyInput(orderInput);
        }
    });
    recalcCart();
    recalcOrder();
}
function recalcCart() {
    let total = 0;
    document.querySelectorAll('.row-qty').forEach(input => {
        const price = parseInt(input.dataset.price) || 0;
        const qty   = clampQtyInput(input);
        const sub   = price * qty; total += sub;
        const cell  = input.closest('tr').querySelector('.row-subtotal');
        if (cell) cell.textContent = formatVND(sub);
    });
    const el = document.getElementById('cart-grand-total');
    if (el) el.textContent = formatVND(total);
}
function recalcOrder() {
    let total = 0;
    document.querySelectorAll('.order-qty').forEach(input => {
        const price = parseInt(input.dataset.price) || 0;
        const qty   = clampQtyInput(input);
        const sub   = price * qty; total += sub;
        const cell  = input.closest('tr').querySelector('[data-order-subtotal]');
        if (cell) cell.textContent = formatVND(sub);
    });
    const el = document.getElementById('order-grand-total');
    if (el) el.textContent = formatVND(total);
}
document.querySelectorAll('.row-qty').forEach(i => {
    const handler = () => {
        recalcCart();
        const checkout = document.getElementById('checkout-section');
        if (checkout && checkout.classList.contains('active')) syncCartQtyToOrder();
    };
    i.addEventListener('input', handler);
    i.addEventListener('change', handler);
});
document.querySelectorAll('.order-qty').forEach(i => { i.addEventListener('input', recalcOrder); i.addEventListener('change', recalcOrder); });
const orderForm = document.getElementById('order-form');
if (orderForm) {
    orderForm.addEventListener('submit', function() {
        document.querySelectorAll('.order-qty').forEach(clampQtyInput);
        recalcOrder();
        syncPersonalFields();
    });
}
const shipInput = document.getElementById('inp_dia_chi_tt');
if (shipInput) shipInput.addEventListener('focus', function() { if (!this.value) this.value = document.getElementById('f_dia_chi').value || ''; });
<?php if (!empty($order_error)): ?>showCheckout();<?php endif; ?>
document.querySelectorAll('input[name="phuong_thuc"]').forEach(r => { if (r.checked) onPaymentChange(r); });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
