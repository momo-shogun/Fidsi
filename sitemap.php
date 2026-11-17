<?php

require_once __DIR__ . '/setting.php';
require_once __DIR__ . '/includes/blog.php';

header('Content-Type: application/xml; charset=UTF-8');

$staticFile = __DIR__ . '/sitemap-static.xml';
$staticUrls = [];

if (is_file($staticFile)) {
    $xml = file_get_contents($staticFile);
    if ($xml !== false && preg_match_all('#<url>(.*?)</url>#s', $xml, $blocks)) {
        foreach ($blocks[1] as $block) {
            if (!preg_match('#<loc>\s*([^<]+?)\s*</loc>#', $block, $loc)) {
                continue;
            }
            $lastmod = '';
            if (preg_match('#<lastmod>\s*([^<]+?)\s*</lastmod>#', $block, $mod)) {
                $lastmod = trim($mod[1]);
            }
            $staticUrls[] = [
                'loc' => trim($loc[1]),
                'lastmod' => $lastmod,
            ];
        }
    }
}

$blogUrls = [];
if ($mysqli instanceof mysqli) {
    blog_boot($mysqli);
    if (blog_tables_ready($mysqli)) {
        $homeMod = blog_latest_modified($mysqli);
        $blogUrls[] = [
            'loc' => blog_absolute('/blog'),
            'lastmod' => $homeMod === '' ? '' : blog_iso_from_mysql($homeMod),
        ];

        $categories = blog_categories_with_posts($mysqli);
        foreach ($categories as $category) {
            $modified = blog_latest_modified($mysqli, (int) $category['id']);
            $blogUrls[] = [
                'loc' => blog_absolute(blog_category_path((string) $category['slug'])),
                'lastmod' => $modified === '' ? '' : blog_iso_from_mysql($modified),
            ];
        }

        $sql = blog_post_select() . ' WHERE ' . blog_public_where('p') . ' AND p.noindex = 0 ORDER BY p.published_at DESC';
        $result = $mysqli->query($sql);
        if ($result) {
            while ($post = $result->fetch_assoc()) {
                $blogUrls[] = [
                    'loc' => blog_absolute(blog_post_path($post)),
                    'lastmod' => blog_iso_from_mysql(blog_modified_mysql($post)),
                ];
            }
        }
    }
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

$seen = [];
foreach (array_merge($staticUrls, $blogUrls) as $url) {
    if (isset($seen[$url['loc']])) {
        continue;
    }
    $seen[$url['loc']] = true;
    echo "  <url>\n";
    echo '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1) . "</loc>\n";
    if ($url['lastmod'] !== '') {
        echo '    <lastmod>' . htmlspecialchars($url['lastmod'], ENT_XML1) . "</lastmod>\n";
    }
    echo "  </url>\n";
}

echo "</urlset>\n";
