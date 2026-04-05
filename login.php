<?php
ob_start();
session_start();
require_once 'config.php';

$error = "";

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Please enter username and password.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {

                // ✅ SESSION DATA
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // 🔥 ROLE FIX (ITO ANG IMPORTANT)
                $_SESSION['role'] = strtolower(trim($user['role']));

                // optional (pang display sa sidebar)
                $_SESSION['full_name'] = $user['full_name'] ?? $user['username'];

                header("Location: dashboard.php");
                exit();

            } else {
                $error = "Invalid login credentials.";
            }

        } catch (PDOException $e) {
            $error = "Login error.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EnSLP IMS | Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    margin: 0;
    height: 100vh;
    font-family: 'Segoe UI', sans-serif;
}

/* LAYOUT */
.container-main {
    display: flex;
    height: 100vh;
}

/* LEFT SIDE */
.left {
    flex: 1;
    background: linear-gradient(135deg, #0a1f44, #0d3b8c);
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 40px;
    position: relative;
}

/* subtle glow circles */
.left::before {
    content: "";
    position: absolute;
    width: 400px;
    height: 400px;
    background: rgba(255,255,255,0.03);
    border-radius: 50%;
    top: -100px;
    left: -100px;
}

.left::after {
    content: "";
    position: absolute;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.03);
    border-radius: 50%;
    bottom: -80px;
    right: -80px;
}

.left img {
    width: 100px;
    background: white;
    padding: 15px;
    border-radius: 20px;
    margin-bottom: 20px;
    z-index: 2;
}

.left h1 {
    font-weight: 700;
    font-size: 38px;
    z-index: 2;
}

.left p {
    opacity: 0.8;
    max-width: 400px;
    font-size: 14px;
    z-index: 2;
}

.footer-text {
    position: absolute;
    bottom: 20px;
    font-size: 12px;
    opacity: 0.6;
}

/* RIGHT SIDE */
.right {
    flex: 1;
    background: #f5f7fb;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* LOGIN CARD */
.login-card {
    width: 100%;
    max-width: 380px;
    background: white;
    padding: 35px;
    border-radius: 15px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
}

.login-card h4 {
    font-weight: bold;
}

/* INPUT */
.form-control {
    height: 45px;
    border-radius: 8px;
}

/* BUTTON */
.btn-primary {
    background: #0d3b8c;
    border: none;
    height: 45px;
    border-radius: 8px;
    font-weight: bold;
}

.btn-primary:hover {
    background: #0a2e6e;
}
</style>
</head>

<body>

<div class="container-main">

    <!-- LEFT -->
    <div class="left">
        <img src="logo.png">
        <h1>EnSLP Inc.</h1>
        <p>A smart system designed to manage FFC production, ensure workflow efficiency, and maintain consistent manufacturing quality.</p>

        <div class="footer-text">
        © 2026 EnSLP Inc. || Lot 1 Blk 1 Science Park III, Sto. Tomas City Batangas, Philippines
        </div>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <div class="login-card">

            <h4>Login</h4>
            <small class="text-muted">Welcome back! Please sign in to your account.</small>

            <?php if ($error): ?>
                <div class="alert alert-danger small mt-3">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="mt-3">

                <div class="mb-3">
                    <label class="small fw-bold">USERNAME</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold">PASSWORD</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" name="login" class="btn btn-primary w-100 mt-2">
                    SIGN IN
                </button>

                <div class="text-center mt-3" style="font-size:12px; opacity:.6;">
                    SYSTEM VERSION 2.0.1
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>