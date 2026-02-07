<?php
/**
 * Snippet Name: WP Bulk Invoice Downloader
 * Description: Refactored tool for bulk downloading and renaming admin invoices with professional English translations.
 * Author: Dicky Ibrohim
 */

/**
 * Step-by-Step Usage:
 * 1. Navigate to the page containing the invoice table.
 * 2. Select the download folder when prompted (required for automatic cleanup if supported).
 * 3. The script will automatically trigger the download for each row.
 * 4. Click the green button at the bottom left to confirm and rename each file.
 */

add_action('admin_footer', 'wp_bulk_invoice_downloader_script');

function wp_bulk_invoice_downloader_script() {
    ?>
    <script type="text/javascript">
    (async function() {
        // Disclaimer: Use this script at your own risk.
        // It is intended to simplify tasks, not replace official processes.
        
        const rows = document.querySelectorAll("table tbody tr");
        let index = 0;
        let button = null;
        let isProcessing = false;
        let directoryHandle = null; // Handle for download folder

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

            if (isProcessing) {
                console.log("⚠️ Waiting for the previous file to be processed...");
                return;
            }

            isProcessing = true;

            const row = rows[index];

            // Extract Company Name, Invoice Number, and Invoice Date from the table
            const companyName = row.children[3]?.innerText.trim()
                .replace(/[^a-zA-Z0-9\s]/g, "") // Remove special characters
                .replace(/\s+/g, "_"); // Replace spaces with underscores (_)
            
            const invoiceNumber = row.children[5]?.innerText.trim().replace(/\s+/g, "");
            const invoiceDate = row.children[6]?.innerText.trim().replace(/\s+/g, "");

            const fileName = `FP_${companyName}_${invoiceNumber}_${invoiceDate}.pdf`;

            const downloadButton = row.querySelector("#DownloadButton");

            if (downloadButton) {
                console.log(`⬇️ Downloading file: ${fileName}...`);
                downloadButton.click();
                showButton(fileName);
            } else {
                console.error(`❌ Failed to find download button for ${fileName}`);
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
                console.log("📂 Please select the recently downloaded PDF file...");

                const [fileHandle] = await window.showOpenFilePicker({
                    types: [{ description: "PDF Files", accept: { "application/pdf": [".pdf"] } }]
                });

                if (!fileHandle) {
                    throw new Error("No file selected.");
                }

                console.log("📂 File selected, reading content...");

                const file = await fileHandle.getFile();
                const blob = await file.arrayBuffer();

                console.log(`🔄 Saving file as: ${fileName}`);

                const newHandle = await window.showSaveFilePicker({
                    suggestedName: fileName,
                    types: [{ description: "PDF Files", accept: { "application/pdf": [".pdf"] } }]
                });

                const writable = await newHandle.createWritable();
                await writable.write(blob);
                await writable.close();

                console.log(`✅ Successfully saved: ${fileName}`);

                // 🔥 Attempt to delete original file if folder access was granted
                if (directoryHandle) {
                    try {
                        await directoryHandle.removeEntry(fileHandle.name);
                        console.log(`🗑️ Original file (${fileHandle.name}) deleted successfully.`);
                    } catch (deleteError) {
                        console.warn(`⚠️ Could not delete original file (${fileHandle.name}). Please delete manually.`);
                    }
                } else {
                    alert(`❌ Original file (${fileHandle.name}) could not be deleted automatically. Please delete manually.`);
                }

                return true;
            } catch (error) {
                console.error(`❌ Failed to rename file: ${fileName}`, error);
                alert("Failed to rename file. Make sure you selected the correct file.");
                return false;
            }
        }

        await requestDownloadFolder();
        downloadNext();
    })();
    </script>
    <?php
}
