<?php

use Kc\CourseCatalog\Controllers\CategoryController;
use Kc\CourseCatalog\Controllers\CourseController;

require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$uriParts = explode('/', $uri);

try {
    $id = $uriParts[3] ?? null;
    $response = match ("$method $uri") {
        'GET /categories' => (new CategoryController())->index(),
        'GET /categories/{id}' => (new CategoryController())->show($id),
        'GET /courses' => (new CourseController())->index(),
        'GET /courses/{id}' => (new CourseController())->show($id),
        default => throw new \Exception('Not Found', 404)
    };

    echo json_encode($response);
} catch (\Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['error' => $e->getMessage()]);
}