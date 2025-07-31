<?php
/*
Plugin Name: Latest YouTube Video Embedder (RSS Version)
Description: Displays the latest YouTube video from a channel or playlist without needing an API key.
Version: 1.2
Author: Jeremy + Echo
*/

add_shortcode('latest_youtube_video', 'lyv_display_latest_video');

function lyv_display_latest_video($atts) {
    $options = get_option('lyv_plugin_settings');

    $atts = shortcode_atts([
        'source'   => '',
        'channel'  => '',
        'playlist' => '',
    ], $atts);

    $video_id = '';
    $source = $atts['source'];
    $channel_id = trim($atts['channel']);
    $playlist_id = trim($atts['playlist']);

    // Determine actual source and ID
    if (!empty($channel_id)) {
        $source = 'channel';
    } elseif (!empty($playlist_id)) {
        $source = 'playlist';
    } elseif (empty($source)) {
        $source = 'channel'; // fallback default
    }

    if ($source === 'channel' && empty($channel_id)) {
        $channel_id = $options['channel_id'] ?? '';
    } elseif ($source === 'playlist' && empty($playlist_id)) {
        $playlist_id = $options['playlist_id'] ?? '';
    }

    // Construct feed URL
    if ($source === 'channel' && !empty($channel_id)) {
        $rss_url = "https://www.youtube.com/feeds/videos.xml?channel_id={$channel_id}";
    } elseif ($source === 'playlist' && !empty($playlist_id)) {
        $rss_url = "https://www.youtube.com/feeds/videos.xml?playlist_id={$playlist_id}";
    } else {
        return 'No valid channel or playlist ID provided.';
    }

    // Cache with transient
    $transient_key = 'lyv_rss_' . md5($rss_url);
    $video_id = get_transient($transient_key);

    if (!$video_id) {
        $rss = @simplexml_load_file($rss_url);
        if (!$rss || empty($rss->entry[0]->id)) {
            return 'Could not retrieve video.';
        }

        $yt_id_string = (string)$rss->entry[0]->id;
        if (preg_match('#video:(.*)$#', $yt_id_string, $matches)) {
            $video_id = $matches[1];
            set_transient($transient_key, $video_id, HOUR_IN_SECONDS);
        } else {
            return 'No video found in feed.';
        }
    }

    return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . esc_attr($video_id) . '" frameborder="0" allowfullscreen></iframe>';
}

add_action('admin_menu', 'lyv_admin_menu');
function lyv_admin_menu() {
    add_options_page('Latest YouTube Video Settings', 'Latest YouTube Video', 'manage_options', 'lyv-settings', 'lyv_settings_page');
}

function lyv_settings_page() {
    ?>
    <div class="wrap">
        <h1>Latest YouTube Video Settings (RSS Version)</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('lyv_plugin_settings_group');
            do_settings_sections('lyv-settings');
            submit_button();
            ?>
        </form>
        <p><strong>Shortcode Examples:</strong></p>
        <code>[latest_youtube_video]</code><br>
        <code>[latest_youtube_video source="channel"]</code><br>
        <code>[latest_youtube_video source="playlist"]</code><br>
        <code>[latest_youtube_video channel="UCabc123"]</code><br>
        <code>[latest_youtube_video playlist="PLxyz456"]</code><br>
    </div>
    <?php
}

add_action('admin_init', 'lyv_admin_init');
function lyv_admin_init() {
    register_setting('lyv_plugin_settings_group', 'lyv_plugin_settings');

    add_settings_section('lyv_main_section', 'Default YouTube IDs (for fallback)', null, 'lyv-settings');

    add_settings_field('channel_id', 'Default Channel ID', 'lyv_channel_id_callback', 'lyv-settings', 'lyv_main_section');
    add_settings_field('playlist_id', 'Default Playlist ID', 'lyv_playlist_id_callback', 'lyv-settings', 'lyv_main_section');
}

function lyv_channel_id_callback() {
    $options = get_option('lyv_plugin_settings');
    echo '<input type="text" name="lyv_plugin_settings[channel_id]" value="' . esc_attr($options['channel_id'] ?? '') . '" size="50">';
}

function lyv_playlist_id_callback() {
    $options = get_option('lyv_plugin_settings');
    echo '<input type="text" name="lyv_plugin_settings[playlist_id]" value="' . esc_attr($options['playlist_id'] ?? '') . '" size="50">';
}
