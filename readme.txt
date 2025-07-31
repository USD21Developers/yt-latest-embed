=== Latest YouTube Video Embedder (RSS Version) ===
Contributors: merlynx
Plugin Name: Latest YouTube Video Embedder
Tags: youtube, youtube embed, latest video, playlist, rss, shortcode
Requires at least: 4.5
Tested up to: 6.8
Stable tag: 1.2
License: GPLv3 or later

Embed the latest YouTube video from a channel or playlist using a WordPress shortcode — no API key required.

== Description ==
**Latest YouTube Video Embedder (RSS Version)** allows you to embed the latest video from a YouTube **channel** or **playlist** using public RSS feeds — no YouTube API key required.  
Configure your default channel ID and/or playlist ID via Dashboard > Settings > Latest YouTube Video.  
Use the shortcode `[latest_youtube_video]` anywhere in posts, pages, or widgets to dynamically show the most recent upload.

You can also override the defaults inline via shortcode attributes. See "Usage" and "Shortcode" examples below.

== Installation ==
1. Download the plugin ZIP file from [GitHub Releases](https://github.com/your-repo-link).
2. In your WordPress admin panel, navigate to `Plugins` → `Add New`.
3. Click `Upload Plugin` and select the downloaded ZIP file.
4. Activate the plugin after installation.

== Usage ==
1. Go to **Settings** → **Latest YouTube Video** in your WordPress dashboard.
2. Enter your default **channel ID** and/or **playlist ID**.
3. Use the shortcode `[latest_youtube_video]` to embed the most recent video from the default channel.
4. Use shortcode attributes to pull from a playlist or override the ID.

== Shortcode ==
Basic shortcode:
[latest_youtube_video]

Optional variations:
[latest_youtube_video source="channel"]
[latest_youtube_video source="playlist"]
[latest_youtube_video channel="UCabc123XYZ"]
[latest_youtube_video playlist="PLxyz456ABC"]

== Parameters ==
- `source`: Either `channel` or `playlist`. Uses the corresponding default from settings if provided.
- `channel`: Overrides the default channel ID inline.
- `playlist`: Overrides the default playlist ID inline.

== FAQ ==

= Do I need a YouTube API key? =
**No.** This plugin uses YouTube’s public RSS feeds to retrieve video data, so there is no need to create or manage API keys or billing quotas.

= How do I get a channel or playlist ID? =
- For **channel ID**: Go to the YouTube channel → right-click → "View page source" → search for `"channel_id"` or use the full channel URL (`youtube.com/channel/UCxxxx`).
- For **playlist ID**: Copy the part of the playlist URL after `?list=`. Example: `https://www.youtube.com/playlist?list=PLxyz123` → Playlist ID is `PLxyz123`.

== Changelog ==
= 1.2 (07/31/2025) =
* Fully updated to use public RSS feeds (no API key required)
* Added support for playlist and channel mode
* Admin settings panel for default IDs
* Smart shortcode system with override flexibility

== License ==
GPLv3 or later

== Credits ==
Code collaboration with **ChatGPT (Echo)** from OpenAI.
