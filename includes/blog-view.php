<?php

function blog_render_head(array $meta): void
{
    $title = (string) $meta['title'];
    $description = (string) $meta['description'];
    $canonical = (string) $meta['canonical'];
    $image = (string) ($meta['image'] ?? BLOG_ORG_LOGO);
    $type = (string) ($meta['og_type'] ?? 'website');
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= blog_e($title) ?></title>
    <meta name="description" content="<?= blog_e($description) ?>">
    <link rel="canonical" href="<?= blog_e($canonical) ?>">
    <?php if (!empty($meta['robots'])): ?>
    <meta name="robots" content="<?= blog_e((string) $meta['robots']) ?>">
    <?php endif; ?>
    <meta property="og:title" content="<?= blog_e($title) ?>">
    <meta property="og:description" content="<?= blog_e($description) ?>">
    <meta property="og:url" content="<?= blog_e($canonical) ?>">
    <meta property="og:type" content="<?= blog_e($type) ?>">
    <meta property="og:image" content="<?= blog_e($image) ?>">
    <meta property="og:site_name" content="FIDSI">
    <?php include __DIR__ . '/../common/header-script.php'; ?>
    <script type="application/ld+json"><?= $meta['schema'] ?></script>
</head>
<body class="theme-creote">
    <div id="page" class="page_wapper hfeed site">
        <div id="wrapper_full" class="content_all_warpper">
            <?php include __DIR__ . '/../common/header.php'; ?>
    <?php
}

function blog_render_foot(): void
{
    include __DIR__ . '/../common/footer.php';
    echo '</div></div>';
    include __DIR__ . '/../common/footer-script.php';
    echo '</body></html>';
}

function blog_render_crumbs(array $crumbs): void
{
    echo '<ul class="breadcrumb m-auto">';
    $last = count($crumbs) - 1;
    foreach ($crumbs as $index => $crumb) {
        if ($index === $last || empty($crumb['href'])) {
            echo '<li class="active">' . blog_e($crumb['name']) . '</li>';
        } else {
            echo '<li><a href="' . blog_e($crumb['href']) . '">' . blog_e($crumb['name']) . '</a></li>';
        }
    }
    echo '</ul>';
}

function blog_render_page_header(string $title, array $crumbs, string $extra = '', string $modifier = ''): void
{
    $class = 'page_header_default style_one' . ($modifier !== '' ? ' ' . $modifier : '');
    echo '<div class="' . blog_e($class) . '">';
    echo '<div class="parallax_cover"><div class="simpleParallax">';
    echo '<img src="/assets/images/page-header-default.jpg" alt="" class="cover-parallax" width="1600" height="616">';
    echo '</div></div>';
    echo '<div class="page_header_content"><div class="auto-container"><div class="row">';
    echo '<div class="col-md-12"><div class="banner_title_inner"><h1 class="title_page">' . blog_e($title) . '</h1></div></div>';
    echo $extra;
    echo '<div class="col-lg-12"><div class="breadcrumbs creote">';
    blog_render_crumbs($crumbs);
    echo '</div></div></div></div></div></div>';
}

function blog_render_sidebar(array $categories, string $currentSlug = ''): void
{
    echo '<div class="col-lg-3"><div class="pd_top_50"></div><aside class="side_bar">';
    echo '<div class="widgets_grid_box"><h2 class="widget-title">Categories</h2><ul class="wp-block-categories">';
    $allCurrent = $currentSlug === '' ? ' aria-current="page"' : '';
    echo '<li><a href="/blog"' . $allCurrent . '>All</a></li>';
    foreach ($categories as $category) {
        $current = $category['slug'] === $currentSlug ? ' aria-current="page"' : '';
        echo '<li><a href="' . blog_e(blog_category_path((string) $category['slug'])) . '"' . $current . '>';
        echo blog_e((string) $category['name']) . ' (' . (int) $category['post_count'] . ')';
        echo '</a></li>';
    }
    echo '</ul></div></aside></div>';
}

function blog_render_pagination(int $page, int $pages, callable $urlFor): void
{
    if ($pages < 2) {
        return;
    }
    echo '<nav class="pagination" aria-label="Pagination"><ul>';
    if ($page > 1) {
        echo '<li><a href="' . blog_e($urlFor($page - 1)) . '" rel="prev">‹</a></li>';
    }
    for ($i = 1; $i <= $pages; $i++) {
        if ($i === $page) {
            echo '<li class="active"><span class="current" aria-current="page">' . $i . '</span></li>';
        } else {
            echo '<li><a href="' . blog_e($urlFor($i)) . '">' . $i . '</a></li>';
        }
    }
    if ($page < $pages) {
        echo '<li><a href="' . blog_e($urlFor($page + 1)) . '" rel="next">›</a></li>';
    }
    echo '</ul></nav>';
}

function blog_render_card(array $post, bool $priority = false): void
{
    $path = blog_post_path($post);
    echo '<div class="col-lg-4 col-md-6">';
    echo '<article class="news_box style_seven">';
    echo '<div class="image_box"><a href="' . blog_e($path) . '">';
    echo blog_img($post, $priority);
    echo '</a><div class="date">' . blog_e(blog_display_date((string) $post['published_at'])) . '</div></div>';
    echo '<div class="content_box">';
    echo '<ul><li><a href="' . blog_e(blog_category_path((string) $post['category_slug'])) . '">' . blog_e((string) $post['category_name']) . '</a></li></ul>';
    echo '<h2 class="title"><a href="' . blog_e($path) . '" rel="bookmark">' . blog_e((string) $post['title']) . '</a></h2>';
    echo '<a href="' . blog_e($path) . '" class="read_more">Read more<i class="icon-right-arrow-long"></i></a>';
    echo '</div></article></div>';
}

function blog_render_feature(array $posts): void
{
    if (!$posts) {
        return;
    }
    $lead = $posts[0];
    $rest = array_slice($posts, 1);
    echo '<section class="blog-feature" aria-label="Featured posts">';
    echo '<a class="blog-feature-lead" href="' . blog_e(blog_post_path($lead)) . '">';
    echo blog_img($lead, true);
    echo '<span><small>' . blog_e((string) $lead['category_name']) . '</small>';
    echo '<strong>' . blog_e((string) $lead['title']) . '</strong></span>';
    echo '</a>';
    if ($rest) {
        echo '<div class="blog-feature-side">';
        foreach ($rest as $post) {
            echo '<a href="' . blog_e(blog_post_path($post)) . '">';
            echo blog_img($post);
            echo '<span><small>' . blog_e((string) $post['category_name']) . '</small>';
            echo '<strong>' . blog_e((string) $post['title']) . '</strong></span>';
            echo '</a>';
        }
        echo '</div>';
    }
    echo '</section>';
}

function blog_render_shell_open(): void
{
    echo '<div id="content" class="site-content"><div class="auto-container"><div class="row default_row"><div class="full_width_box"><div class="pd_top_50"></div><div class="row">';
}

function blog_render_shell_close(): void
{
    echo '</div><div class="pd_bottom_40"></div></div></div></div></div>';
}

function blog_render_not_found(): void
{
    http_response_code(404);
    $canonical = blog_absolute('/blog');
    $schema = blog_schema_graph([blog_organization_node()]);
    blog_render_head([
        'title' => 'Page not found | FIDSI',
        'description' => 'This blog page does not exist.',
        'canonical' => $canonical,
        'robots' => 'noindex, follow',
        'og_type' => 'website',
        'image' => BLOG_ORG_LOGO,
        'schema' => $schema,
    ]);
    blog_render_page_header('Page not found', [
        ['name' => 'Home', 'href' => '/'],
        ['name' => 'Blog', 'href' => '/blog'],
        ['name' => 'Page not found', 'href' => ''],
    ]);
    blog_render_shell_open();
    echo '<div class="col-lg-9"><p>That page is not on the blog. The address may have changed, or the page number is past the last page.</p>';
    echo '<p><a class="read_more" href="/blog">Back to the blog<i class="icon-right-arrow-long"></i></a></p></div>';
    blog_render_sidebar([]);
    blog_render_shell_close();
    blog_render_foot();
}
