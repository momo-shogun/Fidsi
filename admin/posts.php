<?php

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../includes/blog.php';
require_once __DIR__ . '/../includes/blog-admin.php';

blog_boot($mysqli);
$flash = blog_admin_take_flash();
$ready = blog_tables_ready($mysqli);
$posts = [];
if ($ready) {
    $sql = 'SELECT p.id, p.title, p.status, p.published_at, p.pin_slider, p.pin_home, p.pin_category, c.name AS category_name
        FROM blog_posts p
        INNER JOIN blog_categories c ON c.id = p.category_id
        ORDER BY p.id DESC';
    $result = $mysqli->query($sql);
    $posts = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
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
            <h2 class="admin-card-title">Blog</h2>
            <p class="admin-card-subtitle">Private blogs stay in this list. A blog set for later appears on the website at its date.</p>
        </div>
        <a class="btn-admin btn-admin-primary" href="/admin/post-edit">Add blog</a>
    </div>
    <div class="admin-table-wrap">
        <div class="table-responsive">
            <table id="example" class="table" style="width:100%">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Pins</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($posts as $post): ?>
                    <?php
                    $pins = [];
                    if (!empty($post['pin_slider'])) {
                        $pins[] = 'Slider';
                    }
                    if (!empty($post['pin_home'])) {
                        $pins[] = 'Home';
                    }
                    if (!empty($post['pin_category'])) {
                        $pins[] = 'Category';
                    }
                    ?>
                    <tr>
                        <td><?= blog_e($post['title']) ?></td>
                        <td><?= blog_e($post['category_name']) ?></td>
                        <td><span class="admin-badge admin-badge-<?= blog_e($post['status']) ?>"><?= blog_e($post['status']) ?></span></td>
                        <td><?= blog_e(blog_display_date($post['published_at'])) ?></td>
                        <td><?= blog_e($pins ? implode(', ', $pins) : '—') ?></td>
                        <td class="admin-row-actions">
                            <a href="/admin/post-edit?id=<?= (int) $post['id'] ?>">Edit</a>
                            <form method="post" action="/admin/blog-actions">
                                <input type="hidden" name="csrf" value="<?= blog_e(blog_admin_token()) ?>">
                                <input type="hidden" name="action" value="delete-post">
                                <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
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
