<?php 
require 'includes/functions.php';
header('Content-Type: application/json; charset=utf-8');
$file = 'data/sociograma.json';
$data = load_json($file);
echo json_encode([
'count' => count($data),
'items' => $data
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>