<?php
/*
Plugin Name: Latest YouTube Video Embedder
Description: Embeds the latest YouTube video from a specified channel using a shortcode.
Version: 1.2
Author: Jeremy Ciaramella
License: GPLv3 or later
Short Description: Embed the latest YouTube video from a specified channel using a WordPress shortcode.
*/

// Register activation hook
register_activation_hook(__FILE__, 'yt_latest_embed_activate');

// Activation function
function yt_latest_embed_activate() {
    // Set default options on plugin activation
    add_option('yt_latest_embed_channel_id', '');
    add_option('yt_latest_embed_height', '315');
    add_option('yt_latest_embed_width', '560');
}

// Register deactivation hook
register_deactivation_hook(__FILE__, 'yt_latest_embed_deactivate');

// Deactivation function (optional: clean up options)
function yt_latest_embed_deactivate() {
    delete_option('yt_latest_embed_channel_id');
    delete_option('yt_latest_embed_height');
    delete_option('yt_latest_embed_width');
}

// Add admin menu item
add_action('admin_menu', 'yt_latest_embed_add_admin_menu');

function yt_latest_embed_add_admin_menu() {
    add_options_page(
        'YouTube Latest Video Settings', // Page title
        'YouTube Latest Video',         // Menu title
        'manage_options',               // Capability
        'yt_latest_embed_settings',     // Menu slug
        'yt_latest_embed_settings_page' // Callback function
    );
}

// Admin settings page content
function yt_latest_embed_settings_page() {
    ?>
    <div class="wrap">
        <h1>Latest YouTube Video Embedder</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('yt_latest_embed_options');
            do_settings_sections('yt_latest_embed_settings');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

// Register and initialize plugin settings
add_action('admin_init', 'yt_latest_embed_admin_init');

function yt_latest_embed_admin_init() {
    // Register settings with sanitization callbacks
    register_setting('yt_latest_embed_options', 'yt_latest_embed_channel_id', 'yt_latest_embed_sanitize_channel_id');
    register_setting('yt_latest_embed_options', 'yt_latest_embed_height', 'yt_latest_embed_sanitize_height');
    register_setting('yt_latest_embed_options', 'yt_latest_embed_width', 'yt_latest_embed_sanitize_width');

    // Add sections and fields
    add_settings_section(
        'yt_latest_embed_main_section',  // Section ID
        'Settings',                      // Section title
        'yt_latest_embed_section_info',  // Callback for section description
        'yt_latest_embed_settings'       // Page to add section to
    );

    add_settings_field(
        'yt_latest_embed_channel_id',
        'Channel ID',
        'yt_latest_embed_channel_id_input',
        'yt_latest_embed_settings',
        'yt_latest_embed_main_section'
    );

    add_settings_field(
        'yt_latest_embed_height',
        'Height',
        'yt_latest_embed_height_input',
        'yt_latest_embed_settings',
        'yt_latest_embed_main_section'
    );

    add_settings_field(
        'yt_latest_embed_width',
        'Width',
        'yt_latest_embed_width_input',
        'yt_latest_embed_settings',
        'yt_latest_embed_main_section'
    );
}

// Section callback (optional description)
function yt_latest_embed_section_info() {
    echo '<p>Enter your YouTube channel ID and preferred dimensions for embedding the latest video.</p>';
}

// Input field callbacks
function yt_latest_embed_channel_id_input() {
    $channel_id = get_option('yt_latest_embed_channel_id');
    echo '<input type="text" name="yt_latest_embed_channel_id" value="' . esc_attr($channel_id) . '" class="regular-text">';
}

function yt_latest_embed_height_input() {
    $height = get_option('yt_latest_embed_height');
    echo '<input type="number" name="yt_latest_embed_height" value="' . esc_attr($height) . '" class="small-text">';
}

function yt_latest_embed_width_input() {
    $width = get_option('yt_latest_embed_width');
    echo '<input type="number" name="yt_latest_embed_width" value="' . esc_attr($width) . '" class="small-text">';
}

// Sanitization callbacks
function yt_latest_embed_sanitize_channel_id($input) {
    return sanitize_text_field($input);
}

function yt_latest_embed_sanitize_height($input) {
    return absint($input);
}

function yt_latest_embed_sanitize_width($input) {
    return absint($input);
}

// Shortcode function
function yt_latest_embed_shortcode($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts(array(
        'channel' => get_option('yt_latest_embed_channel_id'),
        'height' => get_option('yt_latest_embed_height'),
        'width' => get_option('yt_latest_embed_width')
    ), $atts);

    // Retrieve shortcode attributes
    $channel_id = $atts['channel'];
    $height = $atts['height'];
    $width = $atts['width'];

    // Ensure required parameters are provided
    if (empty($channel_id)) {
        return 'Error: Channel ID is required.';
    }

    // Construct YouTube RSS feed URL
    $rss_url = "https://www.youtube.com/feeds/videos.xml?channel_id={$channel_id}";

    // Fetch RSS feed
    $response = wp_remote_get($rss_url);

    // Check for errors in fetching the feed
    if (is_wp_error($response)) {
        return 'Error fetching video data: ' . $response->get_error_message();
    }

    // Get the HTTP response code
    $response_code = wp_remote_retrieve_response_code($response);

    // Check if response code is not 200 OK
    if ($response_code !== 200) {
        return 'Error fetching video data: HTTP ' . $response_code;
    }

    // Parse XML response
    $body = wp_remote_retrieve_body($response);
    libxml_use_internal_errors(true); // Enable internal error handling
    $xml = simplexml_load_string($body);

    // Check if XML parsing was successful
    if ($xml === false) {
        $errors = libxml_get_errors(); // Get XML parsing errors
        $error_messages = array();
        foreach ($errors as $error) {
            $error_messages[] = $error->message;
        }
        return 'Error parsing video data: ' . implode(', ', $error_messages);
    }

    // Register YouTube namespace
    $xml->registerXPathNamespace('yt', 'http://www.youtube.com/xml/schemas/2015');

    // Find latest video entry
    $entries = $xml->entry;
    if (empty($entries)) {
        return 'No videos found.';
    }

    // Get the first entry (latest video)
    $latest_video = $entries[0];

    // Extract video details
    $video_title = htmlspecialchars($latest_video->title);
    $video_id = (string) $latest_video->children('yt', true)->videoId;

    // Generate embed code
    $embed_code = "<iframe width=\"{$width}\" height=\"{$height}\" src=\"https://www.youtube.com/embed/{$video_id}\" frameborder=\"0\" allowfullscreen></iframe>";

    // Output embedded video
    return $embed_code;
}

// Register shortcode
add_shortcode('ytlatestembed', 'yt_latest_embed_shortcode');
