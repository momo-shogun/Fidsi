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

$cleanRoutes = [
    '/about-us' => 'about-us.php',
    '/governing-board' => 'governingboard.php',
    '/meet-our-team' => 'meet-our-team.php',
    '/members' => 'members.php',
    '/code-of-conduct' => 'code-of-conduct.php',
    '/regulations-and-compliance' => 'regulations-and-compliance.php',
    '/government-industry-collaboration' => 'government-industry-collaboration.php',
    '/news' => 'news.php',
    '/gallery' => 'gallery.php',
    '/direct-selling-industry-report' => 'ds-industry-report.php',
    '/contact-us' => 'contact-us.php',
    '/join-fidsi' => 'joinfidsi.php',
    '/newsletter' => 'newsletter.php',
    '/privacy-policy' => 'privacy-policy.php',
];

$phpRedirects = [
    '/index.php' => '/',
    '/about-us.php' => '/about-us',
    '/governingboard.php' => '/governing-board',
    '/meet-our-team.php' => '/meet-our-team',
    '/members.php' => '/members',
    '/code-of-conduct.php' => '/code-of-conduct',
    '/regulationsandcompliance.php' => '/regulations-and-compliance',
    '/regulations-and-compliance.php' => '/regulations-and-compliance',
    '/government-industry-collaboration.php' => '/government-industry-collaboration',
    '/news.php' => '/news',
    '/gallery.php' => '/gallery',
    '/ds-industry-report.php' => '/direct-selling-industry-report',
    '/contact-us.php' => '/contact-us',
    '/joinfidsi.php' => '/join-fidsi',
    '/newsletter.php' => '/newsletter',
    '/privacy-policy.php' => '/privacy-policy',
];

$normalized = rtrim($uri, '/') ?: '/';

if (($method === 'GET' || $method === 'HEAD') && isset($phpRedirects[$uri])) {
    $target = $phpRedirects[$uri];
    if (is_string($query) && $query !== '') {
        $target .= '?' . $query;
    }
    header('Location: ' . $target, true, 301);
    return true;
}

if (isset($cleanRoutes[$normalized])) {
    $file = __DIR__ . '/' . $cleanRoutes[$normalized];
    if (is_file($file)) {
        require $file;
        return true;
    }
}

return false;
