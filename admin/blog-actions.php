<?php

session_start();
require_once __DIR__ . '/../setting.php';
require_once __DIR__ . '/../includes/blog.php';
require_once __DIR__ . '/../includes/blog-admin.php';
require_once __DIR__ . '/../includes/admin-url.php';

blog_admin_require();

if (!($mysqli instanceof mysqli)) {
    exit('Database connection failed.');
}

blog_boot($mysqli);

if (!blog_admin_csrf_ok()) {
    blog_admin_set_flash(['The form expired. Submit it again.']);
    header('Location: ' . admin_url('posts'));
    exit;
}

$action = (string) ($_POST['action'] ?? '');

if ($action === 'save-category') {
    blog_save_category($mysqli);
}
if ($action === 'delete-category') {
    blog_delete_category($mysqli);
}
if ($action === 'save-post') {
    blog_save_post($mysqli);
}
if ($action === 'delete-post') {
    blog_delete_post($mysqli);
}

header('Location: ' . admin_url('dashboard'));
exit;
