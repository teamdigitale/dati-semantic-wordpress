<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Schema Editor bootstrap
// This file will host functions and hooks to power the Schema Editor UI and APIs.

/**
 * Initialize Schema Editor related hooks.
 * Call this on plugins_loaded or admin_init when needed.
 */
function wp_schema_editor_init() {
    // Register direct endpoints for help content
    add_action('init', 'wp_schema_editor_add_help_endpoints');
}

// Optional: auto-run initializer on plugins_loaded to ensure availability
add_action('plugins_loaded', 'wp_schema_editor_init');

/**
 * Enqueue assets for Schema Editor when shortcode is present.
 */
function wp_schema_enqueue_schema_editor_assets() {
    // CSS
    wp_enqueue_style('wp-schema-editor', plugin_dir_url(dirname(__FILE__)) . 'assets/css/schema-editor.css', array(), '1.0');

    // JS bundle
    wp_enqueue_script('wp-schema-editor', plugin_dir_url(dirname(__FILE__)) . 'assets/js/schema-editor.js', array('jquery'), '1.0', true);
}

/**
 * Shortcode: [schema-editor url="..."]
 * Attributes:
 * - url: YAML URL to load (optional; defaults to bundled openapi.yaml like Swagger)
 * - viewer: true|false to toggle read-only viewer mode (default: true)
 * - height: container height (default: 600px)
 */
function render_schema_editor_shortcode($atts) {
    $plugin_url = plugin_dir_url(dirname(__FILE__));

    // Default YAML like Swagger
    $default_yaml_url = $plugin_url . 'assets/yaml/openapi.yaml';

    $atts = shortcode_atts(array(
        'url' => $default_yaml_url,
        'viewer' => 'true',
        'height' => '600px',
    ), $atts);

    // Enqueue required assets
    wp_schema_enqueue_schema_editor_assets();

    // Normalize viewer to boolean in JS
    $viewer_bool = strtolower($atts['viewer']) === 'true' ? 'true' : 'false';

    // Unique container id
    $container_id = 'schema-editor-' . uniqid();

    ob_start();
    ?>
    <?php
    $is_viewer = strtolower($atts['viewer']) === 'true';
    if ($is_viewer) {
        $editor_base = plugin_dir_url(dirname(__FILE__)) . 'assets/html/viewer.html';
        $query = array('url' => $atts['url']);
        $editor_src  = $editor_base . '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    } else {
        $editor_base = 'https://teamdigitale.github.io/dati-semantic-schema-editor/latest/';
        $query = array('url' => $atts['url']);
        $editor_src  = $editor_base . '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }
    ?>
    <div id="<?php echo $container_id; ?>" style="min-height: <?php echo esc_attr($atts['height']); ?>; height: auto; overflow: visible;">
        <iframe src="<?php echo esc_url($editor_src); ?>" style="width:100%; min-height: <?php echo esc_attr($atts['height']); ?>; border:0; display:block;" loading="lazy" referrerpolicy="no-referrer" allow="clipboard-write"></iframe>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('schema-editor', 'render_schema_editor_shortcode');

/**
 * Add help endpoints
 */
function wp_schema_editor_add_help_endpoints() {
    if (isset($_GET['schema_editor_help'])) {
        wp_schema_editor_serve_help_content();
    }
}

/**
 * Serve help content
 */
function wp_schema_editor_serve_help_content() {
    $help_type = sanitize_text_field($_GET['type'] ?? '');
    
    // Set proper headers for HTML response
    header('Content-Type: text/html; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Headers: Content-Type');
    
    $content = '';
    $file_path = '';
    
    switch ($help_type) {
        case 'help':
            $file_path = plugin_dir_path(__FILE__) . '../assets/help.md';
            break;
        case 'assistant':
            $file_path = plugin_dir_path(__FILE__) . '../assets/assistant.md';
            break;
        default:
            http_response_code(404);
            echo '<div class="error">Help type not found</div>';
            wp_die();
    }
    
    if (file_exists($file_path)) {
        $content = file_get_contents($file_path);
        // Convert markdown to HTML (basic conversion)
        $content = wp_schema_editor_convert_markdown($content);
        echo '<div class="help-content">' . $content . '</div>';
    } else {
        http_response_code(404);
        echo '<div class="error">Help file not found at: ' . esc_html($file_path) . '</div>';
        echo '<div class="debug">Plugin dir: ' . esc_html(plugin_dir_path(__FILE__)) . '</div>';
    }
    
    wp_die();
}


/**
 * Basic markdown to HTML converter
 */
function wp_schema_editor_convert_markdown($markdown) {
    // Convert headers
    $markdown = preg_replace('/^###### (.+)$/m', '<h6>$1</h6>', $markdown);
    $markdown = preg_replace('/^##### (.+)$/m', '<h5>$1</h5>', $markdown);
    $markdown = preg_replace('/^#### (.+)$/m', '<h4>$1</h4>', $markdown);
    $markdown = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $markdown);
    $markdown = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $markdown);
    $markdown = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $markdown);
    
    // Convert links
    $markdown = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $markdown);
    
    // Convert lists
    $markdown = preg_replace('/^- (.+)$/m', '<li>$1</li>', $markdown);
    $markdown = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $markdown);
    
    // Convert paragraphs
    $markdown = preg_replace('/^(?!<[h|u|l])(.+)$/m', '<p>$1</p>', $markdown);
    
    // Clean up empty paragraphs
    $markdown = preg_replace('/<p><\/p>/', '', $markdown);
    
    return $markdown;
}


