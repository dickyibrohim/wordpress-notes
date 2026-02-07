# WP Auto-Set Featured Image

A code snippet that automatically picks the first attached image and sets it as the featured image if none is present.

## Author
**Author Name:** Dicky Ibrohim

## How it Works
The script hooks into `save_post` and other publishing actions. If no thumbnail is set, it queries the post's children for the first image attachment and applies it.

## Installation Guide

### Method 1: functions.php (Child Theme)
Copy the code (excluding the opening `<?php` tag) and paste it at the bottom of your child theme's `functions.php`.

### Method 2: Code Snippets Plugin (Recommended)
Add a new snippet, paste the code, and set it to run where appropriate. **Note:** Check if your plugin automatically adds `<?php`; if so, omit it from your paste.

### Method 3: MU-Plugin (Best for Performance)
Create a new `.php` file in `wp-content/mu-plugins/` and paste the code. **Note:** You **must** include the opening `<?php` tag at the very top for the code to execute.
