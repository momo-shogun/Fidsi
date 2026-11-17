<?php

require_once __DIR__ . '/setting.php';
require_once __DIR__ . '/includes/blog.php';
require_once __DIR__ . '/includes/blog-view.php';

if (!($mysqli instanceof mysqli)) {
    http_response_code(500);
    echo 'The blog is unavailable.';
    exit;
}

blog_boot($mysqli);

$request = blog_parse_request();

if ($request['kind'] === 'home') {
    blog_render_home($mysqli, (int) $request['page'], (string) $request['path']);
    exit;
}

if ($request['kind'] === 'category') {
    blog_render_category($mysqli, (string) $request['category'], (int) $request['page'], (string) $request['path']);
    exit;
}

if ($request['kind'] === 'post') {
    blog_render_post_page($mysqli, (string) $request['category'], (string) $request['slug'], (string) $request['path']);
    exit;
}

blog_render_not_found();

function blog_send_page_one(string $path, string $target): void
{
    if (preg_match('#/page/1$#', $path) === 1) {
        header('Location: ' . $target, true, 301);
        exit;
    }
}

function blog_render_home(mysqli $db, int $page, string $path): void
{
    if ($page < 1) {
        blog_render_not_found();
        return;
    }
    blog_send_page_one($path, '/blog');

    $total = blog_count_posts($db);
    $pages = blog_page_count($total);
    if ($total === 0 && $page > 1) {
        blog_render_not_found();
        return;
    }
    if ($page > $pages) {
        blog_render_not_found();
        return;
    }

    $title = $page > 1 ? 'Blog - Page ' . $page . ' | FIDSI' : 'Blog | FIDSI';
    $description = BLOG_HOME_INTRO . ($page > 1 ? ' Page ' . $page . '.' : '');
    $canonical = blog_absolute(blog_home_path($page));
    $posts = blog_listing($db, null, $page);
    $categories = blog_categories_with_posts($db);

    blog_render_head([
        'title' => $title,
        'description' => $description,
        'canonical' => $canonical,
        'og_type' => 'website',
        'image' => BLOG_ORG_LOGO,
        'schema' => blog_schema_graph([blog_organization_node()]),
    ]);
    $crumbs = [
        ['name' => 'Home', 'href' => '/'],
        ['name' => 'Blog', 'href' => $page > 1 ? '/blog' : ''],
    ];
    if ($page > 1) {
        $crumbs[] = ['name' => 'Page ' . $page, 'href' => ''];
    }
    blog_render_page_header('Blog', $crumbs);
    blog_render_shell_open();
    echo '<div class="col-lg-9"><div class="row">';
    if (!$posts) {
        echo '<div class="col-lg-12"><p>No posts yet.</p></div>';
    } else {
        foreach ($posts as $index => $post) {
            blog_render_card($post, $index === 0);
        }
    }
    echo '</div>';
    blog_render_pagination($page, $pages, 'blog_home_path');
    echo '</div>';
    blog_render_sidebar($categories);
    blog_render_shell_close();
    blog_render_foot();
}

function blog_render_category(mysqli $db, string $slug, int $page, string $path): void
{
    if ($page < 1 || $slug === 'page') {
        blog_render_not_found();
        return;
    }
    $category = blog_find_category($db, $slug);
    if (!$category) {
        $redirect = blog_redirect_target($db, $path);
        if ($redirect !== null) {
            header('Location: ' . $redirect, true, 301);
            exit;
        }
        blog_render_not_found();
        return;
    }
    blog_send_page_one($path, blog_category_path($slug));

    $categoryId = (int) $category['id'];
    $total = blog_count_posts($db, $categoryId);
    $pages = blog_page_count($total);
    if (($total === 0 && $page > 1) || $page > $pages) {
        blog_render_not_found();
        return;
    }

    $baseTitle = trim((string) $category['meta_title']) !== ''
        ? trim((string) $category['meta_title'])
        : $category['name'] . ' | FIDSI';
    $title = $page > 1 ? $baseTitle . ' - Page ' . $page : $baseTitle;
    $baseDescription = trim((string) $category['meta_description']) !== ''
        ? trim((string) $category['meta_description'])
        : 'Articles in ' . $category['name'] . ' from FIDSI.';
    $description = $page > 1 ? $baseDescription . ' Page ' . $page . '.' : $baseDescription;
    $canonical = blog_absolute(blog_category_path($slug, $page));

    blog_render_head([
        'title' => $title,
        'description' => $description,
        'canonical' => $canonical,
        'og_type' => 'website',
        'image' => BLOG_ORG_LOGO,
        'schema' => blog_schema_graph([blog_organization_node()]),
    ]);
    $crumbs = [
        ['name' => 'Home', 'href' => '/'],
        ['name' => 'Blog', 'href' => '/blog'],
        ['name' => (string) $category['name'], 'href' => $page > 1 ? blog_category_path($slug) : ''],
    ];
    if ($page > 1) {
        $crumbs[] = ['name' => 'Page ' . $page, 'href' => ''];
    }
    blog_render_page_header((string) $category['name'], $crumbs);
    blog_render_shell_open();
    echo '<div class="col-lg-9"><div class="row">';
    $posts = blog_listing($db, $categoryId, $page);
    if (!$posts) {
        echo '<div class="col-lg-12"><p>No posts in this category yet.</p></div>';
    } else {
        foreach ($posts as $index => $post) {
            blog_render_card($post, $index === 0);
        }
    }
    echo '</div>';
    blog_render_pagination($page, $pages, static function (int $number) use ($slug): string {
        return blog_category_path($slug, $number);
    });
    echo '</div>';
    blog_render_sidebar(blog_categories_with_posts($db), $slug);
    blog_render_shell_close();
    blog_render_foot();
}

function blog_render_post_page(mysqli $db, string $categorySlug, string $slug, string $path): void
{
    $post = blog_find_public_post($db, $categorySlug, $slug);
    if (!$post) {
        $redirect = blog_redirect_target($db, $path);
        if ($redirect !== null) {
            header('Location: ' . $redirect, true, 301);
            exit;
        }
        blog_render_not_found();
        return;
    }

    $faqs = blog_faqs($db, (int) $post['id']);
    $canonical = blog_absolute(blog_post_path($post));
    $title = trim((string) $post['meta_title']) !== ''
        ? trim((string) $post['meta_title'])
        : $post['title'] . ' | FIDSI';
    $description = trim((string) $post['meta_description']) !== ''
        ? trim((string) $post['meta_description'])
        : (string) $post['summary'];
    $crumbs = [
        ['name' => 'Home', 'item' => BLOG_SITE_URL . '/'],
        ['name' => 'Blog', 'item' => blog_absolute('/blog')],
        ['name' => (string) $post['category_name'], 'item' => blog_absolute(blog_category_path((string) $post['category_slug']))],
        ['name' => (string) $post['title'], 'item' => $canonical],
    ];
    $graph = [
        blog_organization_node(),
        blog_posting_node($post, $canonical),
        blog_breadcrumb_node($crumbs),
    ];
    if ($faqs) {
        $graph[] = blog_faq_node($faqs);
    }

    blog_render_head([
        'title' => $title,
        'description' => $description,
        'canonical' => $canonical,
        'robots' => !empty($post['noindex']) ? 'noindex, follow' : '',
        'og_type' => 'article',
        'image' => blog_absolute((string) $post['image_path']),
        'schema' => blog_schema_graph($graph),
    ]);
    $meta = '<div class="col-lg-12"><div class="meta_blog_single">';
    $meta .= '<div class="left_side"><div class="category"><a href="' . blog_e(blog_category_path((string) $post['category_slug'])) . '">' . blog_e((string) $post['category_name']) . '</a></div></div>';
    $meta .= '<div class="right_side"><div class="content_box_auht"><div class="authour_content"><h6>Written by</h6><h4>' . blog_e((string) $post['author']) . '</h4></div></div></div>';
    $meta .= '</div></div>';
    blog_render_page_header((string) $post['title'], [
        ['name' => 'Home', 'href' => '/'],
        ['name' => 'Blog', 'href' => '/blog'],
        ['name' => (string) $post['category_name'], 'href' => blog_category_path((string) $post['category_slug'])],
        ['name' => (string) $post['title'], 'href' => ''],
    ], $meta, 'blog_single_pageheader');
    blog_render_shell_open();
    echo '<div class="col-lg-9"><div class="pd_top_20"></div>';
    echo '<article class="blog_single_details_outer"><div class="single_content_upper">';
    echo '<div class="blog_feature_image">' . blog_img($post, true) . '</div>';
    echo '<div class="pd_bottom_20"></div>';
    echo '<div class="post_single_content">';
    echo '<div class="description_box"><p>' . blog_e((string) $post['summary']) . '</p></div>';
    echo '<div class="pd_bottom_15"></div>';
    echo $post['content'];
    if ($faqs) {
        echo '<section class="blog-faq"><h2>Frequently asked questions</h2>';
        foreach ($faqs as $faq) {
            echo '<h3>' . blog_e((string) $faq['question']) . '</h3>';
            echo '<p>' . blog_e((string) $faq['answer']) . '</p>';
        }
        echo '</section>';
    }
    echo '</div></div></article></div>';
    blog_render_sidebar(blog_categories_with_posts($db), (string) $post['category_slug']);
    blog_render_shell_close();
    blog_render_foot();
}
