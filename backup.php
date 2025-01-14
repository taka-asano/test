<?php
function fetchMicroCMSData($serviceDomain, $apiKey) {
    $url = $serviceDomain;
    $options = [
        'http' => [
            'header' => "X-API-KEY: $apiKey"
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        throw new Exception("Failed to fetch data from microCMS");
    }

    return json_decode($response, true);
}

function uploadToGoogleDrive($data, $accessToken) {
    $url = "https://www.googleapis.com/upload/drive/v3/files?uploadType=media";
    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $content = json_encode($data, JSON_PRETTY_PRINT);
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $content
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        throw new Exception("Failed to upload to Google Drive");
    }

    echo "Backup uploaded to Google Drive successfully.";
}

try {
    $serviceDomain = getenv('MICROCMS_SERVICE_DOMAIN');
    $apiKey = getenv('MICROCMS_API_KEY');
    $accessToken = getenv('GOOGLE_ACCESS_TOKEN');

    $data = fetchMicroCMSData($serviceDomain, $apiKey);
    uploadToGoogleDrive($data, $accessToken);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
