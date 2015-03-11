<?php
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'c' => 1,
    'b' => [1, 3],
]);
