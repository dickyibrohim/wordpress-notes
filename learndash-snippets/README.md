# LearnDash Snippets

Specialized code snippets for LearnDash LMS to improve routing and custom URL mapping.

## Author
**Author Name:** Dicky Ibrohim

## Snippets Included

### 1. LearnDash Nested URL Mapper
A highly technical script to fix 404 errors for nested lessons, topics, and quizzes. It is Polylang-aware and handles access validation.
- **File:** `wp-learndash-nested-url-mapper.php`
- **Features:** Strict CPT validation, redirect guard to prevent ping-pong loops, and stable gate URLs for unauthorized users.

## Installation Guide

### Method 1: functions.php (Child Theme)
Copy the code (excluding the opening `<?php` tag) and paste it at the bottom of your child theme's `functions.php`.

### Method 2: Code Snippets Plugin (Recommended)
Add a new snippet, paste the code, and set it to run where appropriate. **Note:** Check if your plugin automatically adds `<?php`; if so, omit it from your paste.

### Method 3: MU-Plugin (Best for Performance)
Create a new `.php` file in `wp-content/mu-plugins/` and paste the code. **Note:** You **must** include the opening `<?php` tag at the very top for the code to execute.

**Recommendation:** For routing scripts like this, using an **MU-Plugin** is the most robust method as it ensures the code is always active regardless of theme changes.
