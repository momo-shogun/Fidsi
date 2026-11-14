-- FIDSI blog tables. Import into the database named in setting.php.

CREATE TABLE IF NOT EXISTS blog_categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(191) NOT NULL,
  slug VARCHAR(191) NOT NULL,
  meta_title VARCHAR(255) DEFAULT NULL,
  meta_description VARCHAR(500) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_blog_categories_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_posts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id INT UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(191) NOT NULL,
  summary TEXT NOT NULL,
  content MEDIUMTEXT NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  image_alt VARCHAR(255) NOT NULL,
  image_width INT UNSIGNED NOT NULL DEFAULT 0,
  image_height INT UNSIGNED NOT NULL DEFAULT 0,
  author VARCHAR(191) NOT NULL,
  meta_title VARCHAR(255) DEFAULT NULL,
  meta_description VARCHAR(500) DEFAULT NULL,
  status ENUM('draft', 'published', 'scheduled') NOT NULL DEFAULT 'draft',
  published_at DATETIME DEFAULT NULL,
  content_modified_at DATETIME NOT NULL,
  noindex TINYINT(1) NOT NULL DEFAULT 0,
  pin_slider TINYINT(1) NOT NULL DEFAULT 0,
  pin_slider_order INT UNSIGNED NOT NULL DEFAULT 0,
  pin_home TINYINT(1) NOT NULL DEFAULT 0,
  pin_home_order INT UNSIGNED NOT NULL DEFAULT 0,
  pin_category TINYINT(1) NOT NULL DEFAULT 0,
  pin_category_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_blog_posts_category_slug (category_id, slug),
  KEY idx_blog_posts_public (status, published_at),
  KEY idx_blog_posts_category (category_id),
  CONSTRAINT fk_blog_posts_category FOREIGN KEY (category_id) REFERENCES blog_categories (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_post_faqs (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  post_id INT UNSIGNED NOT NULL,
  question VARCHAR(500) NOT NULL,
  answer TEXT NOT NULL,
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_blog_post_faqs_post (post_id, sort_order),
  CONSTRAINT fk_blog_post_faqs_post FOREIGN KEY (post_id) REFERENCES blog_posts (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS blog_redirects (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  old_path VARCHAR(255) NOT NULL,
  post_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_blog_redirects_path (old_path),
  KEY idx_blog_redirects_post (post_id),
  CONSTRAINT fk_blog_redirects_post FOREIGN KEY (post_id) REFERENCES blog_posts (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
