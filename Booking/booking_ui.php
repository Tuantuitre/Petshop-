<?php
/**
 * booking_ui.php
 * Chỉ chứa HTML, CSS, JS — không có logic, không có SQL.
 * Nhận biến từ booking_control.php:
 *   $booking_success  bool          đặt lịch thành công?
 *   $booking_error    string        thông báo lỗi (nếu có)
 *   $loai_list        LoaiThuCung[] danh sách loại thú cưng
 *   $dv_list          DichVu[]      danh sách dịch vụ
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
    --shadow-md:    0 8px 32px rgba(90,122,90,0.14);
    --trans:        0.25s cubic-bezier(.4,0,.2,1);
}
*, *::before, *::after { box-sizing: border-box; }

.booking-page {
    background: var(--cream);
    min-height: 100vh;
    font-family: 'DM Sans', sans-serif;
}

/* ── HERO ── */
.booking-hero {
    background: linear-gradient(135deg, #3d5c3d 0%, #5a7a5a 50%, #7a9e6a 100%);
    padding: 64px 24px 56px;
    text-align: center;
    position: relative; overflow: hidden;
}
.booking-hero::before {
    content: '📅';
    position: absolute; font-size: 200px; opacity: 0.06;
    top: -30px; right: -20px; pointer-events: none;
}
.booking-hero::after {
    content: '🐾';
    position: absolute; font-size: 160px; opacity: 0.05;
    bottom: -20px; left: 0; pointer-events: none;
}
.booking-hero h1 {
    font-family: 'Fraunces', serif;
    font-size: clamp(2rem, 5vw, 3.2rem);
    color: var(--white); letter-spacing: -0.5px; line-height: 1.2;
}
.booking-hero p {
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

/* ── CONTENT WRAP ── */
.booking-content {
    max-width: 1100px; margin: 0 auto;
    padding: 48px 24px 72px;
    display: grid;
    grid-template-columns: 1fr 290px;
    gap: 28px;
    align-items: start;
}
@media (max-width: 900px) {
    .booking-content { grid-template-columns: 1fr; }
    .booking-sidebar { order: -1; }
}

/* ── FORM CARD ── */
.form-card {
    background: var(--white);
    border-radius: var(--r);
    border: 1px solid var(--stone);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}
.form-card-header {
    padding: 18px 28px;
    border-bottom: 1px solid var(--stone);
    background: var(--cream);
    display: flex; align-items: center; gap: 10px;
}
.form-card-header h2 {
    font-family: 'Fraunces', serif;
    font-size: 1.15rem; color: var(--ink); margin: 0;
}
.form-card-body { padding: 28px 32px; }

/* ── FORM ROWS ── */
.form-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px; margin-bottom: 18px;
}
.form-row-1 { margin-bottom: 18px; }
@media (max-width: 600px) {
    .form-row-2 { grid-template-columns: 1fr; }
    .form-card-body { padding: 20px 18px; }
}

/* ── FORM GROUP ── */
.fg label {
    display: block;
    font-size: .78rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .7px;
    color: var(--ink); margin-bottom: 7px;
}
.fg label .req { color: #e63946; margin-left: 2px; }
.fg .form-control,
.fg .form-select {
    border: 1.5px solid var(--stone) !important;
    border-radius: var(--r-sm) !important;
    background: var(--cream) !important;
    font-family: 'DM Sans', sans-serif !important;
    font-size: .9rem !important;
    color: var(--ink) !important;
    padding: 11px 14px !important;
    height: auto !important;
    box-shadow: none !important;
    transition: border-color var(--trans), box-shadow var(--trans) !important;
    width: 100%;
}
.fg .form-control:focus,
.fg .form-select:focus {
    border-color: var(--sage-light) !important;
    box-shadow: 0 0 0 3px rgba(64,145,108,0.12) !important;
    background: var(--white) !important;
    outline: none !important;
}
.fg .form-control::placeholder { color: var(--muted) !important; }
.fg textarea.form-control { resize: vertical; min-height: 90px; line-height: 1.65; }

/* ── ALERT ── */
.alert-error {
    background: #fff3f3; border: 1px solid #f5c6c6;
    color: #c0392b; border-radius: var(--r-sm);
    padding: 14px 18px; margin-bottom: 20px;
    font-size: .9rem; font-weight: 500;
}

/* ── SUBMIT BTN ── */
.booking-submit-btn {
    width: 100%; padding: 14px;
    background: var(--sage); color: var(--white);
    border: none; border-radius: var(--r-sm);
    font-family: 'DM Sans', sans-serif;
    font-size: 1rem; font-weight: 600;
    cursor: pointer; margin-top: 6px;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    transition: background var(--trans), transform var(--trans), box-shadow var(--trans);
    box-shadow: 0 4px 16px rgba(45,106,79,0.22);
}
.booking-submit-btn:hover {
    background: var(--sage-light);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(45,106,79,0.28);
}
.booking-submit-btn:disabled { opacity: .6; cursor: not-allowed; transform: none; }

/* ── NOTE BOX ── */
.note-box {
    background: #fffbeb; border: 1.5px solid #fde68a;
    border-radius: var(--r-sm); padding: 16px 20px; margin-top: 20px;
}
.note-box p.note-title {
    font-weight: 700; font-size: .88rem;
    color: #92400e; margin-bottom: 8px;
    display: flex; align-items: center; gap: 6px;
}
.note-box ul { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 6px; }
.note-box ul li {
    font-size: .83rem; color: #78350f;
    display: flex; align-items: baseline; gap: 6px; line-height: 1.5;
}
.note-box ul li::before { content: '•'; color: var(--clay); font-weight: 700; flex-shrink: 0; }

/* ── SIDEBAR ── */
.booking-sidebar { display: flex; flex-direction: column; gap: 20px; }
.side-card {
    background: var(--white); border-radius: var(--r);
    border: 1px solid var(--stone); box-shadow: var(--shadow); overflow: hidden;
}
.side-card-header {
    padding: 14px 18px; border-bottom: 1px solid var(--stone);
    background: var(--cream); font-family: 'Fraunces', serif;
    font-size: .95rem; color: var(--ink);
    display: flex; align-items: center; gap: 8px;
}
.side-card-body { padding: 16px 18px; }
.step-list { list-style: none; padding: 0; margin: 0; }
.step-item {
    display: flex; gap: 12px; padding: 10px 0;
    border-bottom: 1px dashed var(--stone);
    font-size: .83rem; color: var(--muted); line-height: 1.5;
}
.step-item:last-child { border-bottom: none; }
.step-num {
    width: 22px; height: 22px; border-radius: 50%;
    background: var(--sage); color: var(--white);
    display: flex; align-items: center; justify-content: center;
    font-size: .7rem; font-weight: 700; flex-shrink: 0; margin-top: 2px;
}
.step-item strong { display: block; color: var(--ink); font-size: .85rem; }
.hours-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; }
.hours-list li { font-size: .84rem; color: var(--muted); display: flex; align-items: center; gap: 8px; }

/* ── CONFIRM SUMMARY ── */
.confirm-summary {
    background: var(--sage-pale); border: 1px solid #b8d4b8;
    border-radius: var(--r-sm); padding: 16px 20px; margin-top: 16px; font-size: .85rem;
}
.confirm-summary h4 { font-weight: 700; color: var(--sage); margin-bottom: 10px; font-size: .9rem; }
.confirm-summary .row {
    display: flex; justify-content: space-between;
    padding: 5px 0; border-bottom: 1px dashed #c8e0c8; color: var(--ink);
}
.confirm-summary .row:last-child { border-bottom: none; }
.confirm-summary .row span:first-child { color: var(--muted); }
.confirm-summary .row span:last-child { font-weight: 600; }
</style>

<?php if ($booking_success): ?>
<!-- POPUP THÀNH CÔNG -->
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:20px;padding:48px 44px;max-width:480px;width:90%;text-align:center;box-shadow:0 16px 50px rgba(0,0,0,0.2);animation:popIn .35s ease">
        <style>@keyframes popIn{from{transform:scale(.85);opacity:0}to{transform:scale(1);opacity:1}}</style>
        <div style="width:80px;height:80px;background:#27ae60;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:2.2rem;color:#fff">✓</div>
        <h3 style="font-family:'Fraunces',serif;font-size:1.4rem;font-weight:700;margin-bottom:12px;color:#1e1e1e">Đặt lịch thành công! 🎉</h3>
        <p style="color:#555;font-size:.95rem;line-height:1.7;margin-bottom:8px">
            Cảm ơn <strong><?= htmlspecialchars($_POST['ho_ten'] ?? '') ?></strong> đã tin tưởng PetShop!<br>
            Chúng tôi sẽ <strong>gọi điện xác nhận</strong> lịch hẹn trong vòng <strong>1–2 giờ</strong>.
        </p>
        <p style="color:#888;font-size:.83rem;margin-bottom:26px">📱 Vui lòng để ý điện thoại số <strong><?= htmlspecialchars($_POST['sdt'] ?? '') ?></strong></p>
        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
            <a href="booking.php" style="background:#5a7a5a;color:#fff;padding:11px 24px;border-radius:9px;text-decoration:none;font-weight:600;font-size:.9rem">📅 Đặt lịch khác</a>
            <a href="index.php"   style="background:#f4f0ea;color:#1e1e1e;padding:11px 24px;border-radius:9px;text-decoration:none;font-weight:600;font-size:.9rem">🏠 Trang chủ</a>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="booking-page">

    <div class="booking-hero">
        <h1>📅 Đặt Lịch Dịch Vụ</h1>
        <p>Đặt lịch chăm sóc thú cưng nhanh chóng, tiện lợi — chúng tôi xác nhận ngay!</p>
        <div class="hero-badges">
            <span class="hero-badge">⚡ Xác nhận nhanh</span>
            <span class="hero-badge">🔒 Miễn phí đặt lịch</span>
            <span class="hero-badge">💬 Hỗ trợ 24/7</span>
        </div>
    </div>

    <div class="booking-content">

        <!-- FORM -->
        <div class="form-card">
            <div class="form-card-header">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="var(--sage)" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <h2>Thông tin đặt lịch</h2>
            </div>
            <div class="form-card-body">

                <?php if ($booking_error): ?>
                <div class="alert-error">⚠️ <?= htmlspecialchars($booking_error) ?></div>
                <?php endif; ?>

                <form method="POST" action="booking.php" id="booking-form">
                    <input type="hidden" name="dat_lich" value="1">

                    <div class="form-row-2">
                        <div class="fg">
                            <label>Họ và tên <span class="req">*</span></label>
                            <input type="text" class="form-control" name="ho_ten"
                                   placeholder="Nguyễn Văn A" required
                                   value="<?= htmlspecialchars($_POST['ho_ten'] ?? '') ?>">
                        </div>
                        <div class="fg">
                            <label>Số điện thoại <span class="req">*</span></label>
                            <input type="tel" class="form-control" name="sdt"
                                   placeholder="0123 456 789" required
                                   pattern="[0-9]{9,11}" title="Số điện thoại 9-11 chữ số"
                                   value="<?= htmlspecialchars($_POST['sdt'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="fg">
                            <label>Email <span class="req">*</span></label>
                            <input type="email" class="form-control" name="email"
                                   placeholder="email@example.com" required
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="fg">
                            <label>Loại thú cưng <span class="req">*</span></label>
                            <select class="form-select" name="loai_thu_cung_id" required>
                                <option value="">-- Chọn loại --</option>
                                <?php foreach ($loai_list as $l): ?>
                                <option value="<?= $l->id ?>"
                                    <?= (($_POST['loai_thu_cung_id'] ?? '') == $l->id ? 'selected' : '') ?>>
                                    <?= htmlspecialchars($l->ten_loai) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="fg">
                            <label>Chọn dịch vụ <span class="req">*</span></label>
                            <select class="form-select" name="dich_vu_id" required>
                                <option value="">-- Chọn dịch vụ --</option>
                                <?php foreach ($dv_list as $dv): ?>
                                <option value="<?= $dv->id ?>"
                                    <?= (($_POST['dich_vu_id'] ?? '') == $dv->id ? 'selected' : '') ?>>
                                    <?= htmlspecialchars($dv->ten_dich_vu) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="fg">
                            <label>Ngày hẹn <span class="req">*</span></label>
                            <input type="date" class="form-control" name="ngay_hen" required
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                   value="<?= htmlspecialchars($_POST['ngay_hen'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="fg">
                            <label>Giờ hẹn <span class="req">*</span></label>
                            <input type="time" class="form-control" name="gio_hen" required
                                   min="08:00" max="18:00"
                                   value="<?= htmlspecialchars($_POST['gio_hen'] ?? '') ?>">
                        </div>
                        <div class="fg"><!-- spacer --></div>
                    </div>

                    <div class="form-row-1">
                        <div class="fg">
                            <label>Ghi chú</label>
                            <textarea class="form-control" name="ghi_chu" rows="3"
                                placeholder="Ghi chú thêm về thú cưng hoặc yêu cầu đặc biệt..."><?= htmlspecialchars($_POST['ghi_chu'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Preview xác nhận (JS tự điền) -->
                    <div class="confirm-summary" id="confirmSummary" style="display:none">
                        <h4>📋 Xác nhận thông tin đặt lịch</h4>
                        <div class="row"><span>Họ tên</span><span id="cs_name">—</span></div>
                        <div class="row"><span>Số điện thoại</span><span id="cs_phone">—</span></div>
                        <div class="row"><span>Dịch vụ</span><span id="cs_service">—</span></div>
                        <div class="row"><span>Ngày hẹn</span><span id="cs_date">—</span></div>
                        <div class="row"><span>Giờ hẹn</span><span id="cs_time">—</span></div>
                    </div>

                    <button type="submit" class="booking-submit-btn" id="submitBtn">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none"
                             stroke="currentColor" stroke-width="2.5"
                             stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Xác nhận đặt lịch
                    </button>

                    <div class="note-box">
                        <p class="note-title">⚠️ Lưu ý khi đặt lịch</p>
                        <ul>
                            <li>Vui lòng đặt lịch trước ít nhất 24 giờ</li>
                            <li>Chúng tôi sẽ liên hệ để xác nhận hẹn trong 1–2 giờ</li>
                            <li>Mang theo sổ tiêm chủng của thú cưng (nếu có)</li>
                            <li>Giờ phục vụ: 8:00 – 18:00 các ngày trong tuần</li>
                        </ul>
                    </div>

                </form>
            </div>
        </div>

        <!-- SIDEBAR -->
        <aside class="booking-sidebar">
            <div class="side-card">
                <div class="side-card-header">🔄 Quy trình đặt lịch</div>
                <div class="side-card-body">
                    <ol class="step-list">
                        <li class="step-item">
                            <span class="step-num">1</span>
                            <div><strong>Điền thông tin</strong>Nhập đầy đủ thông tin và chọn dịch vụ</div>
                        </li>
                        <li class="step-item">
                            <span class="step-num">2</span>
                            <div><strong>Xác nhận</strong>Nhân viên gọi điện xác nhận trong 1–2 giờ</div>
                        </li>
                        <li class="step-item">
                            <span class="step-num">3</span>
                            <div><strong>Đến cửa hàng</strong>Mang thú cưng đến đúng giờ hẹn</div>
                        </li>
                        <li class="step-item">
                            <span class="step-num">4</span>
                            <div><strong>Hoàn tất</strong>Nhận thú cưng sạch sẽ, khỏe mạnh 🐾</div>
                        </li>
                    </ol>
                </div>
            </div>

            <div class="side-card">
                <div class="side-card-header">🕐 Giờ làm việc</div>
                <div class="side-card-body">
                    <ul class="hours-list">
                        <li><span>📅</span> Thứ 2 – Thứ 6: 8:00 – 18:00</li>
                        <li><span>📅</span> Thứ 7 – CN: 8:00 – 16:00</li>
                        <li><span>📞</span> Hotline: 0123 456 789</li>
                    </ul>
                </div>
            </div>

            <div class="side-card">
                <div class="side-card-header">✨ Dịch vụ của chúng tôi</div>
                <div class="side-card-body">
                    <ul class="hours-list">
                        <?php foreach ($dv_list as $dv): ?>
                        <li><span>🐾</span> <?= htmlspecialchars($dv->ten_dich_vu) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </aside>

    </div>
</div>

<script>
const form    = document.getElementById('booking-form');
const summary = document.getElementById('confirmSummary');

function updateSummary() {
    const name    = form.querySelector('[name="ho_ten"]').value;
    const phone   = form.querySelector('[name="sdt"]').value;
    const service = form.querySelector('[name="dich_vu_id"]');
    const date    = form.querySelector('[name="ngay_hen"]').value;
    const time    = form.querySelector('[name="gio_hen"]').value;

    const serviceName   = service.options[service.selectedIndex]?.text || '—';
    const dateFormatted = date ? new Date(date).toLocaleDateString('vi-VN') : '—';

    if (name && phone && date && time && service.value) {
        document.getElementById('cs_name').textContent    = name;
        document.getElementById('cs_phone').textContent   = phone;
        document.getElementById('cs_service').textContent = serviceName;
        document.getElementById('cs_date').textContent    = dateFormatted;
        document.getElementById('cs_time').textContent    = time;
        summary.style.display = 'block';
    } else {
        summary.style.display = 'none';
    }
}

['ho_ten','sdt','dich_vu_id','ngay_hen','gio_hen'].forEach(name => {
    const el = form.querySelector(`[name="${name}"]`);
    if (el) { el.addEventListener('change', updateSummary); el.addEventListener('input', updateSummary); }
});

form.addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round"
             style="animation:spin 1s linear infinite">
            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
        </svg>
        Đang gửi...
    `;
});
</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>