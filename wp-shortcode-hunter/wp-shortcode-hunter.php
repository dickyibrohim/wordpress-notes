<?php
/**
 * Snippet Name: Ultimate Shortcode Hunter (Level 2)
 * Description: Find shortcode sources via Registry reflection OR Deep Physical File Scan. Includes "Nuclear" all-listing.
 * Version: 2.3.0
 * Author: Dicky Ibrohim
 * Author URI: https://www.dickyibrohim.com
 */

// We use 'shutdown' to ensure we capture shortcodes registered late in the load process.
add_action('shutdown', 'ibrohim_shortcode_hunter_execution');

/**
 * Main Execution Function
 */
function ibrohim_shortcode_hunter_execution() {
    // SECURITY: Only run if the user is an Admin AND the specific trigger is present.
    if (!current_user_can('manage_options') || !isset($_GET['find_shortcode'])) {
        return;
    }

    // POWER MOVE: Force disable caching for this specific request to ensure accurate results.
    if (function_exists('apache_setenv')) @apache_setenv('no-gzip', 1);
    @ini_set('zlib.output_compression', 0);
    @ini_set('implicit_flush', 1);
    if (!headers_sent()) {
        nocache_headers();
    }

    $target_tag = sanitize_text_field($_GET['find_shortcode']);
    global $shortcode_tags;

    // UI: High Z-Index Floating Panel (v2.0 Fixed Style)
    echo '<div id="ibrohim-shortcode-console" style="
        position: fixed; 
        top: 0; 
        left: 0; 
        width: 100%;
        height: 450px;
        z-index: 999999; 
        background: #1a1a1a; 
        border-bottom: 5px solid #00d084; 
        padding: 25px; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.8); 
        font-family: Consolas, Monaco, monospace; 
        color: #f0f0f1;
        overflow-y: scroll;
        box-sizing: border-box;
    ">';

    echo "<h2 style='margin: 0 0 10px 0; color: #00d084;'>🕵️ Shortcode Hunter: <span style='color:#fff'>[" . ($target_tag === 'all' ? 'NUCLEAR: ALL' : $target_tag) . "]</span></h2>";
    echo "<p style='margin: 0 0 20px 0; color: #aaa; font-size:13px;'>Context: " . (is_admin() ? 'Admin Dashboard' : 'Front-End') . " | Total Registered: " . count($shortcode_tags) . "</p>";

    if (empty($shortcode_tags)) {
        echo "<h3 style='color: #ff6b6b;'>⚠️ No shortcodes registered in this context.</h3>";
        ibrohim_shortcode_hunter_footer();
        echo "</div>";
        return;
    }

    $found_in_registry = false;

    // 1. Registry Lookup Mode (Handles 'all' or specific tag)
    if ($target_tag === 'all') {
        echo "<h3 style='color:#00d084'>☢️ NUCLEAR OPTION: All Registered Shortcodes</h3>";
        ksort($shortcode_tags);
    }

    foreach ($shortcode_tags as $tag => $callback) {
        // Filter if specific tag requested
        if ($target_tag !== 'all' && $tag !== $target_tag) {
            continue;
        }

        $found_in_registry = true;
        $info = ibrohim_reflect_shortcode($callback);

        echo "<div style='background: #262626; padding: 15px; margin-bottom: 10px; border-left: 4px solid #00d084;'>";
        echo "<strong style='font-size: 1.2em; color: #fff;'>[{$tag}]</strong><br>";
        echo "<div style='margin-top: 5px; color: #ccc;'>";
        echo "📂 File: <span style='color: #4db8ff; user-select: all;'>{$info['file']}</span><br>";
        echo "📍 Line: <strong style='color: #ffab40;'>{$info['line']}</strong><br>";
        echo "🔧 Type: <span style='color: #aaa;'>{$info['type']}</span>";
        echo "</div>";
        echo "</div>";
    }

    // 2. If Not Found in Registry, Fallback to Deep Physical File Scan
    if (!$found_in_registry && $target_tag !== 'all') {
        echo "<div style='padding: 20px; background: #3d1a1a; border: 1px solid #ff6b6b; margin-bottom:20px;'>";
        echo "<h3 style='margin:0; color: #ff6b6b;'>❌ Not Found in Global Registry</h3>";
        echo "<p>Starting Deep Physical File Scan... (Scanning /plugins/ and /themes/)</p>";
        echo "</div>";

        flush(); ob_flush();

        $dirs = [WP_CONTENT_DIR . '/plugins', WP_CONTENT_DIR . '/themes'];
        $match_count = 0;

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) continue;
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
            foreach ($iterator as $file) {
                if ($file->getExtension() !== 'php') continue;
                $handle = fopen($file->getRealPath(), "r");
                if ($handle) {
                    $line_num = 0;
                    while (($line = fgets($handle)) !== false) {
                        $line_num++;
                        if (strpos($line, $target_tag) !== false) {
                            $match_count++;
                            echo "<div style='border-left:3px solid #ff6b6b; padding:10px; background:#222; margin-bottom:5px;'>";
                            echo "📂 <strong>" . str_replace(WP_CONTENT_DIR, '...', $file->getRealPath()) . "</strong><br>";
                            echo "📍 Line $line_num: <code>" . htmlspecialchars(trim(substr($line, 0, 150))) . "</code>";
                            echo "</div>";
                            flush(); ob_flush();
                        }
                    }
                    fclose($handle);
                }
            }
        }

        if ($match_count === 0) {
            echo "<p style='color:#ff6b6b;'>No physical matches found for \"$target_tag\".</p>";
        } else {
            echo "<p style='color:#00d084;'>Scan complete. Found $match_count potential match(es).</p>";
        }
    }

    ibrohim_shortcode_hunter_footer();
    echo '</div>'; // End container
}

/**
 * Helper: Reflector to extract file path and line number (v2.0 Logic)
 */
function ibrohim_reflect_shortcode($callback) {
    try {
        $ref = null;
        $type = 'Unknown';

        if (is_string($callback) && strpos($callback, '::') !== false) {
            $callback = explode('::', $callback);
        }

        if (is_array($callback)) {
            $type = 'Class Method';
            $ref = new ReflectionMethod($callback[0], $callback[1]);
        } elseif (is_string($callback) && function_exists($callback)) {
            $type = 'Standard Function';
            $ref = new ReflectionFunction($callback);
        } elseif ($callback instanceof Closure) {
            $type = 'Closure / Anonymous';
            $ref = new ReflectionFunction($callback);
        } elseif (is_object($callback) && method_exists($callback, '__invoke')) {
            $type = 'Invokable Object';
            $ref = new ReflectionMethod($callback, '__invoke');
        }

        if ($ref) {
            return [
                'file' => $ref->getFileName(),
                'line' => $ref->getStartLine(),
                'type' => $type
            ];
        }
    } catch (Throwable $e) {
        return ['file' => 'Reflection Error: ' . $e->getMessage(), 'line' => '?', 'type' => 'Error'];
    }

    return ['file' => 'Native/Unknown', 'line' => '?', 'type' => 'Internal'];
}

/**
 * Footer Attribution
 */
function ibrohim_shortcode_hunter_footer() {
    echo "<div style='margin-top:20px; padding-top:15px; border-top:1px solid #333; font-size:11px; opacity:0.8; text-align:right;'>";
    echo "Crafted with precision by <a href='https://www.dickyibrohim.com' style='color:#00d084; text-decoration:none;' target='_blank'>Dicky Ibrohim</a>";
    echo "</div>";
}
