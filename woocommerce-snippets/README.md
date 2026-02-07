# WooCommerce Snippets

A collection of utility code snippets to enhance and customize your WooCommerce store workflows.

## Author
**Author Name:** Dicky Ibrohim

## Snippets Included

### 1. WC Auto-Complete Virtual Orders
Automatically completes orders if all items are virtual or downloadable. It intelligently skips manual bank transfers (BACS) to ensure payment is received first.
- **File:** `wc-auto-complete-virtual-orders.php`

### 2. WC Hide Price Suffix
Removes the price display suffix (e.g., "incl. VAT") on the main shop and individual product pages to keep the design clean.
- **File:** `wc-hide-price-suffix.php`

### 3. WC HPOS Search by Custom Order Number
Two variants that enable searching for orders by custom or formatted order numbers when using High-Performance Order Storage (HPOS).
- **Files:** `wc-hpos-order-number-search.php` and `wc-hpos-order-number-search-auto-open.php`

### 4. WC Display Delivery Time in Cart
Displays product delivery time information (from 'product_delivery_time' taxonomy) directly under the product name on the cart page.
- **File:** `wc-cart-delivery-time.php`

## Installation Guide

### Method 1: functions.php (Child Theme)
Copy the code (excluding the opening `<?php` tag) and paste it at the bottom of your child theme's `functions.php`.

### Method 2: Code Snippets Plugin (Recommended)
Add a new snippet, paste the code, and set it to run where appropriate. **Note:** Check if your plugin automatically adds `<?php`; if so, omit it from your paste.

### Method 3: MU-Plugin (Best for Performance)
Create a new `.php` file in `wp-content/mu-plugins/` and paste the code. **Note:** You **must** include the opening `<?php` tag at the very top for the code to execute.

**Recommendation:** For HPOS-related snippets, we recommend using Method 2 or 3 to ensure they are always active for your administrative workflow.
