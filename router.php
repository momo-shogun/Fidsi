<?php

if (PHP_SAPI !== 'cli-server') {
    return false;
}

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$query = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($uri === '/sitemap.xml') {
    require __DIR__ . '/sitemap.php';
    return true;
}

if ($uri === '/blog' || str_starts_with($uri, '/blog/')) {
    require __DIR__ . '/blog.php';
    return true;
}

if (preg_match('#^/admin/([A-Za-z0-9-]+)\.php$#', $uri, $matches) === 1) {
    if ($method === 'GET' || $method === 'HEAD') {
        $target = $matches[1] === 'index' ? '/admin' : '/admin/' . $matches[1];
        if (is_string($query) && $query !== '') {
            $target .= '?' . $query;
        }
        header('Location: ' . $target, true, 301);
        return true;
    }
    $file = __DIR__ . '/admin/' . $matches[1] . '.php';
    if (is_file($file)) {
        require $file;
        return true;
    }
}

if ($uri === '/admin' || $uri === '/admin/') {
    require __DIR__ . '/admin/index.php';
    return true;
}

if (preg_match('#^/admin/([A-Za-z0-9-]+)/?$#', $uri, $matches) === 1) {
    $file = __DIR__ . '/admin/' . $matches[1] . '.php';
    if (is_file($file)) {
        require $file;
        return true;
    }
}

return false;
