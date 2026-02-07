# WP Display Posts by Custom Field

A utility code snippet to query and filter WordPress posts based on specific custom field keys and values.

## Author
**Author Name:** Dicky Ibrohim

## Files Included

### 1. WP Query Posts by Custom Field
A standard WordPress `WP_Query` snippet that you can use in your theme or plugins.
- **File:** `wp-posts-query-by-custom-field.php`
- **Default:** Filters posts that have `photos = 0` AND `videos = 0`.

### 2. MySQL Raw Query
A raw SQL snippet for use directly in database management tools like phpMyAdmin or Sequel Ace.
- **File:** `query.sql`

## Installation Guide (PHP Snippet)

### Method 1: functions.php (Child Theme)
Copy the code (excluding the opening `<?php` tag) and paste it at the bottom of your child theme's `functions.php`.

### Method 2: Code Snippets Plugin (Recommended)
Add a new snippet, paste the code, and set it to run where appropriate. **Note:** Check if your plugin automatically adds `<?php`; if so, omit it from your paste.

### Method 3: MU-Plugin (Best for Performance)
Create a new `.php` file in `wp-content/mu-plugins/` and paste the code. **Note:** You **must** include the opening `<?php` tag at the very top for the code to execute.

**Recommendation:** If you are running this as a one-time cleanup, the **MySQL Query** is often faster. For dynamic display on your website, use the **WP_Query** snippet via the **Code Snippets** plugin.
