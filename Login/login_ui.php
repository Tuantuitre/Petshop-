<?php
/**
 * login_ui.php
 * Giao diện đăng nhập.
 * Nếu URL có ?trigger_google=1 (đến từ trang đăng ký),
 * sẽ tự động kích hoạt Google Sign-In popup ngay khi SDK sẵn sàng.
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập – PetShop</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: url('assets/images/anhnenformdangnhap.png') center/cover no-repeat;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        body::before {
            content: ''; position: fixed; inset: 0;
            background: rgba(0,0,0,0.35);
        }
        .form {
            position: relative; z-index: 1;
            display: flex; flex-direction: column; gap: 10px;
            background-color: #ffffff; padding: 40px 36px;
            width: 480px; border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }
        .form-logo { text-align: center; margin-bottom: 8px; }
        .form-logo img { width: 64px; margin-bottom: 6px; }
        .form-logo h2 { font-size: 1.5rem; font-weight: 700; color: #151717; margin: 0; }
        .form-logo p  { color: #888; font-size: 0.9rem; margin: 4px 0 0; }
        ::placeholder { font-family: inherit; }
        .flex-column > label {
            color: #151717; font-weight: 600; display: block; margin-bottom: 4px;
        }
        .inputForm {
            border: 1.5px solid #ecedec; border-radius: 10px; height: 50px;
            display: flex; align-items: center; padding-left: 10px;
            transition: 0.2s ease-in-out;
        }
        .inputForm:focus-within { border: 1.5px solid #2d79f3; }
        .input {
            margin-left: 10px; border-radius: 10px; border: none;
            width: 100%; height: 100%; font-family: inherit; font-size: 0.97rem;
        }
        .input:focus { outline: none; }
        .flex-row {
            display: flex; flex-direction: row; align-items: center;
            gap: 10px; justify-content: space-between;
        }
        .flex-row > div { display: flex; align-items: center; gap: 6px; }
        .flex-row > div > label { font-size: 14px; color: black; font-weight: 400; }
        .span {
            font-size: 14px; margin-left: 5px; color: #2d79f3;
            font-weight: 500; cursor: pointer; text-decoration: none;
        }
        .span:hover { text-decoration: underline; }
        .button-submit {
            margin: 20px 0 10px 0; background-color: #151717;
            border: none; color: white; font-size: 15px; font-weight: 500;
            border-radius: 10px; height: 50px; width: 100%;
            cursor: pointer; transition: background 0.2s;
        }
        .button-submit:hover { background-color: #2d79f3; }
        .p { text-align: center; color: black; font-size: 14px; margin: 5px 0; }
        .p.line { display: flex; align-items: center; gap: 8px; color: #aaa; }
        .p.line::before, .p.line::after {
            content: ''; flex: 1; height: 1px; background: #ededef;
        }
        .btn {
            margin-top: 6px; width: 100%; height: 50px; border-radius: 10px;
            display: flex; justify-content: center; align-items: center;
            font-weight: 500; gap: 10px; border: 1px solid #ededef;
            background-color: white; cursor: pointer;
            transition: 0.2s ease-in-out; font-family: inherit; font-size: 0.95rem;
        }
        .btn:hover { border: 1px solid #2d79f3; }
        /* Highlight nút Google khi auto trigger */
        .btn.google-highlight {
            border-color: #2d79f3;
            box-shadow: 0 0 0 3px rgba(45,121,243,0.15);
            animation: pulse 1s ease-in-out 3;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 3px rgba(45,121,243,0.15); }
            50%       { box-shadow: 0 0 0 6px rgba(45,121,243,0.25); }
        }
        .error-msg {
            background: #fff0f0; border: 1px solid #ffb3b3; color: #cc0000;
            border-radius: 8px; padding: 10px 14px; font-size: 0.9rem;
            font-weight: 500; text-align: center;
        }
        /* Toast thông báo auto trigger */
        .google-toast {
            display: none;
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            z-index: 9999;
            background: #151717; color: #fff;
            padding: 12px 24px; border-radius: 30px;
            font-size: 0.9rem; font-weight: 500;
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
            gap: 10px; align-items: center;
            animation: slideDown 0.3s ease;
        }
        .google-toast.show { display: flex; }
        @keyframes slideDown {
            from { opacity: 0; transform: translateX(-50%) translateY(-10px); }
            to   { opacity: 1; transform: translateX(-50%) translateY(0); }
        }
        /* Modal nhập username (Google lần đầu) */
        .google-username-modal {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.6); z-index: 9999;
            align-items: center; justify-content: center;
        }
        .google-username-modal.active { display: flex; }
        .modal-box {
            background: #fff; border-radius: 20px; padding: 30px 28px;
            max-width: 400px; width: 90%; text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: popIn 0.3s ease;
        }
        @keyframes popIn {
            from { transform: scale(0.85); opacity: 0; }
            to   { transform: scale(1);    opacity: 1; }
        }
        .modal-box h3 { font-size: 1.3rem; color: #151717; margin-bottom: 10px; }
        .modal-box p  { font-size: 0.9rem; color: #666; margin-bottom: 20px; }
        .modal-box .modal-input {
            width: 100%; padding: 12px 14px; border: 1.5px solid #ecedec;
            border-radius: 10px; font-size: 1rem; margin-bottom: 15px;
            outline: none; transition: border-color 0.2s;
        }
        .modal-box .modal-input:focus { border-color: #2d79f3; }
        .modal-box .modal-submit {
            width: 100%; padding: 12px; background: #151717; color: #fff;
            border: none; border-radius: 10px; font-size: 1rem; font-weight: 600;
            cursor: pointer; transition: background 0.2s;
        }
        .modal-box .modal-submit:hover { background: #2d79f3; }
        .modal-box .modal-error {
            color: #cc0000; font-size: 0.85rem; margin-bottom: 10px; display: none;
        }
        @media (max-width: 520px) {
            .form { width: 95vw; padding: 28px 18px; }
        }
    </style>
</head>
<body>

<!-- Toast thông báo khi auto trigger từ trang đăng ký -->
<div class="google-toast" id="google-toast">
    <svg width="18" height="18" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
        <path d="M113.47,309.408L95.648,375.94l-65.139,1.378C11.042,341.211,0,299.9,0,256c0-42.451,10.324-82.483,28.624-117.732h0.014l57.992,10.632l25.404,57.644c-5.317,15.501-8.215,32.141-8.215,49.456C103.821,274.792,107.225,292.797,113.47,309.408z" style="fill:#FBBB00;"/>
        <path d="M507.527,208.176C510.467,223.662,512,239.655,512,256c0,18.328-1.927,36.206-5.598,53.451c-12.462,58.683-45.025,109.925-90.134,146.187l-0.014-0.014l-73.044-3.727l-10.338-64.535c29.932-17.554,53.324-45.025,65.646-77.911h-136.89V208.176h138.887L507.527,208.176z" style="fill:#518EF8;"/>
        <path d="M416.253,455.624l0.014,0.014C372.396,490.901,316.666,512,256,512c-97.491,0-182.252-54.491-225.491-134.681l82.961-67.91c21.619,57.698,77.278,98.771,142.53,98.771c28.047,0,54.323-7.582,76.87-20.818L416.253,455.624z" style="fill:#28B446;"/>
        <path d="M419.404,58.936l-82.933,67.896c-23.335-14.586-50.919-23.012-80.471-23.012c-66.729,0-123.429,42.957-143.965,102.724l-83.397-68.276h-0.014C71.23,56.123,157.06,0,256,0C318.115,0,375.068,22.126,419.404,58.936z" style="fill:#F14336;"/>
    </svg>
    Đang mở đăng nhập Google...
</div>

<form class="form" method="POST" action="dangnhap.php" id="login-form">
    <input type="hidden" name="login_normal" value="1">

    <div class="form-logo">
        <img src="assets/images/logo.png" alt="PetShop Logo"
             onerror="this.style.display='none'">
        <h2>🐾 PetShop</h2>
        <p>Đăng nhập để tiếp tục</p>
    </div>

    <?php if (!empty($error) || !empty($_SESSION['login_error'])): ?>
        <?php $displayError = $error ?: $_SESSION['login_error']; unset($_SESSION['login_error']); ?>
        <div class="error-msg">⚠️ <?= htmlspecialchars($displayError) ?></div>
    <?php endif; ?>

    <div class="flex-column"><label>Tên đăng nhập</label></div>
    <div class="inputForm">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="#aaa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
        </svg>
        <input class="input" type="text" name="username"
               placeholder="Nhập tên đăng nhập"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
               required autofocus>
    </div>

    <div class="flex-column"><label>Mật khẩu</label></div>
    <div class="inputForm">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="#aaa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        <input class="input" type="password" name="password"
               placeholder="Nhập mật khẩu" required>
    </div>

    <div class="flex-row">
        <div>
            <input type="checkbox" id="remember" name="remember">
            <label for="remember">Ghi nhớ đăng nhập</label>
        </div>
        <a href="#" class="span">Quên mật khẩu?</a>
    </div>

    <button class="button-submit" type="submit">Đăng nhập</button>

    <p class="p">Chưa có tài khoản? <a href="dangky.php" class="span">Đăng ký</a></p>

    <p class="p line">Hoặc đăng nhập với</p>

    <div style="display:flex; flex-direction:column; gap:8px;">
        <button type="button" class="btn" id="google-signin-btn">
            <svg width="20" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                <path d="M113.47,309.408L95.648,375.94l-65.139,1.378C11.042,341.211,0,299.9,0,256c0-42.451,10.324-82.483,28.624-117.732h0.014l57.992,10.632l25.404,57.644c-5.317,15.501-8.215,32.141-8.215,49.456C103.821,274.792,107.225,292.797,113.47,309.408z" style="fill:#FBBB00;"/>
                <path d="M507.527,208.176C510.467,223.662,512,239.655,512,256c0,18.328-1.927,36.206-5.598,53.451c-12.462,58.683-45.025,109.925-90.134,146.187l-0.014-0.014l-73.044-3.727l-10.338-64.535c29.932-17.554,53.324-45.025,65.646-77.911h-136.89V208.176h138.887L507.527,208.176z" style="fill:#518EF8;"/>
                <path d="M416.253,455.624l0.014,0.014C372.396,490.901,316.666,512,256,512c-97.491,0-182.252-54.491-225.491-134.681l82.961-67.91c21.619,57.698,77.278,98.771,142.53,98.771c28.047,0,54.323-7.582,76.87-20.818L416.253,455.624z" style="fill:#28B446;"/>
                <path d="M419.404,58.936l-82.933,67.896c-23.335-14.586-50.919-23.012-80.471-23.012c-66.729,0-123.429,42.957-143.965,102.724l-83.397-68.276h-0.014C71.23,56.123,157.06,0,256,0C318.115,0,375.068,22.126,419.404,58.936z" style="fill:#F14336;"/>
            </svg>
            Đăng nhập với Google
        </button>
        <button type="button" class="btn" id="apple-signin-btn">
            <svg width="20" height="20" viewBox="0 0 22.773 22.773" xmlns="http://www.w3.org/2000/svg">
                <g><g>
                    <path d="M15.769,0c0.053,0,0.106,0,0.162,0c0.13,1.606-0.483,2.806-1.228,3.675c-0.731,0.863-1.732,1.7-3.351,1.573c-0.108-1.583,0.506-2.694,1.25-3.561C13.292,0.879,14.557,0.16,15.769,0z"/>
                    <path d="M20.67,16.716c0,0.016,0,0.03,0,0.045c-0.455,1.378-1.104,2.559-1.896,3.655c-0.723,0.995-1.609,2.334-3.191,2.334c-1.367,0-2.275-0.879-3.676-0.903c-1.482-0.024-2.297,0.735-3.652,0.926c-0.155,0-0.31,0-0.462,0c-0.995-0.144-1.798-0.932-2.383-1.642c-1.725-2.098-3.058-4.808-3.306-8.276c0-0.34,0-0.679,0-1.019c0.105-2.482,1.311-4.5,2.914-5.478c0.846-0.52,2.009-0.963,3.304-0.765c0.555,0.086,1.122,0.276,1.619,0.464c0.471,0.181,1.06,0.502,1.618,0.485c0.378-0.011,0.754-0.208,1.135-0.347c1.116-0.403,2.21-0.865,3.652-0.648c1.733,0.262,2.963,1.032,3.723,2.22c-1.466,0.933-2.625,2.339-2.427,4.74C17.818,14.688,19.086,15.964,20.67,16.716z"/>
                </g></g>
            </svg>
            Đăng nhập với Apple
        </button>
    </div>
</form>

<!-- MODAL NHẬP TÊN ĐĂNG NHẬP (Google lần đầu) -->
<div class="google-username-modal" id="google-username-modal">
    <div class="modal-box">
        <h3>🐾 Chào mừng bạn!</h3>
        <p>Đây là lần đầu bạn đăng nhập bằng Google.<br>Hãy chọn một tên đăng nhập thật dễ nhớ nhé!</p>
        <p class="modal-error" id="modal-error"></p>
        <input type="text" class="modal-input" id="modal-username"
               placeholder="Nhập tên đăng nhập" autocomplete="off">
        <button type="button" class="modal-submit" id="modal-submit">Xác nhận & tiếp tục</button>
    </div>
</div>

<!-- ── Google SDK: dùng onload callback thay vì async/defer ── -->
<script>
const GOOGLE_CLIENT_ID = '478995794288-8rtr3d27ip8vmqcoi761oq3q47k8e39h.apps.googleusercontent.com';
let googleInitialized = false;
let googlePromptActive = false;

// ── Hàm khởi tạo và hiện popup Google ────────────────────────────────────────
function initAndPromptGoogle() {
    if (!window.google || !google.accounts || !google.accounts.id) return;

    if (!googleInitialized) {
        google.accounts.id.initialize({
            client_id: GOOGLE_CLIENT_ID,
            callback: handleGoogleCredential,
            auto_select: false,
            cancel_on_tap_outside: true
        });
        googleInitialized = true;
    }

    if (googlePromptActive) return;
    googlePromptActive = true;

    google.accounts.id.prompt(function() {
        googlePromptActive = false;
    });
}

// ── Callback nhận token từ Google ────────────────────────────────────────────
function handleGoogleCredential(response) {
    const token    = response.credential;
    const remember = document.getElementById('remember')?.checked ? '1' : '0';
    fetch('dangnhap.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'google_id_token=' + encodeURIComponent(token) + '&remember=' + remember
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'index.php';
        } else if (data.need_username) {
            showUsernameModal(token, remember);
        } else {
            alert(data.error || 'Đăng nhập Google thất bại');
        }
    })
    .catch(() => alert('Lỗi kết nối'));
}

// ── Modal nhập username (lần đầu) ────────────────────────────────────────────
function showUsernameModal(token, remember) {
    const modal     = document.getElementById('google-username-modal');
    const input     = document.getElementById('modal-username');
    const error     = document.getElementById('modal-error');
    const submitBtn = document.getElementById('modal-submit');

    modal.classList.add('active');
    input.value = '';
    error.style.display = 'none';

    submitBtn.onclick = function() {
        const username = input.value.trim();
        if (!username) {
            error.textContent    = 'Vui lòng nhập tên đăng nhập!';
            error.style.display  = 'block';
            return;
        }
        submitBtn.disabled    = true;
        submitBtn.textContent = 'Đang xử lý...';
        fetch('dangnhap.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'google_username=1&username=' + encodeURIComponent(username) + '&remember=' + remember
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'index.php';
            } else {
                error.textContent    = data.error || 'Lỗi khi tạo tài khoản';
                error.style.display  = 'block';
                submitBtn.disabled   = false;
                submitBtn.textContent = 'Xác nhận & tiếp tục';
            }
        })
        .catch(() => {
            error.textContent    = 'Lỗi kết nối!';
            error.style.display  = 'block';
            submitBtn.disabled   = false;
            submitBtn.textContent = 'Xác nhận & tiếp tục';
        });
    };
    input.addEventListener('keypress', e => { if (e.key === 'Enter') submitBtn.click(); });
}

// ── Hàm này được Google SDK gọi KHI SDK ĐÃ LOAD XONG ────────────────────────
// Đặt tên cố định để script Google callback vào đây
function onGoogleLibraryLoad() {
    const googleBtn = document.getElementById('google-signin-btn');
    const appleBtn  = document.getElementById('apple-signin-btn');

    // Gắn sự kiện click nút Google
    if (googleBtn) {
        googleBtn.addEventListener('click', function() {
            initAndPromptGoogle();
        });
    }

    // Apple chỉ báo đang phát triển
    if (appleBtn) {
        appleBtn.addEventListener('click', function() {
            alert('Chức năng đăng nhập Apple đang được phát triển.');
        });
    }

    // ── AUTO TRIGGER: nếu đến từ trang đăng ký ──────────────────────────────
    const params = new URLSearchParams(window.location.search);
    if (params.get('trigger_google') === '1') {
        // Hiện toast thông báo
        const toast = document.getElementById('google-toast');
        if (toast) toast.classList.add('show');

        // Highlight nút Google
        if (googleBtn) googleBtn.classList.add('google-highlight');

        // Trigger popup Google ngay lập tức vì SDK đã sẵn sàng
        setTimeout(function() {
            initAndPromptGoogle();
            // Ẩn toast sau 2 giây
            if (toast) setTimeout(() => toast.classList.remove('show'), 2000);
        }, 300); // delay nhỏ để page render xong
    }
}
</script>

<!--
    QUAN TRỌNG: Dùng onload= thay vì async/defer
    Để Google SDK gọi window.onGoogleLibraryLoad() khi load xong
-->
<script src="https://accounts.google.com/gsi/client" onload="onGoogleLibraryLoad()"></script>

</body>
</html>
