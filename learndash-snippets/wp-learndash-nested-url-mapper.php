<?php
/**
 * Snippet Name: LearnDash Nested URL Mapper
 * Description: Fixes 404 errors for nested LearnDash URLs and handles Polylang-aware routing and access validation.
 * Version: 1.0.0
 * Author: Dicky Ibrohim
 * Author URI: https://www.dickyibrohim.com
 */

if (!defined('ABSPATH')) exit;

/**
 * Customization:
 * This script is highly technical. It handles redirects and route mapping for LearnDash.
 * Ensure Polylang is configured correctly if you are using it.
 */

$GLOBALS['IBROHIM_LD_MAPPED'] = false;

/* --------------------------
 * Helpers (route, parse, ACL)
 * -------------------------*/

function ibrohim_ld_is_route_uri($uri = null) {
    if ($uri === null) $uri = $_SERVER['REQUEST_URI'] ?? '';
    if ($uri === '' || strpos($uri, '/wp-json') === 0) return false;
    $path = parse_url($uri, PHP_URL_PATH);
    if (!$path) return false;
    return (bool) preg_match('~^/(?:[a-z]{2}(?:-[a-z]{2})?/)?courses(?:/|$)~i', $path);
}

/** Parse /courses/... into pieces */
function ibrohim_ld_parse_parts() {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if (!$path) return null;
    $path = rtrim($path, '/');

    if (!preg_match('~^/(?:([a-z]{2}(?:-[a-z]{2})?)/)?courses(?:/|$)~i', $path, $m)) return null;
    $lang = isset($m[1]) ? strtolower($m[1]) : '';

    $parts = array_values(array_filter(explode('/', $path), 'strlen'));
    $i = array_search('courses', $parts, true);
    if ($i === false) return null;

    $out = [
        'lang'        => $lang,
        'course_slug' => '',
        'marker'      => '',
        'lesson_slug' => '',
        'final_slug'  => '',
    ];

    if (empty($parts[$i+1])) return null;
    $out['course_slug'] = $parts[$i+1];

    if (!isset($parts[$i+2])) {
        $out['final_slug'] = $out['course_slug']; // course page
        return $out;
    }

    $marker = $parts[$i+2];
    $out['marker'] = $marker;

    if ($marker === 'lessons') {
        $out['lesson_slug'] = $parts[$i+3] ?? '';
        if (!$out['lesson_slug']) return null;

        if (isset($parts[$i+4])) {
            $submarker = $parts[$i+4];
            $child     = $parts[$i+5] ?? '';
            if ($submarker === 'topics' && $child) {
                $out['marker']     = 'topics';
                $out['final_slug'] = $child; // topic
            } elseif ($submarker === 'quizzes' && $child) {
                $out['marker']     = 'quizzes';
                $out['final_slug'] = $child; // quiz under lesson
            } else {
                $out['final_slug'] = $out['lesson_slug']; // lesson
            }
        } else {
            $out['final_slug'] = $out['lesson_slug']; // lesson
        }
    } elseif ($marker === 'quizzes') {
        $quiz = $parts[$i+3] ?? '';
        if (!$quiz) return null;
        $out['final_slug'] = $quiz; // quiz directly under course
    } else {
        $out['final_slug'] = end($parts);
    }

    return $out;
}

/** LearnDash access checks */
function ibrohim_ld_user_has_access($post_id) {
    if (function_exists('sfwd_lms_has_access')) {
        return (bool) sfwd_lms_has_access($post_id, get_current_user_id());
    }
    return is_user_logged_in();
}

/** Relation helpers (prefer LD, fallback meta/parent) */
function ibrohim_ld_get_course_id($post_id) {
    if (function_exists('learndash_get_course_id')) {
        $cid = learndash_get_course_id($post_id);
        if ($cid) return (int) $cid;
    }
    foreach (['course_id','ld_course_id','sfwd-courses'] as $k) {
        $v = get_post_meta($post_id, $k, true);
        if ($v) return (int) $v;
    }
    $p = (int) get_post_field('post_parent', $post_id);
    if ($p) return ibrohim_ld_get_course_id($p);
    return 0;
}
function ibrohim_ld_get_lesson_id($post_id) {
    if (function_exists('learndash_get_lesson_id')) {
        $lid = learndash_get_lesson_id($post_id);
        if ($lid) return (int) $lid;
    }
    foreach (['lesson_id','ld_lesson_id','sfwd-lessons'] as $k) {
        $v = get_post_meta($post_id, $k, true);
        if ($v) return (int) $v;
    }
    return 0;
}

/** Strict single by slug/lang/CPT */
function ibrohim_ld_get_by_slug($slug, $cpt, $lang) {
    $args = [
        'post_type'      => $cpt,
        'name'           => $slug,
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'posts_per_page' => 1,
        'no_found_rows'  => true,
    ];
    if (function_exists('pll_the_languages')) {
        $args['lang'] = $lang ?: '';
    }
    $q = new WP_Query($args);
    $id = ($q->have_posts()) ? (int) $q->posts[0] : 0;
    wp_reset_postdata();
    return $id;
}

/** Build a stable "gate" URL for users without access */
function ibrohim_ld_gate_url($course_id, $lang) {
    if (!is_user_logged_in()) {
        $current = (is_ssl() ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '/');
        return wp_login_url($current);
    }
    $u = get_permalink($course_id);
    if ($u && !empty($lang) && function_exists('pll_get_post')) {
        $tr = pll_get_post($course_id, $lang);
        if ($tr) $u = get_permalink($tr);
    }
    return $u ?: home_url('/');
}

/* ------------------------------------------------
 * 1) Early mapping in `request` (strict + validated)
 * ------------------------------------------------*/

add_filter('request', 'ibrohim_ld_nested_url_request_handler', 0);

function ibrohim_ld_nested_url_request_handler($query_vars) {
    if (!ibrohim_ld_is_route_uri()) return $query_vars;

    $p = ibrohim_ld_parse_parts();
    if (!$p) return $query_vars;

    // Resolve course first (strict)
    $course_id = ibrohim_ld_get_by_slug($p['course_slug'], 'sfwd-courses', $p['lang']);
    if (!$course_id) return $query_vars;

    $target_id = 0;
    $target_pt = 'sfwd-courses';

    if ($p['marker'] === 'topics' && $p['lesson_slug'] && $p['final_slug']) {
        $lesson_id = ibrohim_ld_get_by_slug($p['lesson_slug'], 'sfwd-lessons', $p['lang']);
        if ($lesson_id && ibrohim_ld_get_course_id($lesson_id) === $course_id) {
            $topic_id = ibrohim_ld_get_by_slug($p['final_slug'], 'sfwd-topic', $p['lang']);
            if ($topic_id) {
                $topic_lesson_id = ibrohim_ld_get_lesson_id($topic_id);
                $topic_course_id = ibrohim_ld_get_course_id($topic_id);
                if ($topic_lesson_id === $lesson_id && $topic_course_id === $course_id) {
                    $target_id = $topic_id;
                    $target_pt = 'sfwd-topic';
                }
            }
        }
    } elseif ($p['marker'] === 'quizzes' && $p['final_slug']) {
        $quiz_id = ibrohim_ld_get_by_slug($p['final_slug'], 'sfwd-quiz', $p['lang']);
        if ($quiz_id && ibrohim_ld_get_course_id($quiz_id) === $course_id) {
            if ($p['lesson_slug']) {
                $lesson_id = ibrohim_ld_get_by_slug($p['lesson_slug'], 'sfwd-lessons', $p['lang']);
                if ($lesson_id) {
                    $quiz_lesson_id = ibrohim_ld_get_lesson_id($quiz_id);
                    if ($quiz_lesson_id === 0 || $quiz_lesson_id === $lesson_id) {
                        $target_id = $quiz_id;
                        $target_pt = 'sfwd-quiz';
                    }
                }
            } else {
                $target_id = $quiz_id;
                $target_pt = 'sfwd-quiz';
            }
        }
    } elseif ($p['marker'] === 'lessons' && $p['lesson_slug']) {
        $lesson_id = ibrohim_ld_get_by_slug($p['lesson_slug'], 'sfwd-lessons', $p['lang']);
        if ($lesson_id && ibrohim_ld_get_course_id($lesson_id) === $course_id) {
            $target_id = $lesson_id;
            $target_pt = 'sfwd-lessons';
        }
    } else {
        $target_id = $course_id;
        $target_pt = 'sfwd-courses';
    }

    if (!$target_id) return $query_vars;

    // If child step and user has no access → do NOT map; let LD redirect later
    $is_child = in_array($target_pt, ['sfwd-lessons','sfwd-topic','sfwd-quiz'], true);
    if ($is_child && !ibrohim_ld_user_has_access($target_id)) {
        return $query_vars;
    }

    // We WILL map (enrolled users / course page) → switch Polylang context and suppress canonicals
    if (!empty($p['lang']) && function_exists('pll_switch_language')) {
        pll_switch_language($p['lang']);
        add_filter('pll_redirect_canonical', '__return_false', 99);
    }

    $target = get_post($target_id);
    if (!$target) return $query_vars;

    $GLOBALS['IBROHIM_LD_MAPPED'] = true;

    return array_merge($query_vars, [
        'p'         => $target_id,
        'post_type' => $target_pt,
        'name'      => $target->post_name,
        'error'     => '',
    ]);
}

/* -----------------------------------------------------------------
 * 2) Redirect guard (break LD-route⇄LD-route ping-pong for no-access)
 * ----------------------------------------------------------------*/

add_action('init', 'ibrohim_ld_nested_url_redirect_guard');

function ibrohim_ld_nested_url_redirect_guard() {
    if (!ibrohim_ld_is_route_uri()) return;

    add_filter('wp_redirect', function ($location, $status) {
        if (!ibrohim_ld_is_route_uri($location)) return $location;

        $p = ibrohim_ld_parse_parts();
        if (!$p) return $location;

        $course_id = ibrohim_ld_get_by_slug($p['course_slug'], 'sfwd-courses', $p['lang']);
        if (!$course_id) return $location;

        if ($GLOBALS['IBROHIM_LD_MAPPED']) {
            $loc_path = parse_url($location, PHP_URL_PATH) ?: '';
            foreach (['login','enroll','my-account','checkout','cart'] as $k) {
                if (stripos($loc_path, $k) !== false) return $location;
            }
            return false;
        }

        $gate = ibrohim_ld_gate_url($course_id, $p['lang']);
        if (!$gate) return $location;

        $curr = (is_ssl() ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '/');
        if (trailingslashit($gate) === trailingslashit($curr)) {
            return false;
        }
        return $gate;
    }, 0, 2);
}

/* ------------------------------------------------------
 * 3) Suppress canonicals only when we actually mapped
 * -----------------------------------------------------*/

add_action('template_redirect', 'ibrohim_ld_nested_url_template_cleanup', 0);

function ibrohim_ld_nested_url_template_cleanup() {
    if (!$GLOBALS['IBROHIM_LD_MAPPED']) return;

    remove_action('template_redirect', 'redirect_canonical');
    add_filter('redirect_canonical', '__return_false', 99);
    if (function_exists('pll_the_languages')) {
        add_filter('pll_redirect_canonical', '__return_false', 99);
    }

    if (!headers_sent()) {
        header('X-Ibrohim-LD-Route: 1');
        header('X-Ibrohim-LD-Mapped: 1');
        if (is_singular() && ($obj = get_queried_object()) instanceof WP_Post) {
            header('X-Ibrohim-LD-Object: ' . $obj->post_type . '#' . $obj->ID);
        }
    }
}
