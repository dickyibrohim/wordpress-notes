# Ultimate Shortcode Hunter (Level 2)

**Snippet Name:** Ultimate Shortcode Hunter (Level 2)  
**Description:** High-precision registry reflection (including "Nuclear" mode) with Deep Physical File Scan fallback.  
**Author:** Dicky Ibrohim  
**Version:** 2.3 (Super Power + Deep Scan)

## Problem
Many developers wrap their shortcode registration in `if (!is_admin())` checks. This means the shortcode won't appear in the `global $shortcode_tags` registry when you are in the WordPress dashboard (`wp-admin`). This snippet helps you find the source of any shortcode by checking both the registry and performing a physical file scan.

## Installation

### Method: MU-Plugin (Recommended)
This approach ensures the tool is always available and runs early enough to catch all registrations.

1.  Create a new file named `00-dicky-shortcode-hunter.php` in your `/wp-content/mu-plugins/` directory.
2.  Copy and paste the code from [wp-shortcode-hunter.php](wp-shortcode-hunter.php) into this file.
3.  **Important:** Ensure the opening `<?php` tag is at the very top.

### Method: functions.php or Code Snippets
You can also use this in your child theme's `functions.php` or a plugin like Code Snippets. If doing so, omit the opening `<?php` tag.

## Usage

Once installed, you can trigger the hunter by adding a parameter to your URL.

### 1. Front-End Check (Standard)
Visit any page on your front-end and append `?find_shortcode=your_shortcode_name`.

Example:
`https://yoursite.com/?find_shortcode=my_test_shortcode`

### 2. Admin Dashboard Check
If you are troubleshooting something specifically in the admin, use it there:

Example:
`https://yoursite.com/wp-admin/?find_shortcode=my_test_shortcode`

### 3. The "Nuclear" Option (Find Everything)
If you still can't find it, or simply want to see every shortcode currently active on your system:

Example:
`https://yoursite.com/?find_shortcode=all`

This will generate a scrollable list of all registered shortcodes and their exact file sources.

---

## Sample Outputs

### Case A: Found in Registry
This occurs when the shortcode is "officially" registered and active in the current context.

```text
🕵️ Hunter Target: [my_active_shortcode]
Context: Front-End

✅ FOUND in Registry!
File: /wp-content/plugins/some-plugin/functions.php
Line: 142
```

### Case B: Not in Registry, Deep Scan Triggered
This occurs when the shortcode is registered conditionally (e.g., hidden from admin) or is a "fake" shortcode (manual string replace).

```text
🕵️ Hunter Target: [my_hidden_shortcode]
Context: Admin Dashboard

❌ Not in Registry. Starting Deep File Scan...
Scanning /wp-content/plugins/ and /themes/... This may take a moment.

📂 .../plugins/my-custom-plugin/core-logic.php
📍 Line 56: add_shortcode('my_hidden_shortcode', 'my_custom_callback');

--- Scan Complete ---
```

### Case C: Not Found
The code will finish the scan and report completion without finding matches.

```text
🕵️ Hunter Target: [non_existent_code]
Context: Front-End

❌ Not in Registry. Starting Deep File Scan...
Scanning /wp-content/plugins/ and /themes/... This may take a moment.

--- Scan Complete ---
```
