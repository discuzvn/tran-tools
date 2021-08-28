#!/usr/bin/php
<?php
$dir = './source_china';
$translationFiles = scandir($dir);

function str_ends_with($haystack, $needle): bool
{
    $length = strlen($needle);
    return !($length > 0) || substr($haystack, -$length) === $needle;
}

foreach ($translationFiles as $path) {
    if (!str_ends_with($path, '.json')) {
        continue;
    }
    $fullPath = $dir . '/' . $path;
    $plainText = file_get_contents($fullPath);
    $json = json_decode($plainText);
    print_r($json);
    break;
}