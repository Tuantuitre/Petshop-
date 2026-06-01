<?php
/**
 * register_ui.php
 * Toàn bộ HTML + CSS + JS giao diện đăng ký.
 * Nhận biến từ register_control.php:
 *   $success  bool    đăng ký thành công?
 *   $error    string  thông báo lỗi
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký – PetShop</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('assets/images/anhnenformdangnhap.png') center/cover no-repeat;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.35);
        }

        .form {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background-color: #ffffff;
            padding: 40px 36px;
            width: 480px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        }
        .form-logo { text-align: center; margin-bottom: 8px; }
        .form-logo img { width: 64px; margin-bottom: 6px; }
        .form-logo h2 { font-size: 1.5rem; font-weight: 700; color: #151717; margin: 0; }
        .form-logo p  { color: #888; font-size: 0.9rem; margin: 4px 0 0; }

        ::placeholder { font-family: inherit; }

        .flex-column > label {
            color: #151717; font-weight: 600;
            display: block; margin-bottom: 4px;
        }
        .inputForm {
            border: 1.5px solid #ecedec;
            border-radius: 10px; height: 50px;
            display: flex; align-items: center;
            padding-left: 10px;
            transition: 0.2s ease-in-out;
        }
        .inputForm:focus-within { border: 1.5px solid #2d79f3; }
        .inputForm.error-field  { border: 1.5px solid #ff4d4d; }
        .input {
            margin-left: 10px; border-radius: 10px;
            border: none; width: 100%; height: 100%;
            font-family: inherit; font-size: 0.97rem;
        }
        .input:focus { outline: none; }

        .flex-row {
            display: flex; align-items: center;
            justify-content: center; gap: 6px;
        }
        .span {
            font-size: 14px; color: #2d79f3;
            font-weight: 500; cursor: pointer; text-decoration: none;
        }
        .span:hover { text-decoration: underline; }

        .button-submit {
            margin: 16px 0 10px 0;
            background-color: #151717;
            border: none; color: white;
            font-size: 15px; font-weight: 500;
            border-radius: 10px; height: 50px;
            width: 100%; cursor: pointer;
            transition: background 0.2s;
        }
        .button-submit:hover { background-color: #2d79f3; }

        .p { text-align: center; color: black; font-size: 14px; margin: 5px 0; }
        .p.line {
            display: flex; align-items: center;
            gap: 8px; color: #aaa;
        }
        .p.line::before, .p.line::after {
            content: ''; flex: 1;
            height: 1px; background: #ededef;
        }

        .alert-error {
            background: #fff0f0; border: 1px solid #ffb3b3;
            color: #cc0000; border-radius: 8px;
            padding: 10px 14px; font-size: 0.9rem;
            font-weight: 500; text-align: center;
        }
        .alert-success {
            background: #f0fff4; border: 1px solid #b3ffcc;
            color: #007a33; border-radius: 8px;
            padding: 10px 14px; font-size: 0.9rem;
            font-weight: 500; text-align: center;
        }
        .hint {
            font-size: 0.78rem; color: #aaa;
            margin-top: -4px; padding-left: 2px;
        }

        .btn {
            margin-top: 6px; width: 100%; height: 50px;
            border-radius: 10px;
            display: flex; justify-content: center;
            align-items: center; font-weight: 500;
            gap: 10px; border: 1px solid #ededef;
            background-color: white; cursor: pointer;
            transition: 0.2s ease-in-out;
            font-family: inherit; font-size: 0.95rem;
        }
        .btn:hover { border: 1px solid #2d79f3; }

        /* Loading overlay khi redirect Google */
        .redirect-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            align-items: center; justify-content: center;
            flex-direction: column; gap: 16px;
        }
        .redirect-overlay.show { display: flex; }
        .redirect-overlay p {
            color: #fff; font-size: 1rem; font-weight: 600;
        }
        .spinner {
            width: 40px; height: 40px;
            border: 4px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (max-width: 520px) {
            .form { width: 95vw; padding: 28px 18px; }
        }
    </style>
</head>
<body>

<!-- Loading overlay khi redirect sang login để dùng Google -->
<div class="redirect-overlay" id="redirect-overlay">
    <div class="spinner"></div>
    <p>Đang chuyển đến trang đăng nhập Google...</p>
</div>

<form class="form" method="POST" action="dangky.php">

    <div class="form-logo">
        <img src="assets/images/logo.png" alt="PetShop Logo"
             onerror="this.style.display='none'">
        <h2>🐾 PetShop</h2>
        <p>Tạo tài khoản mới</p>
    </div>

    <?php if ($success): ?>
        <div class="alert-success">
            ✅ Đăng ký thành công! <a href="dangnhap.php" class="span">Đăng nhập ngay</a>
        </div>
    <?php elseif ($error): ?>
        <div class="alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Username -->
    <div class="flex-column">
        <label>Tên đăng nhập</label>
    </div>
    <div class="inputForm <?= ($error && empty($_POST['username'])) ? 'error-field' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
             fill="none" stroke="#aaa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
        </svg>
        <input class="input" type="text" name="username"
               placeholder="Tên đăng nhập của bạn"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
               required autofocus>
    </div>

    <!-- Email -->
    <div class="flex-column">
        <label>Email</label>
    </div>
    <div class="inputForm">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 32 32" fill="#aaa">
            <path d="m30.853 13.87a15 15 0 0 0 -29.729 4.082 15.1 15.1 0 0 0 12.876 12.918 15.6 15.6 0 0 0 2.016.13 14.85 14.85 0 0 0 7.715-2.145 1 1 0 1 0 -1.031-1.711 13.007 13.007 0 1 1 5.458-6.529 2.149 2.149 0 0 1 -4.158-.759v-10.856a1 1 0 0 0 -2 0v1.726a8 8 0 1 0 .2 10.325 4.135 4.135 0 0 0 7.83.274 15.2 15.2 0 0 0 .823-7.455zm-14.853 8.13a6 6 0 1 1 6-6 6.006 6.006 0 0 1 -6 6z"/>
        </svg>
        <input class="input" type="email" name="email"
               placeholder="email@example.com"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               required>
    </div>

    <!-- Password -->
    <div class="flex-column">
        <label>Mật khẩu</label>
    </div>
    <div class="inputForm">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="-64 0 512 512" fill="#aaa">
            <path d="M336 512H48C21.5 512 0 490.5 0 464V240c0-26.5 21.5-48 48-48h288c26.5 0 48 21.5 48 48v224c0 26.5-21.5 48-48 48zm-288-288c-8.8 0-16 7.2-16 16v224c0 8.8 7.2 16 16 16h288c8.8 0 16-7.2 16-16V240c0-8.8-7.2-16-16-16zm0 0"/>
            <path d="M304 224c-8.8 0-16-7.2-16-16v-80c0-52.9-43.1-96-96-96s-96 43.1-96 96v80c0 8.8-7.2 16-16 16s-16-7.2-16-16v-80C64 57.4 121.4 0 192 0s128 57.4 128 128v80c0 8.8-7.2 16-16 16zm0 0"/>
        </svg>
        <input class="input" type="password" name="password"
               placeholder="Ít nhất 6 ký tự" required>
    </div>
    <p class="hint">* Mật khẩu phải có ít nhất 6 ký tự</p>

    <!-- Confirm Password -->
    <div class="flex-column">
        <label>Xác nhận mật khẩu</label>
    </div>
    <div class="inputForm">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="-64 0 512 512" fill="#aaa">
            <path d="M336 512H48C21.5 512 0 490.5 0 464V240c0-26.5 21.5-48 48-48h288c26.5 0 48 21.5 48 48v224c0 26.5-21.5 48-48 48zm-288-288c-8.8 0-16 7.2-16 16v224c0 8.8 7.2 16 16 16h288c8.8 0 16-7.2 16-16V240c0-8.8-7.2-16-16-16zm0 0"/>
            <path d="M304 224c-8.8 0-16-7.2-16-16v-80c0-52.9-43.1-96-96-96s-96 43.1-96 96v80c0 8.8-7.2 16-16 16s-16-7.2-16-16v-80C64 57.4 121.4 0 192 0s128 57.4 128 128v80c0 8.8-7.2 16-16 16zm0 0"/>
        </svg>
        <input class="input" type="password" name="repassword"
               placeholder="Nhập lại mật khẩu" required>
    </div>

    <!-- Submit -->
    <button class="button-submit" type="submit">Đăng ký</button>

    <p class="p">Đã có tài khoản?
        <a href="dangnhap.php" class="span">Đăng nhập</a>
    </p>

    <p class="p line">Hoặc đăng ký với</p>

    <div style="display:flex; flex-direction:column; gap:8px;">
        <!-- Nút Google: redirect sang dangnhap.php?trigger_google=1 -->
        <button type="button" class="btn" id="register-google-btn">
            <svg width="20" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                <path d="M113.47,309.408L95.648,375.94l-65.139,1.378C11.042,341.211,0,299.9,0,256c0-42.451,10.324-82.483,28.624-117.732h0.014l57.992,10.632l25.404,57.644c-5.317,15.501-8.215,32.141-8.215,49.456C103.821,274.792,107.225,292.797,113.47,309.408z" style="fill:#FBBB00;"/>
                <path d="M507.527,208.176C510.467,223.662,512,239.655,512,256c0,18.328-1.927,36.206-5.598,53.451c-12.462,58.683-45.025,109.925-90.134,146.187l-0.014-0.014l-73.044-3.727l-10.338-64.535c29.932-17.554,53.324-45.025,65.646-77.911h-136.89V208.176h138.887L507.527,208.176z" style="fill:#518EF8;"/>
                <path d="M416.253,455.624l0.014,0.014C372.396,490.901,316.666,512,256,512c-97.491,0-182.252-54.491-225.491-134.681l82.961-67.91c21.619,57.698,77.278,98.771,142.53,98.771c28.047,0,54.323-7.582,76.87-20.818L416.253,455.624z" style="fill:#28B446;"/>
                <path d="M419.404,58.936l-82.933,67.896c-23.335-14.586-50.919-23.012-80.471-23.012c-66.729,0-123.429,42.957-143.965,102.724l-83.397-68.276h-0.014C71.23,56.123,157.06,0,256,0C318.115,0,375.068,22.126,419.404,58.936z" style="fill:#F14336;"/>
            </svg>
            Đăng ký với Google
        </button>

        <button type="button" class="btn" id="register-apple-btn">
            <svg width="20" height="20" viewBox="0 0 22.773 22.773" xmlns="http://www.w3.org/2000/svg">
                <g><g>
                    <path d="M15.769,0c0.053,0,0.106,0,0.162,0c0.13,1.606-0.483,2.806-1.228,3.675c-0.731,0.863-1.732,1.7-3.351,1.573c-0.108-1.583,0.506-2.694,1.25-3.561C13.292,0.879,14.557,0.16,15.769,0z"/>
                    <path d="M20.67,16.716c0,0.016,0,0.03,0,0.045c-0.455,1.378-1.104,2.559-1.896,3.655c-0.723,0.995-1.609,2.334-3.191,2.334c-1.367,0-2.275-0.879-3.676-0.903c-1.482-0.024-2.297,0.735-3.652,0.926c-0.155,0-0.31,0-0.462,0c-0.995-0.144-1.798-0.932-2.383-1.642c-1.725-2.098-3.058-4.808-3.306-8.276c0-0.34,0-0.679,0-1.019c0.105-2.482,1.311-4.5,2.914-5.478c0.846-0.52,2.009-0.963,3.304-0.765c0.555,0.086,1.122,0.276,1.619,0.464c0.471,0.181,1.06,0.502,1.618,0.485c0.378-0.011,0.754-0.208,1.135-0.347c1.116-0.403,2.21-0.865,3.652-0.648c1.733,0.262,2.963,1.032,3.723,2.22c-1.466,0.933-2.625,2.339-2.427,4.74C17.818,14.688,19.086,15.964,20.67,16.716z"/>
                </g></g>
            </svg>
            Đăng ký với Apple
        </button>
    </div>

</form>

<script>
// ── Nút Google: redirect sang dangnhap.php và tự trigger Google Sign-In ───────
document.getElementById('register-google-btn').addEventListener('click', function() {
    // Hiện loading overlay
    document.getElementById('redirect-overlay').classList.add('show');
    // Chuyển sang trang login với param trigger_google=1
    // login_ui.php sẽ đọc param này và tự click nút Google
    setTimeout(function() {
        window.location.href = 'dangnhap.php?trigger_google=1';
    }, 600);
});

// ── Nút Apple ────────────────────────────────────────────────────────────────
document.getElementById('register-apple-btn').addEventListener('click', function() {
    alert('Chức năng đăng ký Apple đang được phát triển.');
});
</script>

</body>
</html>