<?php
session_start();
include_once __DIR__ . '/../setting.php';

if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === 'FIDSI') {
    header('Location: /admin/dashboard');
    exit;
}

$error = false;

if (isset($_POST['submit'])) {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($user == "fidsi@gmail.com" && $pass == "123456") {
        $_SESSION['user_id'] = "FIDSI";
        header('Location: /admin/dashboard');
        exit;
    }

    $error = true;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FIDSI Admin — Sign in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="/admin/css/admin.css" rel="stylesheet">
</head>
<body class="login-page">
    <main class="login-card">
        <div class="login-brand">
            <img src="/assets/images/logo/Fidsi-logo-black.png" alt="FIDSI">
            <h1>FIDSI Admin</h1>
            <p>Sign in to manage form submissions</p>
        </div>

        <?php if ($error): ?>
        <div class="login-alert" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            <span>Invalid email or password. Please try again.</span>
        </div>
        <?php endif; ?>

        <form method="post" class="login-form" novalidate>
            <div class="form-group">
                <label for="floatingInput">Email address</label>
                <input
                    type="email"
                    class="form-control"
                    id="floatingInput"
                    name="username"
                    placeholder="name@example.com"
                    autocomplete="username"
                    required
                    value="<?= $error ? htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') : '' ?>"
                >
            </div>
            <div class="form-group">
                <label for="floatingPassword">Password</label>
                <input
                    type="password"
                    class="form-control"
                    id="floatingPassword"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >
            </div>
            <button class="btn-admin btn-admin-primary btn-admin-lg" type="submit" name="submit">Sign in</button>
        </form>
    </main>
</body>
</html>
