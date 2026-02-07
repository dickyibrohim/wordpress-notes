# WP Query Sort & Filter by Custom Date

A specialized Elementor code snippet to sort by date and hide expired posts.

## Author
**Author Name:** Dicky Ibrohim

## Customization
In `wp-query-sort-by-custom-date.php`:
- **Query ID:** Ensure `event_sort_date` matches the Query ID in your Elementor widget.
- **Field Name:** Change `anfangsdatum` to your date field name.

## Installation Guide

### Method 1: functions.php (Child Theme)
Copy the code (excluding the opening `<?php` tag) and paste it at the bottom of your child theme's `functions.php`.

### Method 2: Code Snippets Plugin (Recommended)
Add a new snippet, paste the code, and set it to run where appropriate. **Note:** Check if your plugin automatically adds `<?php`; if so, omit it from your paste.

### Method 3: MU-Plugin (Best for Performance)
Create a new `.php` file in `wp-content/mu-plugins/` and paste the code. **Note:** You **must** include the opening `<?php` tag at the very top for the code to execute.
