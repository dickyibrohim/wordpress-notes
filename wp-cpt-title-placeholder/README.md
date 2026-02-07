# WP CPT Title Placeholder

A code snippet to customize the "Enter title here" text in the WordPress editor for specific post types.

## Author
**Author Name:** Dicky Ibrohim

## Customization
In `wp-cpt-title-placeholder.php`:
- Update `YOUR-CPT-SLUG-HERE` to your desired post type.
- Edit the return string to your preferred placeholder text.

## Installation Guide

### Method 1: functions.php (Child Theme)
Copy the code (excluding the opening `<?php` tag) and paste it at the bottom of your child theme's `functions.php`.

### Method 2: Code Snippets Plugin (Recommended)
Add a new snippet, paste the code, and set it to run where appropriate. **Note:** Check if your plugin automatically adds `<?php`; if so, omit it from your paste.

### Method 3: MU-Plugin (Best for Performance)
Create a new `.php` file in `wp-content/mu-plugins/` and paste the code. **Note:** You **must** include the opening `<?php` tag at the very top for the code to execute.
