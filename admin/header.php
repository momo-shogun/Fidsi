<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include_once __DIR__ . '/../setting.php';
require_once __DIR__ . '/../includes/admin-url.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == '' || $_SESSION['user_id'] != 'FIDSI') {
    header('Location: ' . admin_url());
    exit;
}

$admin_current = admin_script_name();

$admin_page_titles = [
    'dashboard.php' => 'Dashboard',
    'contact.php'   => 'Contact Form',
    'join.php'      => 'Join Form',
    'subscribe.php' => 'Subscribe Form',
    'posts.php'     => 'Blog',
    'post-edit.php' => 'Blog',
    'categories.php' => 'Categories',
    'category-edit.php' => 'Category',
];

if (!isset($admin_page_title)) {
    $admin_page_title = $admin_page_titles[$admin_current] ?? 'FIDSI Admin';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($admin_page_title, ENT_QUOTES, 'UTF-8') ?> — FIDSI Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.2/css/dataTables.bootstrap5.min.css">
    <link href="/admin/css/admin.css" rel="stylesheet">
</head>
<body>
<div class="admin-shell">
    <div class="admin-sidebar-backdrop" id="adminSidebarBackdrop" hidden></div>
    <?php include_once __DIR__ . '/leftlink.php'; ?>
    <div class="admin-main">
        <header class="admin-topbar">
            <div class="admin-topbar-left">
                <button type="button" class="admin-menu-toggle" id="adminMenuToggle" aria-label="Open navigation" aria-controls="adminSidebar" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <h1 class="admin-page-title"><?= htmlspecialchars($admin_page_title, ENT_QUOTES, 'UTF-8') ?></h1>
            </div>
            <a href="<?= admin_url('logout') ?>" class="btn-admin btn-admin-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
                Log out
            </a>
        </header>
        <main class="admin-content">
