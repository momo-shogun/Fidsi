<?php

const BLOG_SITE_URL = 'https://fidsi.co.in';
const BLOG_ORG_NAME = 'Federation of Indian Direct Selling Industries (FIDSI)';
const BLOG_ORG_ID = 'https://fidsi.co.in/#organization';
const BLOG_ORG_LOGO = 'https://fidsi.co.in/assets/images/logo/Fidsi-logo-black.png';
const BLOG_PER_PAGE = 12;
const BLOG_HOME_INTRO = 'Notes and updates from the Federation of Indian Direct Selling Industries.';

function blog_e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function blog_boot(mysqli $db): void
{
    static $ready = false;
    if ($ready) {
        return;
    }
    $db->set_charset('utf8mb4');
    $ready = true;
}

function blog_tables_ready(mysqli $db): bool
{
    static $ready = null;
    if ($ready !== null) {
        return $ready;
    }
    $result = $db->query("SHOW TABLES LIKE 'blog_posts'");
    $ready = $result instanceof mysqli_result && $result->num_rows > 0;
    return $ready;
}

function blog_slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    $text = trim($text, '-');
    if (strlen($text) > 180) {
        $text = rtrim(substr($text, 0, 180), '-');
    }
    return $text;
}

function blog_absolute(string $path): string
{
    if ($path === '' || $path === '/') {
        return BLOG_SITE_URL . '/';
    }
    return BLOG_SITE_URL . '/' . ltrim($path, '/');
}

function blog_public_where(string $alias = 'p'): string
{
    return $alias . ".status <> 'draft' AND " . $alias . '.published_at IS NOT NULL AND ' . $alias . '.published_at <= NOW()';
}

function blog_db_now(mysqli $db): DateTimeImmutable
{
    $result = $db->query('SELECT NOW() AS now_time');
    $row = $result ? $result->fetch_assoc() : null;
    $value = $row['now_time'] ?? date('Y-m-d H:i:s');
    return new DateTimeImmutable($value);
}

function blog_format_mysql(DateTimeImmutable $dt): string
{
    return $dt->format('Y-m-d H:i:s');
}

function blog_iso_from_mysql(?string $value): string
{
    if ($value === null || $value === '') {
        return '';
    }
    return (new DateTimeImmutable($value))->format('c');
}

function blog_modified_mysql(array $post): string
{
    $modified = (string) ($post['content_modified_at'] ?? '');
    $published = (string) ($post['published_at'] ?? '');
    if ($published === '') {
        return $modified;
    }
    if ($modified === '') {
        return $published;
    }
    return $modified > $published ? $modified : $published;
}

function blog_display_date(?string $value): string
{
    if ($value === null || $value === '') {
        return '';
    }
    return (new DateTimeImmutable($value))->format('j M Y');
}

function blog_post_path(array $post): string
{
    return '/blog/' . $post['category_slug'] . '/' . $post['slug'];
}

function blog_category_path(string $slug, int $page = 1): string
{
    if ($page <= 1) {
        return '/blog/' . $slug;
    }
    return '/blog/' . $slug . '/page/' . $page;
}

function blog_home_path(int $page = 1): string
{
    if ($page <= 1) {
        return '/blog';
    }
    return '/blog/page/' . $page;
}

function blog_request_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $path = trim((string) $path, '/');
    return $path;
}

function blog_parse_request(): array
{
    $path = blog_request_path();

    if ($path === 'blog' || $path === 'blog.php') {
        $page = isset($_GET['blog_page']) ? (int) $_GET['blog_page'] : 1;
        return ['kind' => 'home', 'page' => $page, 'path' => $path];
    }
    if (preg_match('#^blog/page/(\d+)$#', $path, $matches) === 1) {
        return ['kind' => 'home', 'page' => (int) $matches[1], 'path' => $path];
    }
    if (preg_match('#^blog/([a-z0-9-]+)/page/(\d+)$#', $path, $matches) === 1) {
        return [
            'kind' => 'category',
            'category' => $matches[1],
            'page' => (int) $matches[2],
            'path' => $path,
        ];
    }
    if (preg_match('#^blog/([a-z0-9-]+)/([a-z0-9-]+)$#', $path, $matches) === 1) {
        return [
            'kind' => 'post',
            'category' => $matches[1],
            'slug' => $matches[2],
            'path' => $path,
        ];
    }
    if (preg_match('#^blog/([a-z0-9-]+)$#', $path, $matches) === 1) {
        return [
            'kind' => 'category',
            'category' => $matches[1],
            'page' => 1,
            'path' => $path,
        ];
    }

    return ['kind' => 'missing', 'path' => $path];
}

function blog_fetch_all(mysqli_stmt $stmt): array
{
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        return [];
    }
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function blog_fetch_one(mysqli_stmt $stmt): ?array
{
    $rows = blog_fetch_all($stmt);
    return $rows[0] ?? null;
}

function blog_post_select(): string
{
    return 'SELECT p.*, c.name AS category_name, c.slug AS category_slug,
            c.meta_title AS category_meta_title, c.meta_description AS category_meta_description
        FROM blog_posts p
        INNER JOIN blog_categories c ON c.id = p.category_id';
}

function blog_categories_with_posts(mysqli $db): array
{
    if (!blog_tables_ready($db)) {
        return [];
    }
    $sql = 'SELECT c.id, c.name, c.slug, COUNT(p.id) AS post_count
        FROM blog_categories c
        INNER JOIN blog_posts p ON p.category_id = c.id AND ' . blog_public_where('p') . '
        GROUP BY c.id, c.name, c.slug
        HAVING post_count > 0
        ORDER BY c.name ASC';
    $result = $db->query($sql);
    if (!$result) {
        return [];
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}

function blog_find_category(mysqli $db, string $slug): ?array
{
    $stmt = $db->prepare('SELECT * FROM blog_categories WHERE slug = ? LIMIT 1');
    $stmt->bind_param('s', $slug);
    return blog_fetch_one($stmt);
}

function blog_find_public_post(mysqli $db, string $categorySlug, string $slug): ?array
{
    $sql = blog_post_select() . ' WHERE c.slug = ? AND p.slug = ? AND ' . blog_public_where('p') . ' LIMIT 1';
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ss', $categorySlug, $slug);
    return blog_fetch_one($stmt);
}

function blog_find_post_by_id(mysqli $db, int $id): ?array
{
    $sql = blog_post_select() . ' WHERE p.id = ? LIMIT 1';
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $id);
    return blog_fetch_one($stmt);
}

function blog_redirect_target(mysqli $db, string $path): ?string
{
    if (!blog_tables_ready($db)) {
        return null;
    }
    $path = trim($path, '/');
    $stmt = $db->prepare('SELECT post_id FROM blog_redirects WHERE old_path = ? LIMIT 1');
    $stmt->bind_param('s', $path);
    $row = blog_fetch_one($stmt);
    if (!$row) {
        return null;
    }
    $post = blog_find_post_by_id($db, (int) $row['post_id']);
    if (!$post || !blog_is_public_now($post, $db)) {
        return null;
    }
    $target = blog_post_path($post);
    if (trim($target, '/') === $path) {
        return null;
    }
    return $target;
}

function blog_is_public_now(array $post, mysqli $db): bool
{
    if (($post['status'] ?? '') === 'draft' || empty($post['published_at'])) {
        return false;
    }
    $now = blog_format_mysql(blog_db_now($db));
    return (string) $post['published_at'] <= $now;
}

function blog_faqs(mysqli $db, int $postId): array
{
    $stmt = $db->prepare('SELECT question, answer FROM blog_post_faqs WHERE post_id = ? ORDER BY sort_order ASC, id ASC');
    $stmt->bind_param('i', $postId);
    return blog_fetch_all($stmt);
}

function blog_count_posts(mysqli $db, ?int $categoryId = null): int
{
    if (!blog_tables_ready($db)) {
        return 0;
    }
    if ($categoryId === null) {
        $sql = 'SELECT COUNT(*) AS total FROM blog_posts p WHERE ' . blog_public_where('p');
        $result = $db->query($sql);
    } else {
        $sql = 'SELECT COUNT(*) AS total FROM blog_posts p WHERE p.category_id = ? AND ' . blog_public_where('p');
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    if (!$result) {
        return 0;
    }
    $row = $result->fetch_assoc();
    return (int) ($row['total'] ?? 0);
}

function blog_listing(mysqli $db, ?int $categoryId, int $page): array
{
    $offset = ($page - 1) * BLOG_PER_PAGE;
    $limit = BLOG_PER_PAGE;
    $order = $categoryId === null
        ? 'p.pin_home DESC, p.pin_home_order ASC, p.published_at DESC, p.id DESC'
        : 'p.pin_category DESC, p.pin_category_order ASC, p.published_at DESC, p.id DESC';
    $sql = blog_post_select() . ' WHERE ' . blog_public_where('p');
    if ($categoryId !== null) {
        $sql .= ' AND p.category_id = ?';
    }
    $sql .= ' ORDER BY ' . $order . ' LIMIT ' . $limit . ' OFFSET ' . $offset;
    if ($categoryId === null) {
        $result = $db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $categoryId);
    return blog_fetch_all($stmt);
}

function blog_slider_posts(mysqli $db): array
{
    if (!blog_tables_ready($db)) {
        return [];
    }
    $sql = blog_post_select() . ' WHERE p.pin_slider = 1 AND ' . blog_public_where('p') . '
        ORDER BY p.pin_slider_order ASC, p.published_at DESC, p.id DESC
        LIMIT 5';
    $result = $db->query($sql);
    $posts = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    if (count($posts) >= 5) {
        return $posts;
    }
    $need = 5 - count($posts);
    $ids = array_map(static function (array $post): int {
        return (int) $post['id'];
    }, $posts);
    $sql = blog_post_select() . ' WHERE ' . blog_public_where('p');
    if ($ids) {
        $sql .= ' AND p.id NOT IN (' . implode(',', $ids) . ')';
    }
    $sql .= ' ORDER BY p.published_at DESC, p.id DESC LIMIT ' . (int) $need;
    $extra = $db->query($sql);
    if ($extra) {
        foreach ($extra->fetch_all(MYSQLI_ASSOC) as $row) {
            $posts[] = $row;
        }
    }
    return $posts;
}

function blog_latest_posts(mysqli $db, int $limit = 5): array
{
    if (!blog_tables_ready($db)) {
        return [];
    }
    $limit = max(1, $limit);
    $sql = blog_post_select() . ' WHERE ' . blog_public_where('p') . '
        ORDER BY p.published_at DESC, p.id DESC
        LIMIT ' . $limit;
    $result = $db->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function blog_latest_modified(mysqli $db, ?int $categoryId = null): string
{
    if (!blog_tables_ready($db)) {
        return '';
    }
    $sql = 'SELECT p.published_at, p.content_modified_at FROM blog_posts p WHERE ' . blog_public_where('p');
    if ($categoryId !== null) {
        $sql .= ' AND p.category_id = ' . (int) $categoryId;
    }
    $result = $db->query($sql);
    if (!$result) {
        return '';
    }
    $latest = '';
    while ($row = $result->fetch_assoc()) {
        $value = blog_modified_mysql($row);
        if ($value > $latest) {
            $latest = $value;
        }
    }
    return $latest;
}

function blog_page_count(int $total): int
{
    if ($total < 1) {
        return 1;
    }
    return (int) ceil($total / BLOG_PER_PAGE);
}

function blog_save_redirect(mysqli $db, string $oldPath, int $postId, string $newPath): void
{
    $oldPath = trim($oldPath, '/');
    $newPath = trim($newPath, '/');
    if ($oldPath === '' || $oldPath === $newPath) {
        return;
    }
    $delete = $db->prepare('DELETE FROM blog_redirects WHERE old_path = ?');
    $delete->bind_param('s', $newPath);
    $delete->execute();
    $delete->close();

    $stmt = $db->prepare('INSERT INTO blog_redirects (old_path, post_id) VALUES (?, ?)
        ON DUPLICATE KEY UPDATE post_id = VALUES(post_id)');
    $stmt->bind_param('si', $oldPath, $postId);
    $stmt->execute();
    $stmt->close();
}

function blog_img(array $post, bool $priority = false): string
{
    $alt = trim((string) ($post['image_alt'] ?? ''));
    if ($alt === '') {
        $alt = (string) ($post['title'] ?? '');
    }
    $html = '<img src="' . blog_e((string) $post['image_path']) . '" alt="' . blog_e($alt) . '" width="' . (int) $post['image_width'] . '" height="' . (int) $post['image_height'] . '"';
    if ($priority) {
        $html .= ' fetchpriority="high"';
    } else {
        $html .= ' loading="lazy"';
    }
    return $html . '>';
}

function blog_schema_graph(array $nodes): string
{
    $payload = [
        '@context' => 'https://schema.org',
        '@graph' => $nodes,
    ];
    return json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
}

function blog_organization_node(): array
{
    return [
        '@type' => 'Organization',
        '@id' => BLOG_ORG_ID,
        'name' => BLOG_ORG_NAME,
        'url' => BLOG_SITE_URL . '/',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => BLOG_ORG_LOGO,
        ],
    ];
}

function blog_posting_node(array $post, string $canonical): array
{
    return [
        '@type' => 'BlogPosting',
        'headline' => (string) $post['title'],
        'description' => (string) $post['summary'],
        'image' => blog_absolute((string) $post['image_path']),
        'datePublished' => blog_iso_from_mysql((string) $post['published_at']),
        'dateModified' => blog_iso_from_mysql(blog_modified_mysql($post)),
        'author' => [
            '@type' => 'Person',
            'name' => (string) $post['author'],
        ],
        'publisher' => [
            '@id' => BLOG_ORG_ID,
        ],
        'mainEntityOfPage' => $canonical,
    ];
}

function blog_breadcrumb_node(array $items): array
{
    $list = [];
    $position = 1;
    foreach ($items as $item) {
        $list[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $item['name'],
            'item' => $item['item'],
        ];
        $position++;
    }
    return [
        '@type' => 'BreadcrumbList',
        'itemListElement' => $list,
    ];
}

function blog_faq_node(array $faqs): array
{
    $entities = [];
    foreach ($faqs as $faq) {
        $entities[] = [
            '@type' => 'Question',
            'name' => (string) $faq['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => (string) $faq['answer'],
            ],
        ];
    }
    return [
        '@type' => 'FAQPage',
        'mainEntity' => $entities,
    ];
}
