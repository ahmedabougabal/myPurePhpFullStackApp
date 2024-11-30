<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Kc\CourseCatalog\Database\DataLoader;

$loader = new DataLoader();
$loader->loadAll();