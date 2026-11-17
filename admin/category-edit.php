<?php

require_once __DIR__ . '/../includes/blog.php';
require_once __DIR__ . '/../includes/blog-admin.php';

$id = (int) ($_GET['id'] ?? 0);
$admin_page_title = $id > 0 ? 'Edit Category' : 'New Category';

require_once __DIR__ . '/header.php';

blog_boot($mysqli);
$flash = blog_admin_take_flash();
$category = [
    'name' => '',
    'slug' => '',
    'meta_title' => '',
    'meta_description' => '',
];
if ($id > 0 && blog_tables_ready($mysqli)) {
    $stmt = $mysqli->prepare('SELECT * FROM blog_categories WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$row) {
        echo '<div class="admin-alert" role="alert">That category does not exist.</div>';
        require __DIR__ . '/footer.php';
        exit;
    }
    $category = $row;
}
?>

<?php if (!empty($flash['errors'])): ?>
    <div class="admin-alert" role="alert"><?= blog_e(implode(' ', $flash['errors'])) ?></div>
<?php endif; ?>

<form class="admin-form admin-card" method="post" action="/admin/blog-actions">
    <div class="admin-card-header">
        <div>
            <h2 class="admin-card-title"><?= blog_e($admin_page_title) ?></h2>
            <p class="admin-card-subtitle">The slug is the category address: /blog/your-slug</p>
        </div>
    </div>
    <div class="admin-card-body">
        <input type="hidden" name="csrf" value="<?= blog_e(blog_admin_token()) ?>">
        <input type="hidden" name="action" value="save-category">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="form-group">
            <label for="name">Name</label>
            <input class="form-control" id="name" name="name" required value="<?= blog_e((string) blog_admin_old($flash, 'name', $category['name'])) ?>">
        </div>
        <div class="form-group">
            <label for="slug">Slug</label>
            <input class="form-control" id="slug" name="slug" value="<?= blog_e((string) blog_admin_old($flash, 'slug', $category['slug'])) ?>">
            <p class="admin-help">Leave this blank to build it from the name. “page” is reserved.</p>
        </div>
        <div class="form-group">
            <label for="meta_title">Meta title</label>
            <input class="form-control" id="meta_title" name="meta_title" value="<?= blog_e((string) blog_admin_old($flash, 'meta_title', $category['meta_title'])) ?>">
        </div>
        <div class="form-group">
            <label for="meta_description">Meta description</label>
            <textarea class="form-control" id="meta_description" name="meta_description" rows="3"><?= blog_e((string) blog_admin_old($flash, 'meta_description', $category['meta_description'])) ?></textarea>
        </div>
        <div class="admin-form-actions">
            <button class="btn-admin btn-admin-primary" type="submit">Save category</button>
            <a class="btn-admin btn-admin-outline" href="/admin/categories">Cancel</a>
        </div>
    </div>
</form>

<?php require_once __DIR__ . '/footer.php'; ?>
