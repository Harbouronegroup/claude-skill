<?php
require __DIR__ . '/lib.php';
$p = hog_logo_path();
if (!$p) { http_response_code(404); exit; }
header('Content-Type: image/png');
header('Cache-Control: public, max-age=604800');
readfile($p);
