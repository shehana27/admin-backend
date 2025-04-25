<?php
// Set content type to JSON
header('Content-Type: application/json');

// Detect browser language
$browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? 
    strtok(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']), ',') : '';

// Allow language override via query parameter
$language = isset($_GET['language']) ? $_GET['language'] : $browser_language;

// Validate and set default language
switch (substr($language, 0, 2)) {
    case 'de':
        $language = 'de';
        break;
    case 'en':
    default:
        $language = 'en';
}

// Define available languages
$available_languages = [
    ['name' => 'English', 'token' => 'en'],
    ['name' => 'Arabic', 'token' => 'ar']
];

// Prepare response data
$response = [
    'current_language' => $language,
    'available_languages' => $available_languages,
    'language_switcher' => []
];

// Generate language switcher links
foreach ($available_languages as $lang) {
    if ($lang['token'] !== $language) {
        $response['language_switcher'][] = [
            'name' => $lang['name'],
            'url' => $_SERVER['PHP_SELF'] . '?language=' . $lang['token'],
            'token' => $lang['token']
        ];
    }
}

// Output JSON response
echo json_encode($response);