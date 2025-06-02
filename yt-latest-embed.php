<?php
/*
Plugin Name: YouTube Latest Video Embed
Description: Embeds the latest YouTube video from a specified channel using a shortcode.
Version: 1.1
Author: Jeremy Ciaramella
*/

// Register activation hook
register_activation_hook(__FILE__, 'yt_latest_embed_activate');

// Activation function
function yt_latest_embed_activate() {
    // Set default options on plugin activation
    add_option('yt_latest_embed_api_key', '');
    add_option('yt_latest_embed_channel_id', '');
    add_option('yt_latest_embed_height', '315');
    add_option('yt_latest_embed_width', '560');
}

// Register deactivation hook
register_deactivation_hook(__FILE__, 'yt_latest_embed_deactivate');

// Deactivation function (optional: clean up options)
function yt_latest_embed_deactivate() {
    delete_option('yt_latest_embed_api_key');
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
        <h1>YouTube Latest Video Embed Settings</h1>
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
    // Register settings
    register_setting('yt_latest_embed_options', 'yt_latest_embed_api_key');
    register_setting('yt_latest_embed_options', 'yt_latest_embed_channel_id');
    register_setting('yt_latest_embed_options', 'yt_latest_embed_height');
    register_setting('yt_latest_embed_options', 'yt_latest_embed_width');

    // Add sections and fields
    add_settings_section(
        'yt_latest_embed_main_section',  // Section ID
        'Settings',                      // Section title
        'yt_latest_embed_section_info',  // Callback for section description
        'yt_latest_embed_settings'       // Page to add section to
    );

    add_settings_field(
        'yt_latest_embed_api_key',      // Field ID
        'API Key',                      // Field label
        'yt_latest_embed_api_key_input', // Callback for field input
        'yt_latest_embed_settings',     // Page to add field to
        'yt_latest_embed_main_section'  // Section to add field to
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
    echo '<p>Enter your YouTube Data API key, channel ID, and preferred dimensions for embedding the latest video.</p>';
}

// Input field callbacks
function yt_latest_embed_api_key_input() {
    $api_key = get_option('yt_latest_embed_api_key');
    echo '<input type="text" name="yt_latest_embed_api_key" value="' . esc_attr($api_key) . '" class="regular-text">';
}

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

// Shortcode function
function yt_latest_embed_shortcode($atts) {
    // Extract shortcode attributes
    $atts = shortcode_atts(array(
        'api' => get_option('yt_latest_embed_api_key'),
        'channel' => get_option('yt_latest_embed_channel_id'),
        'height' => get_option('yt_latest_embed_height'),
        'width' => get_option('yt_latest_embed_width')
    ), $atts);

    // Retrieve shortcode attributes
    $api_key = $atts['api'];
    $channel_id = $atts['channel'];
    $height = $atts['height'];
    $width = $atts['width'];

    // Ensure required parameters are provided
    if (empty($api_key) || empty($channel_id)) {
        return 'Error: API key and channel ID are required.';
    }

    // Construct YouTube API URL
    $api_url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=' . $channel_id . '&order=date&maxResults=1&key=' . $api_key;

    // Make API request
    $response = wp_remote_get($api_url);

    // Check for API request errors
    if (is_wp_error($response)) {
        return 'Error fetching video data.';
    }

    // Parse JSON response
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body);

    // Check if video data exists
    if (isset($data->items[0]->id->videoId)) {
        $video_id = $data->items[0]->id->videoId;
        $video_url = 'https://www.youtube.com/embed/' . $video_id;

        // Output embedded video
        return '<iframe width="' . $width . '" height="' . $height . '" src="' . $video_url . '" frameborder="0" allowfullscreen></iframe>';
    } else {
        return 'No videos found.';
    }
}

// Register shortcode
add_shortcode('ytlatestembed', 'yt_latest_embed_shortcode');