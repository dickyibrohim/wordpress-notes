# Elementor Snippets

A collection of specialized code snippets for Elementor Pro to extend query capabilities and ACF integrations.

## Author
**Author Name:** Dicky Ibrohim

## Step-by-Step Usage
1. **Setup ACF:** Create a Relationship field in ACF and attach it to your post type.
2. **Apply Snippet:** Install the snippet using one of the methods below.
3. **Configure Widget:** In Elementor, add a Posts or Loop Grid widget. Under **Query**, set the **Custom Query ID** to `relatedPosts`.
4. **Publish:** Save and view a single post to see your manually selected related posts.

## Customization
You can modify the following in `elementor-custom-query-acf-relationship.php`:
- **Query ID:** Change `relatedPosts` in the hook to match your Elementor setting.
- **Post Type:** Update `$post_type` to your specific Post Type slug.
- **ACF Field:** Update `$relationship_field` to your ACF field name.

## Installation Guide

### Method 1: functions.php (Child Theme)
Copy the code (excluding the opening `<?php` tag) and paste it at the bottom of your child theme's `functions.php`.

### Method 2: Code Snippets Plugin (Recommended)
Add a new snippet, paste the code, and set it to run where appropriate. **Note:** Check if your plugin automatically adds `<?php`; if so, omit it from your paste.

### Method 3: MU-Plugin (Best for Performance)
Create a new `.php` file in `wp-content/mu-plugins/` and paste the code. **Note:** You **must** include the opening `<?php` tag at the very top for the code to execute.

**Recommendation:** Use a **Snippet Plugin** for easy management or **MU-Plugin** for stability.
