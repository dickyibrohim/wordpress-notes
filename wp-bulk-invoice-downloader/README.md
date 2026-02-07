# WP Bulk Invoice Downloader

A refactored code snippet to facilitate bulk downloading and renaming of invoice PDFs from the WordPress admin dashboard.

## Author
**Author Name:** Dicky Ibrohim
*Crafted like a premium WordPress plugin.*

## Features
- **Automatic Renaming:** Files are renamed using the format `FP_CompanyName_InvoiceNumber_Date.pdf`.
- **Bulk Action:** Triggers downloads automatically for each row in the table.
- **Cleanup Helper:** Helps you manage your downloads by asking for a folder handle to attempt automatic deletion of original files after renaming.

## Step-by-Step Usage
1. **Selection:** Navigate to the admin page containing your invoice table.
2. **Grant Access:** When prompted, select your browser's "Downloads" folder. This allows the script to help manage the files.
3. **Execution:** The script will automatically start clicking "Download" for each row.
4. **Rename & Save:** For each file, a green button will appear at the bottom-left of your screen. Click it, select the file you just downloaded, and then confirm the save location (it will suggest the new name automatically).
5. **Repeat:** The script will proceed to the next row until finished.

## Customization
If you need to adapt this script for a different table structure or file naming convention, you can modify the following sections in `wp-bulk-invoice-downloader.php`:

### 1. Table Selector
If your invoice list uses a different table structure, update this line:
```javascript
const rows = document.querySelectorAll("table tbody tr");
```

### 2. Data Extraction
To change which columns the script pulls data from (e.g., Company Name, Date), modify these lines (index starts at 0):
```javascript
const companyName = row.children[3]?.innerText.trim() // Column 4
const invoiceNumber = row.children[5]?.innerText.trim() // Column 6
const invoiceDate = row.children[6]?.innerText.trim() // Column 7
```

### 3. File Name Format
To change how the downloaded file is named, edit this variable:
```javascript
const fileName = `FP_${companyName}_${invoiceNumber}_${invoiceDate}.pdf`;
```

## Installation Guide

### Method 1: functions.php (Child Theme)
Copy the code (excluding the opening `<?php` tag) and paste it at the bottom of your child theme's `functions.php`.

### Method 2: Code Snippets Plugin (Recommended)
Add a new snippet, paste the code, and set it to run where appropriate. **Note:** Check if your plugin automatically adds `<?php`; if so, omit it from your paste.

### Method 3: MU-Plugin (Best for Performance)
Create a new `.php` file in `wp-content/mu-plugins/` (e.g., `wp-bulk-invoice-downloader.php`) and paste the code. **Note:** You **must** include the opening `<?php` tag at the very top for the code to execute.

### Comparison: Which is best?
| Method | Ease of Use | Persistence | Performance |
| :--- | :--- | :--- | :--- |
| **functions.php** | Easy | Lost on theme change | Normal |
| **Snippet Plugin** | **Very Easy** | Permanent | Good |
| **MU-Plugin** | Moderate | **High** | **Best** |

**Recommendation:** Use a **Snippet Plugin** if you want a user-friendly interface to manage your code, or **MU-Plugin** if you want the most stable and performant solution that stays active regardless of theme or plugin changes.
