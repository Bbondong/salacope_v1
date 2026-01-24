<?php
header('Content-Type: application/json');
require_once '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
  echo json_encode(['success' => false, 'message' => 'Données invalides']);
  exit;
}

$product_id = $data['product_id'];
$quantity   = (int)$data['quantity'];

/* ========= RÉCUP PRODUIT ========= */
$stmt = $bd->prepare("
  SELECT price, stock_quantity
  FROM products
  WHERE product_id = ?
    AND status = 'published'
  LIMIT 1
");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  echo json_encode(['success' => false, 'message' => 'Produit introuvable']);
  exit;
}

if ($quantity > $product['stock_quantity']) {
  echo json_encode(['success' => false, 'message' => 'Stock insuffisant']);
  exit;
}

$total = $product['price'] * $quantity;

/* ========= INSERT COMMANDE ========= */
$stmt = $bd->prepare("
  INSERT INTO orders (
    product_id, quantity, total_price,
    customer_name, phone, address, city,
    delivery, payment_method, created_at
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
");

$stmt->execute([
  $product_id,
  $quantity,
  $total,
  $data['name'],
  $data['phone'],
  $data['address'],
  $data['city'],
  $data['delivery'],
  $data['payment']
]);

echo json_encode([
  'success' => true,
  'message' => 'Commande enregistrée'
]);
