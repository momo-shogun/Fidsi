<?php include_once __DIR__ . '/header.php';

$contact_count = 0;
$join_count = 0;
$subscribe_count = 0;

$contact_result = $mysqli->query('SELECT COUNT(*) AS total FROM fds_contact');
if ($contact_result) {
    $contact_count = (int) $contact_result->fetch_assoc()['total'];
}

$join_result = $mysqli->query('SELECT COUNT(*) AS total FROM fds_join');
if ($join_result) {
    $join_count = (int) $join_result->fetch_assoc()['total'];
}

$subscribe_result = $mysqli->query('SELECT COUNT(*) AS total FROM subscribe');
if ($subscribe_result) {
    $subscribe_count = (int) $subscribe_result->fetch_assoc()['total'];
}

$blog_count = 0;
$blog_check = $mysqli->query("SHOW TABLES LIKE 'blog_posts'");
if ($blog_check && $blog_check->num_rows > 0) {
    $blog_result = $mysqli->query('SELECT COUNT(*) AS total FROM blog_posts');
    if ($blog_result) {
        $blog_count = (int) $blog_result->fetch_assoc()['total'];
    }
}
?>

<section class="dash-hero">
    <p class="dash-kicker">FIDSI</p>
    <h2>What needs a look today</h2>
    <p>Messages, membership leads, subscribers, and the blog.</p>
</section>

<div class="stat-grid">
    <a href="/admin/contact" class="stat-card stat-card-contact">
        <div class="stat-card-top">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
            </div>
        </div>
        <p class="stat-label">Contact</p>
        <p class="stat-value"><?= number_format($contact_count) ?></p>
        <p class="stat-hint">Open messages</p>
    </a>

    <a href="/admin/join" class="stat-card stat-card-join">
        <div class="stat-card-top">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                </svg>
            </div>
        </div>
        <p class="stat-label">Join</p>
        <p class="stat-value"><?= number_format($join_count) ?></p>
        <p class="stat-hint">Open leads</p>
    </a>

    <a href="/admin/subscribe" class="stat-card stat-card-subscribe">
        <div class="stat-card-top">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
            </div>
        </div>
        <p class="stat-label">Subscribe</p>
        <p class="stat-value"><?= number_format($subscribe_count) ?></p>
        <p class="stat-hint">Open subscribers</p>
    </a>

    <a href="/admin/posts" class="stat-card stat-card-blog">
        <div class="stat-card-top">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z" />
                </svg>
            </div>
        </div>
        <p class="stat-label">Blog</p>
        <p class="stat-value"><?= number_format($blog_count) ?></p>
        <p class="stat-hint">Open the blog</p>
    </a>
</div>

<?php include_once __DIR__ . '/footer.php'; ?>
