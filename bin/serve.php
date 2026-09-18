<?php

$docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/') ?: '/index';

$path = $docRoot . $uri;

if (is_file($path)) {
    return false;
}

if (is_file($path . '.html')) {
    header('Content-Type: text/html; charset=utf-8');
    readfile($path . '.html');
    return true;
}

if (is_dir($path) && is_file($path . '/index.html')) {
    header('Content-Type: text/html; charset=utf-8');
    readfile($path . '/index.html');
    return true;
}

http_response_code(404);
header('Content-Type: text/plain');
echo '404 Not Found';
return true;
