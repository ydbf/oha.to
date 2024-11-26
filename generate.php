<?php
// Set the URL of the API endpoint
$url = 'https://www.oha.to/channels';

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Set to true to return the result as a string
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects if any

// Execute cURL request
$response = curl_exec($ch);

// Check for errors
if(curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
    exit;
}

// Close cURL session
curl_close($ch);

// Decode the JSON response into a PHP array
$data = json_decode($response, true);

// Check if the JSON was decoded successfully
if (!$data) {
    echo "Failed to decode JSON.";
    exit;
}

// Create the M3U8 file content
$m3u8Content = "#EXTM3U\n";

// Loop through the channels in the JSON response and generate M3U8 entries
foreach ($data as $channel) {
    // Extract necessary fields
    $channelName = htmlspecialchars($channel['name']);  // Channel name
    $channelId = $channel['id'];  // Channel ID
    $channelCountry = htmlspecialchars($channel['country']);  // Channel country (used for category)

    // Construct the stream URL using the provided pattern
    $channelUrl = "https://www.oha.to/play/{$channelId}/index.m3u8";
    
    // Add the channel to the M3U8 content using the new format
    $m3u8Content .= "#EXTINF:-1 group-title=\"{$channelCountry}\", {$channelName}\n";
    
    // Add EXTVLCOPT tags for each channel
    $m3u8Content .= "#EXTVLCOPT:http-referrer=https://www.oha.to/\n";
    $m3u8Content .= "#EXTVLCOPT:http-user-agent=Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.0 Mobile/15E148 Safari/604.1\n";
    
    // Add the stream URL
    $m3u8Content .= "$channelUrl\n";
}

// Define the path for the M3U8 file
$m3u8FilePath = 'playlist.m3u8';

// Write the M3U8 content to the file
file_put_contents($m3u8FilePath, $m3u8Content);

echo "M3U8 file has been generated successfully: $m3u8FilePath";
?>
