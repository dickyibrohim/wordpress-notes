<?php
/**
 * Snippet Name: Ultimate Shortcode Hunter (Level 2)
 * Description: Finds shortcodes via Registry OR Physical File Scan.
 * Author: Dicky Ibrohim
 * Version: 2.1 (Deep Scan Edition)
 */

add_action('shutdown', function () {
    if (!current_user_can('manage_options') || !isset($_GET['find_shortcode'])) {
        return;
    }

    // Disable output buffering & cache to handle heavy scanning
    if (function_exists('apache_setenv')) @apache_setenv('no-gzip', 1);
    @ini_set('zlib.output_compression', 0);
    @ini_set('implicit_flush', 1);
    if (!headers_sent()) nocache_headers();

    $target = sanitize_text_field($_GET['find_shortcode']);
    global $shortcode_tags;

    echo '<div style="background:#1a1a1a; color:#fff; padding:20px; position:relative; z-index:999999; font-family:monospace; border-bottom:5px solid #00d084;">';
    echo "<h2>🕵️ Hunter Target: [{$target}]</h2>";
    echo "<p>Context: " . (is_admin() ? 'Admin Dashboard' : 'Front-End') . "</p>";

    // 1. Cek Registry (Cara Standar)
    if (isset($shortcode_tags[$target])) {
        $cb = $shortcode_tags[$target];
        $info = dicky_reflect($cb);
        echo "<h3 style='color:#00d084'>✅ FOUND in Registry!</h3>";
        echo "File: <strong>{$info['file']}</strong><br>";
        echo "Line: <strong>{$info['line']}</strong><br>";
        echo "</div>";
        return; 
    }

    echo "<h3 style='color:#ff6b6b'>❌ Not in Registry. Starting Deep File Scan...</h3>";
    echo "<p>Scanning /wp-content/plugins/ and /themes/... This may take a moment.</p>";
    
    // 2. Deep File Scan (Cara \"Brute Force\")
    // Memaksa PHP untuk flush output agar terlihat progress-nya
    flush(); 
    ob_flush();

    $dirs = [WP_CONTENT_DIR . '/plugins', WP_CONTENT_DIR . '/themes'];
    $found_in_files = [];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) continue;
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            // Hanya scan file PHP
            if ($file->getExtension() !== 'php') continue;

            // Baca file baris per baris untuk hemat memori
            $handle = fopen($file->getRealPath(), "r");
            if ($handle) {
                $line_number = 0;
                while (($line = fgets($handle)) !== false) {
                    $line_number++;
                    if (strpos($line, $target) !== false) {
                        echo "<div style='margin-bottom:10px; border-left:3px solid #ffab40; padding-left:10px;'>";
                        echo "📂 <strong>" . str_replace(WP_CONTENT_DIR, '...', $file->getRealPath()) . "</strong><br>";
                        echo "📍 Line $line_number: <code>" . htmlspecialchars(trim(substr($line, 0, 150))) . "</code>";
                        echo "</div>";
                        flush(); ob_flush(); // Real-time output
                    }
                }
                fclose($handle);
            }
        }
    }

    echo "<p>--- Scan Complete ---</p>";
    echo "</div>";
});

function dicky_reflect($cb) {
    try {
        if (is_array($cb)) $r = new ReflectionMethod($cb[0], $cb[1]);
        elseif (is_string($cb) && function_exists($cb)) $r = new ReflectionFunction($cb);
        elseif ($cb instanceof Closure) $r = new ReflectionFunction($cb);
        elseif (is_object($cb)) $r = new ReflectionMethod($cb, '__invoke');
        else return ['file'=>'?', 'line'=>'?'];
        return ['file'=>$r->getFileName(), 'line'=>$r->getStartLine()];
    } catch (Throwable $e) { return ['file'=>'Err', 'line'=>'?']; }
}
