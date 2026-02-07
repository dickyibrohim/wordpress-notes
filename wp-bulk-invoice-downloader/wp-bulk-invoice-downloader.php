<?php
/**
 * Snippet Name: WP Bulk Invoice Downloader
 * Description: Refactored tool for bulk downloading and renaming admin invoices with professional English translations.
 * Version: 1.0.0
 * Author: Dicky Ibrohim
 * Author URI: https://www.dickyibrohim.com
 */

/**
 * Step-by-Step Usage:
 * 1. Navigate to the page containing the invoice table.
 * 2. Select the download folder when prompted (required for automatic cleanup if supported).
 * 3. The script will automatically trigger the download for each row.
 * 4. Click the green button at the bottom left to confirm and rename each file.
 */

add_action('admin_footer', 'ibrohim_bulk_invoice_downloader_script');

function ibrohim_bulk_invoice_downloader_script() {
    ?>
    <script type="text/javascript">
    (async function() {
        const rows = document.querySelectorAll("table tbody tr");
        let index = 0;
        let button = null;
        let isProcessing = false;
        let directoryHandle = null;

        async function requestDownloadFolder() {
            try {
                directoryHandle = await window.showDirectoryPicker();
                console.log("📁 Download folder approved:", directoryHandle.name);
            } catch (error) {
                console.error("❌ Failed to access download folder.", error);
                alert("⚠️ You must select a download folder for automatic file deletion to work.");
            }
        }

        async function downloadNext() {
            if (index >= rows.length) {
                console.log("✅ All files have been processed.");
                removeButton();
                return;
            }

            if (isProcessing) return;
            isProcessing = true;

            const row = rows[index];
            const companyName = row.children[3]?.innerText.trim()
                .replace(/[^a-zA-Z0-9\s]/g, "")
                .replace(/\s+/g, "_");
            
            const invoiceNumber = row.children[5]?.innerText.trim().replace(/\s+/g, "");
            const invoiceDate = row.children[6]?.innerText.trim().replace(/\s+/g, "");

            const fileName = `FP_${companyName}_${invoiceNumber}_${invoiceDate}.pdf`;
            const downloadButton = row.querySelector("#DownloadButton");

            if (downloadButton) {
                console.log(`⬇️ Downloading file: ${fileName}...`);
                downloadButton.click();
                showButton(fileName);
            } else {
                index++;
                isProcessing = false;
                downloadNext();
            }
        }

        function showButton(fileName) {
            removeButton();
            button = document.createElement("button");
            button.innerText = `Click here to select & rename file to: ${fileName}`;
            button.style.position = "fixed";
            button.style.bottom = "20px";
            button.style.left = "20px";
            button.style.padding = "10px";
            button.style.fontSize = "16px";
            button.style.backgroundColor = "#28a745";
            button.style.color = "white";
            button.style.border = "none";
            button.style.borderRadius = "5px";
            button.style.cursor = "pointer";
            button.style.zIndex = "10000";
            button.onclick = async () => {
                const success = await renameAndDeleteFile(fileName);
                if (success) {
                    index++;
                    isProcessing = false;
                    downloadNext();
                }
            };
            document.body.appendChild(button);
        }

        function removeButton() {
            if (button) {
                document.body.removeChild(button);
                button = null;
            }
        }

        async function renameAndDeleteFile(fileName) {
            try {
                const [fileHandle] = await window.showOpenFilePicker({
                    types: [{ description: "PDF Files", accept: { "application/pdf": [".pdf"] } }]
                });

                if (!fileHandle) throw new Error("No file selected.");

                const file = await fileHandle.getFile();
                const blob = await file.arrayBuffer();

                const newHandle = await window.showSaveFilePicker({
                    suggestedName: fileName,
                    types: [{ description: "PDF Files", accept: { "application/pdf": [".pdf"] } }]
                });

                const writable = await newHandle.createWritable();
                await writable.write(blob);
                await writable.close();

                if (directoryHandle) {
                    try {
                        await directoryHandle.removeEntry(fileHandle.name);
                    } catch (e) {}
                }
                return true;
            } catch (error) {
                return false;
            }
        }

        await requestDownloadFolder();
        downloadNext();
    })();
    </script>
    <div style='position:fixed; bottom:5px; left:20px; font-size:10px; opacity:0.6; z-index:9999;'>
        Discover more at <a href='https://www.dickyibrohim.com' target='_blank' style='color:#00d084;'>www.dickyibrohim.com</a>
    </div>
    <?php
}
