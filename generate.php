<?php
$ch = curl_init('https://www.oha.to/channels');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true
]);
$response = curl_exec($ch);
if(curl_errno($ch)) { echo 'cURL Error: ' . curl_error($ch); exit; }
curl_close($ch);

$data = json_decode($response, true);
if (!$data) { echo "Failed to decode JSON."; exit; }

$m3u8Content = "#EXTM3U\n";
foreach ($data as $channel) {
    $channelUrl = "https://www.oha.to/play/{$channel['id']}/index.m3u8";
    $m3u8Content .= "#EXTINF:-1 group-title=\"".htmlspecialchars($channel['country'])."\", ".htmlspecialchars($channel['name'])."\n";
    $m3u8Content .= "#EXTVLCOPT:http-referrer=https://www.oha.to/\n";
    $m3u8Content .= "#EXTVLCOPT:http-user-agent=Mozilla/5.0 (iPhone; CPU iPhone OS 17_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.0 Mobile/15E148 Safari/604.1\n";
    $m3u8Content .= "$channelUrl\n";
}

file_put_contents('playlist.m3u8', $m3u8Content);
echo "M3U8 file has been generated successfully: playlist.m3u8";
?>
