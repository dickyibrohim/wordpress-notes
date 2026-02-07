# Temporary Snippet Fixes

A collection of specialized code snippets to resolve temporary bugs or limitations in WordPress and third-party plugins.

## Author
**Author Name:** Dicky Ibrohim

## Snippets Included

### 1. WC Packing Slip Bulk Action Notice
Provides a reliable success banner for bulk actions related to packing slips and labels in WooCommerce. This ensures that administrators receive feedback even when default notices are lost during redirects.
- **File:** `wc-packslip-bulk-notice.php`
- **Features:** Persistent notices using user meta, dismissible banners, and support for various Shiptastic and Germanized Pro hooks.

## Installation Guide

### Method 1: functions.php (Child Theme)
Copy the code (excluding the opening `<?php` tag) and paste it at the bottom of your child theme's `functions.php`.

### Method 2: Code Snippets Plugin (Recommended)
Add a new snippet, paste the code, and set it to run where appropriate. **Note:** Check if your plugin automatically adds `<?php`; if so, omit it from your paste.

### Method 3: MU-Plugin (Best for Performance)
Create a new `.php` file in `wp-content/mu-plugins/` and paste the code. **Note:** You **must** include the opening `<?php` tag at the very top for the code to execute.

**Recommendation:** Since this snippet affects the administrative workflow, it is best kept as a **Snippet Plugin** or **MU-Plugin** to ensure it persists across theme updates.
