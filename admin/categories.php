<?php

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../includes/blog.php';
require_once __DIR__ . '/../includes/blog-admin.php';

blog_boot($mysqli);
$flash = blog_admin_take_flash();
$ready = blog_tables_ready($mysqli);
$categories = [];
if ($ready) {
    $result = $mysqli->query('SELECT c.*, (SELECT COUNT(*) FROM blog_posts p WHERE p.category_id = c.id) AS post_count FROM blog_categories c ORDER BY c.name ASC');
    $categories = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
?>

<?php if (!$ready): ?>
    <div class="admin-alert" role="alert">Import <code>db/blog.sql</code> before using the blog.</div>
<?php endif; ?>
<?php if (!empty($flash['errors'])): ?>
    <div class="admin-alert" role="alert"><?= blog_e(implode(' ', $flash['errors'])) ?></div>
<?php endif; ?>
<?php if (!empty($flash['success'])): ?>
    <div class="admin-alert admin-alert-ok"><?= blog_e($flash['success']) ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h2 class="admin-card-title">Categories</h2>
            <p class="admin-card-subtitle">Topics for the blog. Empty topics stay off the public site.</p>
        </div>
        <a class="btn-admin btn-admin-primary" href="/admin/category-edit">Add category</a>
    </div>
    <div class="admin-table-wrap">
        <div class="table-responsive">
            <table id="example" class="table" style="width:100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Blogs</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= blog_e($category['name']) ?></td>
                        <td><?= blog_e($category['slug']) ?></td>
                        <td><?= (int) $category['post_count'] ?></td>
                        <td class="admin-row-actions">
                            <a href="/admin/category-edit?id=<?= (int) $category['id'] ?>">Edit</a>
                            <form method="post" action="/admin/blog-actions">
                                <input type="hidden" name="csrf" value="<?= blog_e(blog_admin_token()) ?>">
                                <input type="hidden" name="action" value="delete-category">
                                <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
                                <button type="submit" class="btn-admin btn-admin-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
