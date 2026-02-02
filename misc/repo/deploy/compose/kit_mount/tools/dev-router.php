<?php declare(strict_types=1);
if (($_SERVER['REQUEST_URI'] ?? '/') === '/health') {
    header('content-type:application/json');
    echo json_encode(['ok' => true]);
    exit;
}
http_response_code(501);
echo json_encode(['ok' => false]);
