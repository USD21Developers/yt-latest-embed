# YouTube Latest Embed Plugin
Embed the latest YouTube video from a specified channel using a WordPress shortcode.

## Installation
1. Download the plugin ZIP file from [GitHub Releases](link-to-your-releases).
2. In your WordPress admin panel, navigate to `Plugins` -> `Add New`.
3. Click on `Upload Plugin` and select the downloaded ZIP file.
4. Activate the plugin once uploaded.

## Usage 
Fill out the settings with your defaults for Channel ID, API Key, and your preferred height and width values.  
Once you have done this, the [ytlatestembed] shortcode will embed the latest video from that channel.  

### Shortcode
You can customize any four of these settings as attributes in the shortcode.

[ytlatestembed api=YOUR_API_KEY channel=YOUR_CHANNEL_ID height=315 width=560]
   
Replace `YOUR_API_KEY` with your YouTube Data API key and `YOUR_CHANNEL_ID` with the ID of the YouTube channel.

#### Parameters

- `api`: Your YouTube Data API key.
- `channel`: YouTube channel ID.
- `height` (optional): Height of the embedded video player (default: 315).
- `width` (optional): Width of the embedded video player (default: 560).

## Frequently Asked Question(s)
How do I set up a YouTube API Key?

Setting up a YouTube API key on Google Developer Console is essential for accessing YouTube's API services. 

Here's a step-by-step guide to help you set it up:

1. **Navigate to Google Developer Console:**
   * Go to the [Google Developer Console](https://console.developers.google.com/).

2. **Create a New Project:**
   * If you haven't created a project yet, click on the dropdown menu at the top of the page next to "Google Cloud Platform" and select "New Project." Give your project a name and click "Create."

3. **Enable YouTube Data API:**
   * In the sidebar menu, click on "APIs & Services" and then "Library."
   * Search for "YouTube Data API" and click on it.
   * Click the "Enable" button to enable the API for your project.

4. **Create Credentials:**
   * After enabling the API, click on "Credentials" in the sidebar menu under "APIs & Services."
   * Click on "Create Credentials" and select "API key."

5. **Restrict Your API Key (Optional but Recommended):**
   * You can restrict your API key to prevent unauthorized use (recommended for security). Click on "Restrict Key" after creating it.
   * Under "Application restrictions," select "HTTP referrers (websites)" or "IP addresses," and add your website URL or IP address where your application will make requests from. You can also add restrictions based on APIs.
   * Click "Save" to apply the restrictions.

6. **Copy Your API Key:**
   * Once your API key is created, copy it. You'll use this API key to authenticate your requests to the YouTube Data API.

7. **Use Your API Key:**
   * Enter your API Key in Dashboard > Settings > YouTube Latest Video


8. **Manage Your API Key:**
   * You can manage your API keys (edit, delete, or regenerate) from the "Credentials" page in the Google Developer Console.

9. **Billing (if required):**
   * Depending on your usage, enabling APIs might require billing setup. Google offers free usage quotas for many APIs, but it's essential to check the [Google Cloud Pricing](https://cloud.google.com/pricing/) for details.

## Changelog
6/2/2025 - 1.1 released.

## License
GPLv3 or later