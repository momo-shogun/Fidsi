<?php

require_once __DIR__ . '/admin-url.php';

function blog_admin_token(): string
{
    if (empty($_SESSION['blog_csrf'])) {
        $_SESSION['blog_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['blog_csrf'];
}

function blog_admin_csrf_ok(): bool
{
    $sent = (string) ($_POST['csrf'] ?? '');
    $known = (string) ($_SESSION['blog_csrf'] ?? '');
    return $sent !== '' && $known !== '' && hash_equals($known, $sent);
}

function blog_admin_set_flash(array $errors, array $old = [], string $success = ''): void
{
    $_SESSION['blog_admin_flash'] = [
        'errors' => $errors,
        'old' => $old,
        'success' => $success,
    ];
}

function blog_admin_take_flash(): array
{
    $flash = $_SESSION['blog_admin_flash'] ?? ['errors' => [], 'old' => [], 'success' => ''];
    unset($_SESSION['blog_admin_flash']);
    return $flash;
}

function blog_admin_old(array $flash, string $key, $fallback = '')
{
    if (array_key_exists($key, $flash['old'])) {
        return $flash['old'][$key];
    }
    return $fallback;
}

function blog_admin_require(): void
{
    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 'FIDSI') {
        header('Location: ' . admin_url());
        exit;
    }
}

function blog_pin_count(mysqli $db, string $column, int $exceptId, ?int $categoryId = null): int
{
    $allowed = ['pin_slider', 'pin_home', 'pin_category'];
    if (!in_array($column, $allowed, true)) {
        return 0;
    }
    $sql = 'SELECT COUNT(*) AS total FROM blog_posts WHERE ' . $column . ' = 1 AND id <> ?';
    if ($column === 'pin_category') {
        $sql .= ' AND category_id = ?';
        $stmt = $db->prepare($sql);
        $categoryId = (int) $categoryId;
        $stmt->bind_param('ii', $exceptId, $categoryId);
    } else {
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $exceptId);
    }
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (int) ($row['total'] ?? 0);
}

function blog_store_upload(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['ok' => false, 'missing' => true];
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'The image could not be uploaded.'];
    }
    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        return ['ok' => false, 'error' => 'Use an image smaller than 5 MB.'];
    }
    $info = @getimagesize($file['tmp_name']);
    $allowed = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($allowed[$info[2]])) {
        return ['ok' => false, 'error' => 'Upload a JPG, PNG, GIF, or WebP image.'];
    }
    $dir = dirname(__DIR__) . '/uploads/blog';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return ['ok' => false, 'error' => 'The image folder is not available.'];
    }
    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$info[2]];
    $target = $dir . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return ['ok' => false, 'error' => 'The image could not be saved.'];
    }
    return [
        'ok' => true,
        'path' => '/uploads/blog/' . $name,
        'width' => (int) $info[0],
        'height' => (int) $info[1],
    ];
}

function blog_delete_upload(string $path): void
{
    if (!str_starts_with($path, '/uploads/blog/')) {
        return;
    }
    $full = dirname(__DIR__) . $path;
    if (is_file($full)) {
        unlink($full);
    }
}

function blog_input_datetime(string $value): ?DateTimeImmutable
{
    $value = trim($value);
    if ($value === '') {
        return null;
    }
    $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $value);
    if ($dt instanceof DateTimeImmutable) {
        return $dt;
    }
    $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', $value);
    return $dt instanceof DateTimeImmutable ? $dt : null;
}

function blog_content_for_storage(string $content): string
{
    if ($content === '' || preg_match('/<[^>]+>/', $content) === 1) {
        return $content;
    }
    $content = str_replace(["\r\n", "\r"], "\n", $content);
    $parts = preg_split("/\n\s*\n/", $content) ?: [];
    $html = '';
    foreach ($parts as $part) {
        $part = trim($part);
        if ($part === '') {
            continue;
        }
        if (str_starts_with($part, '## ')) {
            $html .= '<h2>' . htmlspecialchars(trim(substr($part, 3)), ENT_QUOTES, 'UTF-8') . '</h2>';
            continue;
        }
        $part = htmlspecialchars($part, ENT_QUOTES, 'UTF-8');
        $part = str_replace("\n", '<br>', $part);
        $html .= '<p>' . $part . '</p>';
    }
    return $html;
}

function blog_content_for_editor(string $content): string
{
    $content = trim($content);
    if ($content === '' || preg_match('/<(?!\/?(p|h2|br)\b)[^>]*>/i', $content) === 1) {
        return $content;
    }
    if (preg_match_all('/<(p|h2)>(.*?)<\/\1>/is', $content, $matches, PREG_SET_ORDER) !== false && $matches) {
        $blocks = [];
        foreach ($matches as $match) {
            $text = preg_replace('/<br\s*\/?>/i', "\n", $match[2]) ?? $match[2];
            $text = trim(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
            $blocks[] = strtolower($match[1]) === 'h2' ? '## ' . $text : $text;
        }
        return implode("\n\n", $blocks);
    }
    return $content;
}

function blog_faq_pairs(array $questions, array $answers): array
{
    $pairs = [];
    $count = max(count($questions), count($answers));
    for ($i = 0; $i < $count; $i++) {
        $question = trim((string) ($questions[$i] ?? ''));
        $answer = trim((string) ($answers[$i] ?? ''));
        if ($question === '' && $answer === '') {
            continue;
        }
        $pairs[] = ['question' => $question, 'answer' => $answer];
    }
    return $pairs;
}

function blog_content_hash(array $data, array $faqs): string
{
    return hash('sha256', json_encode([$data, $faqs]));
}

function blog_replace_faqs(mysqli $db, int $postId, array $faqs): void
{
    $delete = $db->prepare('DELETE FROM blog_post_faqs WHERE post_id = ?');
    $delete->bind_param('i', $postId);
    $delete->execute();
    $delete->close();
    $insert = $db->prepare('INSERT INTO blog_post_faqs (post_id, question, answer, sort_order) VALUES (?, ?, ?, ?)');
    foreach ($faqs as $index => $faq) {
        $question = $faq['question'];
        $answer = $faq['answer'];
        $insert->bind_param('issi', $postId, $question, $answer, $index);
        $insert->execute();
    }
    $insert->close();
}

function blog_save_category(mysqli $db): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim((string) ($_POST['name'] ?? ''));
    $slug = blog_slugify((string) ($_POST['slug'] ?? ''));
    if ($slug === '') {
        $slug = blog_slugify($name);
    }
    $metaTitle = trim((string) ($_POST['meta_title'] ?? ''));
    $metaDescription = trim((string) ($_POST['meta_description'] ?? ''));
    $errors = [];
    if ($name === '') {
        $errors[] = 'Enter a category name.';
    }
    if ($slug === '') {
        $errors[] = 'Enter a slug.';
    }
    if ($slug === 'page') {
        $errors[] = 'The slug “page” is reserved.';
    }
    $stmt = $db->prepare('SELECT id FROM blog_categories WHERE slug = ? AND id <> ? LIMIT 1');
    $stmt->bind_param('si', $slug, $id);
    $stmt->execute();
    $taken = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($taken) {
        $errors[] = 'That slug is already used.';
    }
    $old = null;
    if ($id > 0) {
        $stmt = $db->prepare('SELECT * FROM blog_categories WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $old = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$old) {
            $errors[] = 'That category no longer exists.';
        }
    }
    if ($errors) {
        blog_admin_set_flash($errors, $_POST);
        $back = admin_url('category-edit') . ($id > 0 ? '?id=' . $id : '');
        header('Location: ' . $back);
        exit;
    }

    if ($id > 0) {
        $stmt = $db->prepare('UPDATE blog_categories SET name = ?, slug = ?, meta_title = ?, meta_description = ? WHERE id = ?');
        $stmt->bind_param('ssssi', $name, $slug, $metaTitle, $metaDescription, $id);
        $stmt->execute();
        $stmt->close();
        if ($old && $old['slug'] !== $slug) {
            $posts = $db->prepare('SELECT id, slug FROM blog_posts WHERE category_id = ?');
            $posts->bind_param('i', $id);
            $posts->execute();
            $result = $posts->get_result();
            while ($post = $result->fetch_assoc()) {
                blog_save_redirect(
                    $db,
                    'blog/' . $old['slug'] . '/' . $post['slug'],
                    (int) $post['id'],
                    'blog/' . $slug . '/' . $post['slug']
                );
            }
            $posts->close();
        }
    } else {
        $stmt = $db->prepare('INSERT INTO blog_categories (name, slug, meta_title, meta_description) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $name, $slug, $metaTitle, $metaDescription);
        $stmt->execute();
        $stmt->close();
    }
    blog_admin_set_flash([], [], 'Category saved.');
    header('Location: ' . admin_url('categories'));
    exit;
}

function blog_delete_category(mysqli $db): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = $db->prepare('SELECT COUNT(*) AS total FROM blog_posts WHERE category_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $count = (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
    $stmt->close();
    if ($count > 0) {
        blog_admin_set_flash(['Delete or move the posts in this category first.']);
        header('Location: ' . admin_url('categories'));
        exit;
    }
    $stmt = $db->prepare('DELETE FROM blog_categories WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    blog_admin_set_flash([], [], 'Category deleted.');
    header('Location: ' . admin_url('categories'));
    exit;
}

function blog_save_post(mysqli $db): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $existing = null;
    if ($id > 0) {
        $existing = blog_find_post_by_id($db, $id);
        if (!$existing) {
            blog_admin_set_flash(['That post no longer exists.'], $_POST);
            header('Location: ' . admin_url('posts'));
            exit;
        }
    }

    $title = trim((string) ($_POST['title'] ?? ''));
    $slug = blog_slugify((string) ($_POST['slug'] ?? ''));
    if ($slug === '') {
        $slug = blog_slugify($title);
    }
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $summary = trim((string) ($_POST['summary'] ?? ''));
    $content = trim((string) ($_POST['content'] ?? ''));
    $alt = trim((string) ($_POST['image_alt'] ?? ''));
    $author = trim((string) ($_POST['author'] ?? ''));
    $metaTitle = trim((string) ($_POST['meta_title'] ?? ''));
    $metaDescription = trim((string) ($_POST['meta_description'] ?? ''));
    $status = (string) ($_POST['status'] ?? 'draft');
    $noindex = isset($_POST['noindex']) ? 1 : 0;
    $pinSlider = isset($_POST['pin_slider']) ? 1 : 0;
    $pinHome = isset($_POST['pin_home']) ? 1 : 0;
    $pinCategory = isset($_POST['pin_category']) ? 1 : 0;
    $pinSliderOrder = max(0, (int) ($_POST['pin_slider_order'] ?? 0));
    $pinHomeOrder = max(0, (int) ($_POST['pin_home_order'] ?? 0));
    $pinCategoryOrder = max(0, (int) ($_POST['pin_category_order'] ?? 0));
    $questions = $_POST['faq_question'] ?? [];
    $answers = $_POST['faq_answer'] ?? [];
    if (!is_array($questions)) {
        $questions = [$questions];
    }
    if (!is_array($answers)) {
        $answers = [$answers];
    }
    $faqs = blog_faq_pairs($questions, $answers);

    $errors = [];
    if ($title === '') {
        $errors[] = 'Enter a headline.';
    }
    if ($slug === '') {
        $errors[] = 'Enter a link name. The headline could not be turned into one.';
    }
    $category = null;
    if ($categoryId < 1) {
        $errors[] = 'Choose a topic.';
    } else {
        $stmt = $db->prepare('SELECT * FROM blog_categories WHERE id = ?');
        $stmt->bind_param('i', $categoryId);
        $stmt->execute();
        $category = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$category) {
            $errors[] = 'Choose a topic.';
        }
    }
    if ($summary === '') {
        $errors[] = 'Enter a short summary.';
    }
    if (trim(strip_tags($content)) === '') {
        $errors[] = 'Write the article.';
    }
    if ($author === '') {
        $errors[] = 'Enter who wrote this.';
    }
    if ($alt === '') {
        $errors[] = 'Describe the picture in a few words.';
    }
    if (!in_array($status, ['draft', 'published', 'scheduled'], true)) {
        $errors[] = 'Choose when readers can see this.';
    }
    foreach ($faqs as $faq) {
        if ($faq['question'] === '' || $faq['answer'] === '') {
            $errors[] = 'Each question needs an answer. You can also leave that question blank.';
            break;
        }
    }

    $publishedAt = blog_input_datetime((string) ($_POST['published_at'] ?? ''));
    $now = blog_db_now($db);
    if ($status === 'published') {
        if ($publishedAt === null) {
            $publishedAt = $now;
        } elseif (blog_format_mysql($publishedAt) > blog_format_mysql($now)) {
            $errors[] = '“Show it now” needs today or an earlier date. Choose “Show it later” for a future date.';
        }
    } elseif ($status === 'scheduled') {
        if ($publishedAt === null || blog_format_mysql($publishedAt) <= blog_format_mysql($now)) {
            $errors[] = '“Show it later” needs a future date and time.';
        }
    }

    $upload = blog_store_upload($_FILES['image'] ?? []);
    if (!$upload['ok'] && empty($upload['missing'])) {
        $errors[] = $upload['error'];
    }
    if ($id === 0 && !empty($upload['missing'])) {
        $errors[] = 'Choose a picture.';
    }
    if ($id > 0 && !empty($upload['missing']) && $existing && $existing['image_path'] === '') {
        $errors[] = 'Choose a picture.';
    }

    if ($slug !== '' && $category) {
        $stmt = $db->prepare('SELECT id FROM blog_posts WHERE category_id = ? AND slug = ? AND id <> ? LIMIT 1');
        $stmt->bind_param('isi', $categoryId, $slug, $id);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            $errors[] = 'Another article in this topic already uses that link name.';
        }
        $stmt->close();
    }

    if ($pinSlider && blog_pin_count($db, 'pin_slider', $id) >= 5) {
        $errors[] = 'The large pictures at the top can only hold 5 articles.';
    }
    if ($pinHome && blog_pin_count($db, 'pin_home', $id) >= 3) {
        $errors[] = 'The blog home list can only feature 3 articles.';
    }
    if ($pinCategory && $category && blog_pin_count($db, 'pin_category', $id, $categoryId) >= 3) {
        $errors[] = 'This topic’s list can only feature 3 articles.';
    }

    if ($errors) {
        if (!empty($upload['ok'])) {
            blog_delete_upload($upload['path']);
        }
        blog_admin_set_flash($errors, $_POST);
        header('Location: ' . admin_url('post-edit') . ($id > 0 ? '?id=' . $id : ''));
        exit;
    }

    $content = blog_content_for_storage($content);

    $imagePath = $upload['ok'] ? $upload['path'] : (string) $existing['image_path'];
    $imageWidth = $upload['ok'] ? $upload['width'] : (int) $existing['image_width'];
    $imageHeight = $upload['ok'] ? $upload['height'] : (int) $existing['image_height'];
    $publishedSql = $publishedAt ? blog_format_mysql($publishedAt) : null;
    if (!$pinSlider) {
        $pinSliderOrder = 0;
    }
    if (!$pinHome) {
        $pinHomeOrder = 0;
    }
    if (!$pinCategory) {
        $pinCategoryOrder = 0;
    }

    $hashData = [
        'title' => $title,
        'slug' => $slug,
        'category_id' => $categoryId,
        'summary' => $summary,
        'content' => $content,
        'image_path' => $imagePath,
        'image_alt' => $alt,
        'author' => $author,
        'meta_title' => $metaTitle,
        'meta_description' => $metaDescription,
    ];
    $nowSql = blog_format_mysql($now);

    $db->begin_transaction();
    if ($id > 0) {
        $oldFaqs = blog_faqs($db, $id);
        $oldHash = blog_content_hash([
            'title' => (string) $existing['title'],
            'slug' => (string) $existing['slug'],
            'category_id' => (int) $existing['category_id'],
            'summary' => (string) $existing['summary'],
            'content' => (string) $existing['content'],
            'image_path' => (string) $existing['image_path'],
            'image_alt' => (string) $existing['image_alt'],
            'author' => (string) $existing['author'],
            'meta_title' => (string) $existing['meta_title'],
            'meta_description' => (string) $existing['meta_description'],
        ], $oldFaqs);
        $contentModified = blog_content_hash($hashData, $faqs) === $oldHash
            ? (string) $existing['content_modified_at']
            : $nowSql;
        $stmt = $db->prepare('UPDATE blog_posts SET category_id = ?, title = ?, slug = ?, summary = ?, content = ?, image_path = ?, image_alt = ?, image_width = ?, image_height = ?, author = ?, meta_title = ?, meta_description = ?, status = ?, published_at = ?, content_modified_at = ?, noindex = ?, pin_slider = ?, pin_slider_order = ?, pin_home = ?, pin_home_order = ?, pin_category = ?, pin_category_order = ? WHERE id = ?');
        $stmt->bind_param(
            'issssssii' . 'ssssss' . 'iiiiiiii',
            $categoryId,
            $title,
            $slug,
            $summary,
            $content,
            $imagePath,
            $alt,
            $imageWidth,
            $imageHeight,
            $author,
            $metaTitle,
            $metaDescription,
            $status,
            $publishedSql,
            $contentModified,
            $noindex,
            $pinSlider,
            $pinSliderOrder,
            $pinHome,
            $pinHomeOrder,
            $pinCategory,
            $pinCategoryOrder,
            $id
        );
        $stmt->execute();
        $stmt->close();
        blog_replace_faqs($db, $id, $faqs);
        $oldPath = 'blog/' . $existing['category_slug'] . '/' . $existing['slug'];
        $newPath = 'blog/' . $category['slug'] . '/' . $slug;
        blog_save_redirect($db, $oldPath, $id, $newPath);
        if ($upload['ok']) {
            blog_delete_upload((string) $existing['image_path']);
        }
    } else {
        $stmt = $db->prepare('INSERT INTO blog_posts (category_id, title, slug, summary, content, image_path, image_alt, image_width, image_height, author, meta_title, meta_description, status, published_at, content_modified_at, noindex, pin_slider, pin_slider_order, pin_home, pin_home_order, pin_category, pin_category_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param(
            'issssssii' . 'ssssss' . 'iiiiiii',
            $categoryId,
            $title,
            $slug,
            $summary,
            $content,
            $imagePath,
            $alt,
            $imageWidth,
            $imageHeight,
            $author,
            $metaTitle,
            $metaDescription,
            $status,
            $publishedSql,
            $nowSql,
            $noindex,
            $pinSlider,
            $pinSliderOrder,
            $pinHome,
            $pinHomeOrder,
            $pinCategory,
            $pinCategoryOrder
        );
        $stmt->execute();
        $id = (int) $stmt->insert_id;
        $stmt->close();
        blog_replace_faqs($db, $id, $faqs);
        $newPath = 'blog/' . $category['slug'] . '/' . $slug;
        $clear = $db->prepare('DELETE FROM blog_redirects WHERE old_path = ?');
        $clear->bind_param('s', $newPath);
        $clear->execute();
        $clear->close();
    }
    $db->commit();

    blog_admin_set_flash([], [], 'Blog saved.');
    header('Location: ' . admin_url('posts'));
    exit;
}

function blog_delete_post(mysqli $db): void
{
    $id = (int) ($_POST['id'] ?? 0);
    $post = blog_find_post_by_id($db, $id);
    if ($post) {
        $stmt = $db->prepare('DELETE FROM blog_posts WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        blog_delete_upload((string) $post['image_path']);
    }
    blog_admin_set_flash([], [], 'Blog deleted.');
    header('Location: ' . admin_url('posts'));
    exit;
}
