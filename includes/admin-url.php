<?php

function admin_url(string $page = ''): string
{
    $page = preg_replace('/\.php$/', '', $page) ?? '';
    $page = trim($page, '/');
    if ($page === '' || $page === 'index') {
        return '/admin';
    }
    return '/admin/' . $page;
}

function admin_script_name(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $base = basename((string) $path);
    if ($base === '' || $base === 'admin') {
        return 'index.php';
    }
    if (!str_ends_with($base, '.php')) {
        $base .= '.php';
    }
    return $base;
}
