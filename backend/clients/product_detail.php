<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once '../config.php';

/* ========= MÉTHODE ========= */
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  http_response_code(405);
  echo json_encode(['error' => 'Méthode non autorisée']);
  exit;
}

/* ========= VALIDATION ID ========= */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
  'options' => ['min_range' => 1]
]);

if (!$id) {
  http_response_code(400);
  echo json_encode(['error' => 'ID invalide']);
  exit;
}

/* ========= REQUÊTE ========= */
$sql = "
SELECT
  p.product_id,
  p.seller_id,
  p.title,
  p.description,
  p.price,
  p.stock_quantity,
  p.product_condition,
  p.brand,
  p.delivery_available,
  p.delivery_cost,
  COALESCE(pi.image_path, 'assets/no-image.png') AS image_path
FROM products p
LEFT JOIN product_images pi
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

/* ========= NORMALISATION ========= */
$product['product_id'] = (int) $product['product_id'];
$product['seller_id'] = (int) $product['seller_id'];
$product['price'] = (float) $product['price'];
$product['stock_quantity'] = (int) $product['stock_quantity'];
$product['delivery_available'] = (int) $product['delivery_available'];
$product['delivery_cost'] = (float) $product['delivery_cost'];

/* ========= URL IMAGE ABSOLUE ========= */
$baseUrl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
         . '://' . $_SERVER['HTTP_HOST'];

$product['image_path'] = $baseUrl . '/' . ltrim($product['image_path'], '/');

/* ========= SORTIE ========= */
echo json_encode($product, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
