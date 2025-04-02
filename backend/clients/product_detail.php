<?php
header('Content-Type: application/json');
require_once '../config.php';

$id = $_GET['id'] ?? null;

if (!$id) {
  http_response_code(400);
  echo json_encode(['error' => 'ID manquant']);
  exit;
}

$sql = "
SELECT 
  p.product_id,
  p.title,
  p.price,
  p.description,
  pi.image_path
FROM products p
JOIN product_images pi 
  ON pi.product_id = p.product_id
 AND pi.is_primary = 1
WHERE p.product_id = ?
  AND p.status = 'published'
LIMIT 1
";

$stmt = $bd->prepare($sql);
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  http_response_code(404);
  echo json_encode(['error' => 'Produit introuvable']);
  exit;
}

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
         . '://' . $_SERVER['HTTP_HOST'];

$product['image_path'] = $baseUrl . '/' . ltrim($product['image_path'], '/');

echo json_encode($product);
