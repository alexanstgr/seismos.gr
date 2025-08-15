<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");

$url = 'https://www.seismos.gr/seismoi-lista';

$context = stream_context_create([
    'http' => ['header' => "User-Agent: Mozilla/5.0\r\n"]
]);

$html = file_get_contents($url, false, $context);

if ($html === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to retrieve data from the URL']);
    exit;
}

$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
libxml_clear_errors();

$xpath = new DOMXPath($dom);
$items = $xpath->query('//div[contains(@class,"list-group")]/a[contains(@class,"list-group-item")]');

$quakes = [];

foreach ($items as $i => $a) {
   
    $magnitude = '';
    $magLevel = '';
    $magNode = $xpath->query('.//span[contains(@class, "pull-right") and contains(@class, "mag")]', $a)->item(0);
    
    if ($magNode) {
        $magnitude = trim($magNode->textContent);
        $classAttr = $magNode->getAttribute('class');  
        $classes = explode(' ', $classAttr);
        foreach ($classes as $cls) {
            if (strpos($cls, 'mag') === 0) {
                $magLevel = $cls;
                break;
            }
        }
    }

  
    $titleNode = $xpath->query('.//h4', $a)->item(0);
    $title = $titleNode ? trim($titleNode->textContent) : '';

    $metaDate = '';
    $metaTime = '';
    $metaTitle = '';
    if (preg_match('/^(\d{2}\/\d{2}\/\d{4}) (\d{2}:\d{2}:\d{2}) - (.+)$/u', $title, $matches)) {
        $metaDate = $matches[1];
        $metaTime = $matches[2];
        $metaTitle = $matches[3];
    } else {
        $metaTitle = $title; 
    }

    $timeAgoNode = $xpath->query('.//small', $a)->item(0);
    $timeAgo = $timeAgoNode ? trim($timeAgoNode->textContent) : '';

    $href = $a->getAttribute('href');
    $detailUrl = 'https://seismos.gr' . $href;


    $quakes[] = [
        'id' => $i + 1,
        'magnitude' => $magnitude,
        'magLevel' => $magLevel,
        'meta' => [
            'title' => $metaTitle,
            'date' => $metaDate,
            'time' => $metaTime
        ],
        'timeAgo' => $timeAgo,
        'detailUrl' => $detailUrl
    ];
}

echo json_encode($quakes, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
