<?php
header('Content-Type: application/json; charset=utf-8');

echo json_encode([
    'a' => 1,
    'b' => [1, 2],
    'c' => 3,
]);
