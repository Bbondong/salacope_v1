<?php
session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

// Vérifier l'authentification
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false, 
        'message' => 'Veuillez vous connecter pour publier un produit'
    ]);
    exit();
}

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false, 
        'message' => 'Méthode non autorisée'
    ]);
    exit();
}

// Récupérer et nettoyer les données
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

$seller_id = $_SESSION['user_id'];
$seller_type = $_SESSION['user_type']; // 'client' ou 'admin'
$title = sanitize($_POST['product_name'] ?? '');
$category_id = intval($_POST['category_id'] ?? 0);
$price = floatval($_POST['price'] ?? 0);
$description = sanitize($_POST['description'] ?? '');
$stock_quantity = intval($_POST['stock_quantity'] ?? 1);
$product_condition = $_POST['product_condition'] ?? 'new';
$brand = sanitize($_POST['brand'] ?? '');
$model = sanitize($_POST['model'] ?? '');
$city = sanitize($_POST['city'] ?? '');
$address = sanitize($_POST['address'] ?? '');
$is_negotiable = isset($_POST['is_negotiable']) ? 1 : 0;
$delivery_available = isset($_POST['delivery_available']) ? 1 : 0;
$delivery_cost = floatval($_POST['delivery_cost'] ?? 0);
$warranty_months = intval($_POST['warranty_months'] ?? 0);

// Validation
$errors = [];

if (empty($title) || strlen($title) < 3) {
    $errors[] = 'Le nom du produit doit avoir au moins 3 caractères';
}

if ($category_id <= 0) {
    $errors[] = 'Veuillez sélectionner une catégorie valide';
}

if ($price <= 0) {
    $errors[] = 'Le prix doit être supérieur à 0';
}

if (empty($description) || strlen($description) < 10) {
    $errors[] = 'La description doit avoir au moins 10 caractères';
}

if ($stock_quantity < 1) {
    $errors[] = 'La quantité doit être au moins 1';
}

if (!in_array($product_condition, ['new', 'used', 'refurbished'])) {
    $errors[] = 'État du produit invalide';
}

if (empty($city)) {
    $errors[] = 'La ville est requise';
}

// Vérifier les images
$images = $_FILES['product_images'] ?? [];
if (empty($images['name'][0])) {
    $errors[] = 'Au moins une image est requise';
} else {
    $imageCount = count($images['name']);
    if ($imageCount > 3) {
        $errors[] = 'Maximum 3 images autorisées';
    }
    
    // Vérifier chaque image
    for ($i = 0; $i < $imageCount; $i++) {
        if ($images['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = 'Erreur lors du téléchargement d\'une image';
            break;
        }
        
        // Vérifier la taille (5MB)
        if ($images['size'][$i] > 5 * 1024 * 1024) {
            $errors[] = 'Une image dépasse 5MB';
            break;
        }
        
        // Vérifier le type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($images['tmp_name'][$i]);
        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = 'Format d\'image non supporté';
            break;
        }
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => implode(', ', $errors)
    ]);
    exit();
}

try {
    // Commencer la transaction
    $bd->beginTransaction();
    
    // Créer un slug unique
    $slug = createSlug($title) . '-' . uniqid();
    
    // Insérer le produit
    $stmt = $bd->prepare("
        INSERT INTO products (
            seller_id, seller_type, title, slug, description, price,
            category_id, stock_quantity, product_condition, brand, model,
            city, address, is_negotiable, delivery_available, delivery_cost,
            warranty_months, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'published', NOW())
    ");
    
    $stmt->execute([
        $seller_id, $seller_type, $title, $slug, $description, $price,
        $category_id, $stock_quantity, $product_condition, $brand, $model,
        $city, $address, $is_negotiable, $delivery_available, $delivery_cost,
        $warranty_months
    ]);
    
    $product_id = $bd->lastInsertId();
    
    // Traitement des images
    $uploadDir = '../../uploads/products/' . date('Y/m/');
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    for ($i = 0; $i < $imageCount; $i++) {
        $extension = pathinfo($images['name'][$i], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $title) . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($images['tmp_name'][$i], $filepath)) {
            $isPrimary = ($i === 0) ? 1 : 0;
            
            $imageStmt = $bd->prepare("
                INSERT INTO product_images (product_id, image_path, image_name, image_order, is_primary)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            // Stocker le chemin relatif
            $relativePath = 'uploads/products/' . date('Y/m/') . $filename;
            
            $imageStmt->execute([
                $product_id,
                $relativePath,
                $images['name'][$i],
                $i,
                $isPrimary
            ]);
        }
    }
    
    // Valider la transaction
    $bd->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Produit publié avec succès',
        'product_id' => $product_id,
        'redirect' => '../../product.php?id=' . $product_id
    ]);
    
} catch (PDOException $e) {
    // Annuler en cas d'erreur
    if ($bd->inTransaction()) {
        $bd->rollBack();
    }
    
    http_response_code(500);
    error_log('Erreur publication produit: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la publication du produit'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}

// Fonction pour créer un slug
function createSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    
    if (empty($text)) {
        return 'produit';
    }
    
    return $text;
}
?>