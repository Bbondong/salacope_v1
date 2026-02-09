<?php
header('Content-Type: application/json');
require_once '../config.php';

$seller_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$seller_type = isset($_GET['type']) ? $_GET['type'] : 'client';

if (!$seller_id || !in_array($seller_type, ['admin', 'client'])) {
    echo json_encode(['success' => false, 'message' => 'Paramètres invalides']);
    exit;
}

try {
    $seller_info = null;
    
    if ($seller_type == 'admin') {
        // Chercher dans admin
        $sql = "SELECT 
                    admin_id as user_id,
                    admin_name as username,
                    'admin' as user_type,
                    admin_role as role,
                    Num as tel
                FROM admin 
                WHERE admin_id = ?";
        
        $stmt = $bd->prepare($sql);
        $stmt->execute([$seller_id]);
        $seller_info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($seller_info) {
            $seller_info['is_online'] = true; // Les admins sont souvent considérés comme en ligne
        }
    } 
    else {
        // Chercher dans client
        $sql = "SELECT 
                    id_client as user_id,
                    CONCAT(nom, ' ', post_nom, ' ', prenom) as username,
                    'client' as user_type,
                    type_client as role,
                    tel,
                    CASE 
                        WHEN type_client = 'vendeur' THEN 'Vendeur'
                        ELSE 'Client'
                    END as user_role_display
                FROM client 
                WHERE id_client = ?";
        
        $stmt = $bd->prepare($sql);
        $stmt->execute([$seller_id]);
        $seller_info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($seller_info) {
            // Simuler le statut en ligne (à adapter avec votre système)
            $seller_info['is_online'] = rand(0, 1) == 1; // Exemple aléatoire
        }
    }
    
    if ($seller_info) {
        // Ajouter une image par défaut
        $seller_info['profile_picture'] = 'images/default-avatar.png';
        
        echo json_encode([
            'success' => true,
            ...$seller_info
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Vendeur non trouvé']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()]);
}
?>