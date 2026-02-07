# General WordPress Snippets

A collection of utility code snippets for general WordPress maintenance and server-level compatibility.

## Author
**Author Name:** Dicky Ibrohim

## Snippets Included

### 1. WP Trash Posts Without Featured Image or Video
Automatically moves published posts to the trash if they lack both a featured image and a video URL. Useful for cleaning up bulk imports.
- **File:** `wp-trash-posts-without-assets.php`
- **Safeguard:** Processes in batches of 20 to avoid timeouts.

### 2. WP Unset Cloudflare IPCountry Header
Unsets the `HTTP_CF_IPCOUNTRY` variable to bypass geolocation-based restrictions during development or to fix compatibility issues.
- **Folder:** `wp-unset-cloudflare-country-header/`

## Installation Guide

### Method 1: functions.php (Child Theme)
Copy the code (excluding the opening `<?php` tag) and paste it at the bottom of your child theme's `functions.php`.

### Method 2: Code Snippets Plugin (Recommended)
Add a new snippet, paste the code, and set it to run where appropriate. **Note:** Check if your plugin automatically adds `<?php`; if so, omit it from your paste.

### Method 3: MU-Plugin (Best for Performance)
Create a new `.php` file in `wp-content/mu-plugins/` and paste the code. **Note:** You **must** include the opening `<?php` tag at the very top for the code to execute.

**Recommendation:** USE WITH CAUTION. Always backup your database before running cleanup scripts like the trash posts utility.
