<?php

require_once __DIR__ . '/../includes/blog.php';
require_once __DIR__ . '/../includes/blog-admin.php';

$id = (int) ($_GET['id'] ?? 0);
$admin_page_title = $id > 0 ? 'Edit blog' : 'New blog';

require_once __DIR__ . '/header.php';

blog_boot($mysqli);
$flash = blog_admin_take_flash();
$hasOld = !empty($flash['old']);
$categories = [];
if (blog_tables_ready($mysqli)) {
    $result = $mysqli->query('SELECT id, name, slug FROM blog_categories ORDER BY name ASC');
    $categories = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

$post = [
    'category_id' => '',
    'title' => '',
    'slug' => '',
    'summary' => '',
    'content' => '',
    'image_path' => '',
    'image_alt' => '',
    'image_width' => 0,
    'image_height' => 0,
    'author' => '',
    'meta_title' => '',
    'meta_description' => '',
    'status' => 'draft',
    'published_at' => '',
    'noindex' => 0,
    'pin_slider' => 0,
    'pin_slider_order' => 0,
    'pin_home' => 0,
    'pin_home_order' => 0,
    'pin_category' => 0,
    'pin_category_order' => 0,
];
$faqs = [['question' => '', 'answer' => '']];

if ($id > 0) {
    $row = blog_find_post_by_id($mysqli, $id);
    if (!$row) {
        echo '<div class="admin-alert" role="alert">That blog does not exist.</div>';
        require __DIR__ . '/footer.php';
        exit;
    }
    $post = $row;
    $storedFaqs = blog_faqs($mysqli, $id);
    if ($storedFaqs) {
        $faqs = $storedFaqs;
    }
}

$publishedValue = '';
if ($hasOld) {
    $publishedValue = (string) blog_admin_old($flash, 'published_at', '');
} elseif (!empty($post['published_at'])) {
    $publishedValue = (new DateTimeImmutable((string) $post['published_at']))->format('Y-m-d\TH:i');
}

if ($hasOld) {
    $oldQuestions = $flash['old']['faq_question'] ?? [];
    $oldAnswers = $flash['old']['faq_answer'] ?? [];
    $faqs = blog_faq_pairs($oldQuestions, $oldAnswers);
    if (!$faqs) {
        $faqs = [['question' => '', 'answer' => '']];
    }
}

$checked = static function (string $key) use ($flash, $hasOld, $post): bool {
    if ($hasOld) {
        return isset($flash['old'][$key]);
    }
    return !empty($post[$key]);
};

$val = static function (string $key) use ($flash, $post) {
    return blog_admin_old($flash, $key, $post[$key] ?? '');
};

$editorContent = (string) $val('content');
$statusValue = (string) $val('status');
if (!in_array($statusValue, ['draft', 'published', 'scheduled'], true)) {
    $statusValue = 'draft';
}
$categorySlug = 'topic';
foreach ($categories as $category) {
    if ((string) $val('category_id') === (string) $category['id']) {
        $categorySlug = (string) $category['slug'];
    }
}
$linkName = trim((string) $val('slug'));
if ($linkName === '') {
    $linkName = 'article-name';
}
?>

<?php if (!empty($flash['errors'])): ?>
    <div class="admin-alert" role="alert">
        <?php foreach ($flash['errors'] as $error): ?>
            <p><?= blog_e($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

<?php if (!$categories): ?>
    <div class="admin-alert" role="alert">
        <p>Add a topic before you save an article. <a href="/admin/category-edit">Add a topic</a></p>
    </div>
<?php endif; ?>

<form id="blogForm" class="admin-form" method="post" action="/admin/blog-actions" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= blog_e(blog_admin_token()) ?>">
    <input type="hidden" name="action" value="save-post">
    <input type="hidden" name="id" value="<?= $id ?>">

    <div class="admin-form-bar">
        <a class="btn-admin btn-admin-outline" href="/admin/posts">Back</a>
        <button class="btn-admin btn-admin-primary" type="submit">Save</button>
    </div>

    <p class="admin-intro">Write the blog, add a picture, then choose when people can read it. Fields marked Required must be filled in. The other sections can stay empty.</p>

    <div class="admin-split">
        <div class="admin-card">
            <div class="admin-card-header">
                <div>
                    <h2 class="admin-card-title">1. The blog</h2>
                    <p class="admin-card-subtitle">This is the page readers open.</p>
                </div>
            </div>
            <div class="admin-card-body">
                <div class="form-group">
                    <label for="title">Headline <span class="admin-req">Required</span></label>
                    <input class="form-control" id="title" name="title" required value="<?= blog_e((string) $val('title')) ?>" aria-describedby="title-help">
                    <p class="admin-help" id="title-help">The main title at the top of the page.</p>
                </div>
                <div class="form-group">
                    <label for="category_id">Topic <span class="admin-req">Required</span></label>
                    <select class="form-control" id="category_id" name="category_id" required aria-describedby="category-help">
                        <option value="">Choose a topic</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id'] ?>" data-slug="<?= blog_e((string) $category['slug']) ?>"<?= (string) $val('category_id') === (string) $category['id'] ? ' selected' : '' ?>><?= blog_e($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="admin-help" id="category-help">The group this article belongs to, such as Industry notes.</p>
                </div>
                <div class="form-group">
                    <label for="slug">Link name</label>
                    <input class="form-control" id="slug" name="slug" value="<?= blog_e((string) $val('slug')) ?>" aria-describedby="slug-help">
                    <p class="admin-help" id="slug-help">Leave this blank and it is made from the headline. Use short words with hyphens if you want to choose it yourself.</p>
                    <p class="admin-address" id="articleAddress">/blog/<?= blog_e($categorySlug) ?>/<?= blog_e($linkName) ?></p>
                </div>
                <div class="form-group">
                    <label for="summary">Short summary <span class="admin-req">Required</span></label>
                    <textarea class="form-control" id="summary" name="summary" rows="3" required aria-describedby="summary-help"><?= blog_e((string) $val('summary')) ?></textarea>
                    <p class="admin-help" id="summary-help">One or two sentences under the headline. Google uses this if the Google description below is empty.</p>
                </div>
                <div class="form-group">
                    <label id="content-label">Content <span class="admin-req">Required</span></label>
                    <div class="admin-editor">
                        <div id="blogEditor" aria-labelledby="content-label"></div>
                    </div>
                    <textarea id="content" name="content" hidden><?= blog_e($editorContent) ?></textarea>
                    <p class="admin-help" id="content-help">Use the buttons for a heading, bold text, a list, or a link. The page shows the same formatting.</p>
                </div>
                <div class="form-group">
                    <label for="author">Written by <span class="admin-req">Required</span></label>
                    <input class="form-control" id="author" name="author" required value="<?= blog_e((string) $val('author')) ?>" aria-describedby="author-help" autocomplete="name">
                    <p class="admin-help" id="author-help">The name readers see, for example FIDSI Secretariat.</p>
                </div>
            </div>
        </div>

        <div class="admin-stack">
            <div class="admin-card">
                <div class="admin-card-header">
                    <div>
                        <h2 class="admin-card-title">2. When people can read it</h2>
                    </div>
                </div>
                <div class="admin-card-body">
                    <fieldset class="admin-choices">
                        <legend>Choose one</legend>
                        <label class="admin-choice">
                            <input type="radio" name="status" value="draft"<?= $statusValue === 'draft' ? ' checked' : '' ?>>
                            <span><strong>Keep it private</strong><small>Saved here only. It does not appear on the website.</small></span>
                        </label>
                        <label class="admin-choice">
                            <input type="radio" name="status" value="published"<?= $statusValue === 'published' ? ' checked' : '' ?>>
                            <span><strong>Show it now</strong><small>Readers can open it on the blog.</small></span>
                        </label>
                        <label class="admin-choice">
                            <input type="radio" name="status" value="scheduled"<?= $statusValue === 'scheduled' ? ' checked' : '' ?>>
                            <span><strong>Show it later</strong><small>It stays hidden until the date below.</small></span>
                        </label>
                    </fieldset>
                    <div class="form-group">
                        <label for="published_at">Date and time</label>
                        <input class="form-control" id="published_at" name="published_at" type="datetime-local" value="<?= blog_e($publishedValue) ?>" aria-describedby="date-help">
                        <p class="admin-help" id="date-help">For “Show it now”, leave this empty to use the current time. For “Show it later”, pick a future date.</p>
                    </div>
                    <label class="admin-check">
                        <input type="checkbox" name="noindex" value="1"<?= $checked('noindex') ? ' checked' : '' ?>>
                        Keep this page out of Google
                    </label>
                    <p class="admin-help">The page still opens for anyone who has the link.</p>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <div>
                        <h2 class="admin-card-title">3. Picture</h2>
                        <p class="admin-card-subtitle">Shown at the top of the article and on the blog list.</p>
                    </div>
                </div>
                <div class="admin-card-body">
                    <?php if (!empty($post['image_path'])): ?>
                        <img class="admin-image-preview" src="<?= blog_e((string) $post['image_path']) ?>" alt="<?= blog_e((string) $post['image_alt']) ?>" width="<?= (int) $post['image_width'] ?>" height="<?= (int) $post['image_height'] ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="image">Choose a picture<?= $id === 0 ? ' <span class="admin-req">Required</span>' : '' ?></label>
                        <input class="form-control" id="image" name="image" type="file" accept="image/jpeg,image/png,image/gif,image/webp" aria-describedby="image-help">
                        <p class="admin-help" id="image-help">JPG, PNG, GIF, or WebP, up to 5 MB.<?= $id > 0 ? ' Choose a new file only when you want to replace the picture.' : '' ?></p>
                    </div>
                    <div class="form-group">
                        <label for="image_alt">What is in the picture? <span class="admin-req">Required</span></label>
                        <input class="form-control" id="image_alt" name="image_alt" required value="<?= blog_e((string) $val('image_alt')) ?>" aria-describedby="alt-help">
                        <p class="admin-help" id="alt-help">A short description for people who cannot see the picture. Example: FIDSI team at a meeting.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2 class="admin-card-title">4. Google listing</h2>
                <p class="admin-card-subtitle">Optional. Leave both empty to use the headline and the short summary.</p>
            </div>
        </div>
        <div class="admin-card-body">
            <div class="form-group">
                <label for="meta_title">Title in Google</label>
                <input class="form-control" id="meta_title" name="meta_title" value="<?= blog_e((string) $val('meta_title')) ?>" aria-describedby="meta-title-help">
                <p class="admin-help" id="meta-title-help">The blue link people see in search. The heading on your page stays the headline.</p>
            </div>
            <div class="form-group">
                <label for="meta_description">Description in Google</label>
                <textarea class="form-control" id="meta_description" name="meta_description" rows="3" aria-describedby="meta-description-help"><?= blog_e((string) $val('meta_description')) ?></textarea>
                <p class="admin-help" id="meta-description-help">About two short sentences under that link.</p>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2 class="admin-card-title">5. Feature this article</h2>
                <p class="admin-card-subtitle">Optional. Tick a box only when this article should stay near the top. A lower number is shown first.</p>
            </div>
        </div>
        <div class="admin-card-body admin-pin-grid">
            <div>
                <label class="admin-check"><input type="checkbox" name="pin_slider" value="1"<?= $checked('pin_slider') ? ' checked' : '' ?>> Large pictures on the blog home</label>
                <p class="admin-help">Up to 5 articles.</p>
                <label for="pin_slider_order">Place in that row</label>
                <input class="form-control" id="pin_slider_order" name="pin_slider_order" type="number" min="0" value="<?= (int) $val('pin_slider_order') ?>">
            </div>
            <div>
                <label class="admin-check"><input type="checkbox" name="pin_home" value="1"<?= $checked('pin_home') ? ' checked' : '' ?>> Top of the blog list</label>
                <p class="admin-help">Up to 3 articles.</p>
                <label for="pin_home_order">Place in that list</label>
                <input class="form-control" id="pin_home_order" name="pin_home_order" type="number" min="0" value="<?= (int) $val('pin_home_order') ?>">
            </div>
            <div>
                <label class="admin-check"><input type="checkbox" name="pin_category" value="1"<?= $checked('pin_category') ? ' checked' : '' ?>> Top of this topic’s list</label>
                <p class="admin-help">Up to 3 articles.</p>
                <label for="pin_category_order">Place in that list</label>
                <input class="form-control" id="pin_category_order" name="pin_category_order" type="number" min="0" value="<?= (int) $val('pin_category_order') ?>">
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2 class="admin-card-title">6. Common questions</h2>
                <p class="admin-card-subtitle">Optional. These appear at the bottom of the article. Leave a row empty if you do not need it.</p>
            </div>
        </div>
        <div class="admin-card-body">
            <div id="faqRows">
                <?php foreach ($faqs as $index => $faq): ?>
                    <div class="admin-faq-row">
                        <div class="form-group">
                            <label for="faq-question-<?= (int) $index ?>">Question</label>
                            <input class="form-control" id="faq-question-<?= (int) $index ?>" name="faq_question[]" value="<?= blog_e((string) $faq['question']) ?>">
                        </div>
                        <div class="form-group">
                            <label for="faq-answer-<?= (int) $index ?>">Answer</label>
                            <textarea class="form-control" id="faq-answer-<?= (int) $index ?>" name="faq_answer[]" rows="2"><?= blog_e((string) $faq['answer']) ?></textarea>
                        </div>
                        <button class="btn-admin btn-admin-outline admin-faq-remove" type="button">Remove this question</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="btn-admin btn-admin-outline" type="button" id="addFaq">Add another question</button>
        </div>
    </div>
</form>

<template id="faqTemplate">
    <div class="admin-faq-row">
        <div class="form-group">
            <label for="faq-question-new">Question</label>
            <input class="form-control" id="faq-question-new" name="faq_question[]" value="">
        </div>
        <div class="form-group">
            <label for="faq-answer-new">Answer</label>
            <textarea class="form-control" id="faq-answer-new" name="faq_answer[]" rows="2"></textarea>
        </div>
        <button class="btn-admin btn-admin-outline admin-faq-remove" type="button">Remove this question</button>
    </div>
</template>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
var blogQuill = new Quill('#blogEditor', {
    theme: 'snow',
    placeholder: 'Write the blog here.',
    modules: {
        toolbar: [
            [{ header: [2, 3, false] }],
            ['bold', 'italic', 'underline'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['blockquote', 'link'],
            ['clean']
        ]
    }
});
var blogContent = document.getElementById('content');
if (blogContent.value.trim() !== '') {
    blogQuill.clipboard.dangerouslyPasteHTML(blogContent.value);
}
document.getElementById('blogForm').addEventListener('submit', function () {
    blogContent.value = blogQuill.getSemanticHTML();
});
document.getElementById('addFaq').addEventListener('click', function () {
    var row = document.getElementById('faqTemplate').content.cloneNode(true);
    var stamp = String(Date.now());
    row.querySelectorAll('[id]').forEach(function (field) {
        var label = row.querySelector('label[for="' + field.id + '"]');
        field.id = field.id + '-' + stamp;
        if (label) {
            label.setAttribute('for', field.id);
        }
    });
    document.getElementById('faqRows').appendChild(row);
});
document.getElementById('faqRows').addEventListener('click', function (event) {
    var button = event.target.closest('.admin-faq-remove');
    if (!button) {
        return;
    }
    var row = button.closest('.admin-faq-row');
    if (row) {
        row.remove();
    }
});
(function () {
    var title = document.getElementById('title');
    var slug = document.getElementById('slug');
    var category = document.getElementById('category_id');
    var address = document.getElementById('articleAddress');
    function linkName(value) {
        return value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }
    function refreshAddress() {
        var option = category.options[category.selectedIndex];
        var topic = option && option.getAttribute('data-slug') ? option.getAttribute('data-slug') : 'topic';
        var name = slug.value.trim() || linkName(title.value) || 'article-name';
        address.textContent = '/blog/' + topic + '/' + name;
    }
    title.addEventListener('input', refreshAddress);
    slug.addEventListener('input', refreshAddress);
    category.addEventListener('change', refreshAddress);
})();
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
