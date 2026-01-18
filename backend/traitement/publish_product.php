<?php
session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
$seller_id = $_SESSION['user_id'];
$seller_type = $_SESSION['user_type']; // 'client' ou 'admin'
$title = trim($_POST['product_name'] ?? '');
$category_id = intval($_POST['category_id'] ?? 0);
$price = floatval($_POST['price'] ?? 0);
$description = trim($_POST['description'] ?? '');
$stock_quantity = intval($_POST['stock_quantity'] ?? 1);
$product_condition = $_POST['product_condition'] ?? 'new';
$brand = trim($_POST['brand'] ?? '');
$model = trim($_POST['model'] ?? '');
$city = trim($_POST['city'] ?? '');
$address = trim($_POST['address'] ?? '');
$is_negotiable = isset($_POST['is_negotiable']) ? 1 : 0;
$delivery_available = isset($_POST['delivery_available']) ? 1 : 0;
$delivery_cost = floatval($_POST['delivery_cost'] ?? 0);
$warranty_months = intval($_POST['warranty_months'] ?? 0);

// Log des données reçues (pour débogage)
error_log("Publication produit - Données reçues:");
error_log("seller_id: $seller_id, seller_type: $seller_type");
error_log("title: $title, category_id: $category_id, price: $price");
error_log("city: $city, condition: $product_condition");

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
    
    // Vérifier la table products
    error_log("Insertion dans la table products...");
    
    // Insérer le produit - VERSION SIMPLIFIÉE POUR DÉBOGAGE
    $sql = "
        INSERT INTO products (
            seller_id, seller_type, title, slug, description, price,
            category_id, stock_quantity, product_condition, brand, model,
            city, address, is_negotiable, delivery_available, delivery_cost,
            warranty_months, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'published', NOW())
    ";
    
    error_log("SQL: $sql");
    
    $stmt = $bd->prepare($sql);
    
    // Log des paramètres
    $params = [
        $seller_id, $seller_type, $title, $slug, $description, $price,
        $category_id, $stock_quantity, $product_condition, $brand, $model,
        $city, $address, $is_negotiable, $delivery_available, $delivery_cost,
        $warranty_months
    ];
    
    error_log("Params: " . print_r($params, true));
    
    $result = $stmt->execute($params);
    
    if (!$result) {
        throw new Exception("Échec de l'exécution de la requête INSERT");
    }
    
    $product_id = $bd->lastInsertId();
    error_log("Produit inséré avec ID: $product_id");
    
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
            
            error_log("Image $i insérée: $relativePath");
        } else {
            throw new Exception("Échec du téléchargement de l'image $i");
        }
    }
    
    // Valider la transaction
    $bd->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Produit publié avec succès',
        'product_id' => $product_id,
        'redirect' => '/products.php?id=' . $product_id
    ]);
    
} catch (PDOException $e) {
    // Annuler en cas d'erreur
    if ($bd->inTransaction()) {
        $bd->rollBack();
    }
    
    error_log('ERREUR PDO publication produit: ' . $e->getMessage());
    error_log('Code erreur: ' . $e->getCode());
    error_log('Fichier: ' . $e->getFile());
    error_log('Ligne: ' . $e->getLine());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur base de données: ' . $e->getMessage(),
        'error_code' => $e->getCode()
    ]);
} catch (Exception $e) {
    error_log('ERREUR GENERALE publication produit: ' . $e->getMessage());
    
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