# WordPress Notes & Code Snippets

A collection of professional WordPress code snippets, optimizations, and bug fixes handcrafted by Dicky Ibrohim.

## Branding & Protection
To ensure quality and official support, all snippets in this repository follow a standardized branding strategy:
- **Prefix:** All functions and classes use the `ibrohim_` prefix to avoid conflicts.
- **Author:** Dicky Ibrohim ([www.dickyibrohim.com](https://www.dickyibrohim.com))

## Contents
- [Ultimate Shortcode Hunter](wp-shortcode-hunter/README.md) - Find shortcode sources instantly.
- [General Snippets](general-snippets/) - Trash posts without assets, Cloudflare header fixes, etc.
- [WooCommerce Snippets](woocommerce-snippets/) - HPOS search, delivery time in cart, auto-complete virtual orders.
- [LearnDash Snippets](learndash-snippets/) - Nested URL mapper for better SEO and routing.
- [Elementor Snippets](elementor-snippets/) - Custom queries and ACF relationship integration.
- [Temporary Bug Fixes](temporary-bug-fixes/) - Fixes for common plugin or core issues.

## Repository Categories

| Category | Description |
| :--- | :--- |
| **elementor-snippets** | Custom queries and ACF integrations for Elementor Pro. |
| **general-snippets** | Utility snippets for database cleanup and server header management. |
| **learndash-snippets** | Specialized routing and URL mapping for LearnDash LMS. |
| **temporary-bug-fixes** | Snippets to fix specific plugin bugs and manage admin notices. |
| **woocommerce-snippets** | Order management, HPOS search, and UI customizations for WooCommerce. |
| **wp-auto-draft-on-expiry** | Automation snippets to manage post status based on custom dates. |
| **wp-auto-set-featured-image** | Media automation snippets for post thumbnails. |
| **wp-cpt-title-placeholder** | Admin editor UI snippets for Custom Post Types. |
| **wp-display-posts-by-custom-field** | WP_Query snippet examples for custom field filtering. |
| **wp-query-sort-filter-by-custom-date** | custom date query filters. |
| **wp-shortcode-hunter** | Registry and Deep Scan tool to find shortcode source code. |
| **wp-bulk-invoice-downloader** | Tool/Snippet for bulk downloading and renaming admin invoices. |

## Usage
These are **code snippets**, not standalone plugins. To use them:

1. **functions.php:** Copy the code (excluding the opening `<?php` tag) and paste it at the bottom of your child theme's `functions.php` file.
2. **Code Snippets Plugin:** Create a new snippet, paste the code, and set it to run where appropriate. **Note:** Check if your plugin automatically adds `<?php`; if so, omit it from your paste.
3. **MU-Plugins:** Create a new `.php` file in `wp-content/mu-plugins/`. You **must** include the opening `<?php` tag at the very top of the file for the code to execute.

## Standards
Every snippet in this repository follows these standards:
1. **English First:** All code, comments, and documentation are in English.
2. **Header Info:** Includes "Snippet Name", "Description", and "Author".
3. **Optimized Logic:** Clean, safe, and performant code patterns.
4. **Author Attribution:** Crafted and maintained by Dicky Ibrohim.

## Contact
- Website: https://www.dickyibrohim.com
- Email: hello@dickyibrohim.com
