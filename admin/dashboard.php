<?php
session_start();
require_once '../backend/config.php';

// Récupérer les statistiques réelles
try {
    // 1. Produits en ligne (depuis votre table products)
    $stmtProducts = $bd->prepare("SELECT COUNT(*) as total FROM products WHERE status = 'published'");
    $stmtProducts->execute();
    $productsCount = $stmtProducts->fetch()['total'];
    
    // 2. Clients actifs (depuis votre table client)
    $stmtClients = $bd->prepare("SELECT COUNT(*) as total FROM client");
    $stmtClients->execute();
    $clientsCount = $stmtClients->fetch()['total'];
    
    // 3. Derniers produits publiés (pour les activités)
    $stmtRecentProducts = $bd->prepare("
        SELECT p.title, p.price, p.created_at, p.city, 
               c.name as category_name,
               CASE 
                   WHEN p.seller_type = 'client' THEN cl.nom
                   WHEN p.seller_type = 'admin' THEN a.admin_name
                   ELSE 'Anonyme'
               END as seller_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        LEFT JOIN client cl ON p.seller_id = cl.id_client AND p.seller_type = 'client'
        LEFT JOIN admin a ON p.seller_id = a.admin_id AND p.seller_type = 'admin'
        WHERE p.status = 'published'
        ORDER BY p.created_at DESC
        LIMIT 3
    ");
    $stmtRecentProducts->execute();
    $recentProducts = $stmtRecentProducts->fetchAll(PDO::FETCH_ASSOC);
    
    // 4. Derniers clients inscrits
    $stmtRecentClients = $bd->prepare("
        SELECT nom, prenom, created_at 
        FROM client 
        ORDER BY created_at DESC 
        LIMIT 3
    ");
    $stmtRecentClients->execute();
    $recentClients = $stmtRecentClients->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // En cas d'erreur
    error_log("Erreur dashboard stats: " . $e->getMessage());
    $productsCount = 0;
    $clientsCount = 0;
    $recentProducts = [];
    $recentClients = [];
}
?>

<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon icon-sales">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-info">
            <h3>0</h3>
            <p>Ventes totales</p>
            <span class="stat-subtitle">À venir</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon icon-users">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $clientsCount; ?></h3>
            <p>Utilisateurs actifs</p>
            <span class="stat-subtitle"><?php echo $clientsCount; ?> client(s)</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon icon-products">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $productsCount; ?></h3>
            <p>Produits en ligne</p>
            <span class="stat-subtitle"><?php echo $productsCount; ?> produit(s)</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon icon-subscriptions">
            <i class="fas fa-id-card"></i>
        </div>
        <div class="stat-info">
            <h3>0</h3>
            <p>Abonnements actifs</p>
            <span class="stat-subtitle">À venir</span>
        </div>
    </div>
</div>

<div class="dashboard-cards">
    <div class="card">
        <div class="card-header">
            <h3>Produits récemment publiés</h3>
            <a href="?page=products">Voir tout</a>
        </div>
        <div class="card-body">
            <?php if (empty($recentProducts)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <p>Aucun produit publié</p>
                </div>
            <?php else: ?>
                <ul class="sales-list">
                    <?php foreach ($recentProducts as $product): ?>
                        <li class="sale-item">
                            <div class="sale-info">
                                <h4><?php echo htmlspecialchars(substr($product['title'], 0, 25)); ?><?php echo strlen($product['title']) > 25 ? '...' : ''; ?></h4>
                                <p>
                                    <span class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'Non catégorisé'); ?></span>
                                    <span class="product-seller">Par <?php echo htmlspecialchars($product['seller_name']); ?></span>
                                </p>
                            </div>
                            <div class="sale-amount">
                                <?php echo number_format($product['price'], 2, ',', ' '); ?> €
                                <div class="sale-time">
                                    <?php 
                                        $time = strtotime($product['created_at']);
                                        $now = time();
                                        $diff = $now - $time;
                                        
                                        if ($diff < 60) {
                                            echo 'À l\'instant';
                                        } elseif ($diff < 3600) {
                                            echo 'Il y a ' . floor($diff/60) . ' min';
                                        } elseif ($diff < 86400) {
                                            echo 'Il y a ' . floor($diff/3600) . ' h';
                                        } else {
                                            echo date('d/m/Y H:i', $time);
                                        }
                                    ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3>Activités récentes</h3>
            <a href="?page=activity">Voir tout</a>
        </div>
        <div class="card-body">
            <?php if (empty($recentClients) && empty($recentProducts)): ?>
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <p>Aucune activité récente</p>
                </div>
            <?php else: ?>
                <ul class="activity-list">
                    <?php 
                    // Compter le nombre d'activités
                    $totalActivities = count($recentClients) + count($recentProducts);
                    $displayed = 0;
                    
                    // Afficher d'abord les nouveaux produits
                    foreach ($recentProducts as $product):
                        if ($displayed >= 4) break;
                        $time = strtotime($product['created_at']);
                        $now = time();
                        $diff = $now - $time;
                        
                        if ($diff < 60) {
                            $timeText = 'À l\'instant';
                        } elseif ($diff < 3600) {
                            $timeText = 'Il y a ' . floor($diff/60) . ' min';
                        } elseif ($diff < 86400) {
                            $timeText = 'Il y a ' . floor($diff/3600) . ' h';
                        } else {
                            $timeText = date('d/m/Y H:i', $time);
                        }
                    ?>
                    <li class="activity-item">
                        <i class="fas fa-box activity-icon"></i>
                        <div class="activity-content">
                            <p>Nouveau produit publié: <?php echo htmlspecialchars(substr($product['title'], 0, 20)); ?><?php echo strlen($product['title']) > 20 ? '...' : ''; ?></p>
                            <span class="activity-time"><?php echo $timeText; ?></span>
                        </div>
                    </li>
                    <?php 
                        $displayed++;
                    endforeach; 
                    
                    // Afficher ensuite les nouveaux clients
                    foreach ($recentClients as $client):
                        if ($displayed >= 4) break;
                        $time = strtotime($client['created_at']);
                        $now = time();
                        $diff = $now - $time;
                        
                        if ($diff < 60) {
                            $timeText = 'À l\'instant';
                        } elseif ($diff < 3600) {
                            $timeText = 'Il y a ' . floor($diff/60) . ' min';
                        } elseif ($diff < 86400) {
                            $timeText = 'Il y a ' . floor($diff/3600) . ' h';
                        } else {
                            $timeText = date('d/m/Y H:i', $time);
                        }
                    ?>
                    <li class="activity-item">
                        <i class="fas fa-user-plus activity-icon"></i>
                        <div class="activity-content">
                            <p>Nouvel utilisateur inscrit: <?php echo htmlspecialchars($client['nom'] . ' ' . $client['prenom']); ?></p>
                            <span class="activity-time"><?php echo $timeText; ?></span>
                        </div>
                    </li>
                    <?php 
                        $displayed++;
                    endforeach;
                    
                    // Si pas assez d'activités, ajouter des messages par défaut
                    if ($displayed < 4):
                        $defaultActivities = [
                            ['icon' => 'fas fa-comment', 'text' => 'Nouveau message reçu', 'time' => 'À venir'],
                            ['icon' => 'fas fa-shopping-cart', 'text' => 'Nouvelle vente réalisée', 'time' => 'À venir']
                        ];
                        
                        foreach ($defaultActivities as $activity):
                            if ($displayed >= 4) break;
                    ?>
                    <li class="activity-item">
                        <i class="<?php echo $activity['icon']; ?> activity-icon"></i>
                        <div class="activity-content">
                            <p><?php echo $activity['text']; ?></p>
                            <span class="activity-time"><?php echo $activity['time']; ?></span>
                        </div>
                    </li>
                    <?php 
                            $displayed++;
                        endforeach;
                    endif;
                    ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Stats Container */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    display: flex;
    align-items: center;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    font-size: 24px;
}

.icon-sales { background: #ffeaa7; color: #fdcb6e; }
.icon-users { background: #a29bfe; color: #6c5ce7; }
.icon-products { background: #81ecec; color: #00cec9; }
.icon-subscriptions { background: #fab1a0; color: #e17055; }

.stat-info {
    flex: 1;
}

.stat-info h3 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 5px 0;
    color: #2c3e50;
}

.stat-info p {
    margin: 0 0 5px 0;
    color: #7f8c8d;
    font-size: 15px;
    font-weight: 500;
}

.stat-subtitle {
    font-size: 12px;
    color: #95a5a6;
    display: block;
}

/* Dashboard Cards */
.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 25px;
}

@media (max-width: 768px) {
    .dashboard-cards {
        grid-template-columns: 1fr;
    }
}

.card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}

.card-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    color: #2c3e50;
}

.card-header a {
    color: #3498db;
    text-decoration: none;
    font-size: 14px;
}

.card-header a:hover {
    text-decoration: underline;
}

.card-body {
    padding: 20px;
}

/* Sales List */
.sales-list, .activity-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sale-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 15px 0;
    border-bottom: 1px solid #f5f5f5;
}

.sale-item:last-child {
    border-bottom: none;
}

.sale-info h4 {
    margin: 0 0 5px 0;
    font-size: 15px;
    color: #2c3e50;
    font-weight: 600;
}

.sale-info p {
    margin: 0;
    font-size: 13px;
    color: #7f8c8d;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.product-category {
    background: #e8f5e9;
    color: #388e3c;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 11px;
}

.product-seller {
    font-size: 12px;
    color: #7b1fa2;
    font-style: italic;
}

.sale-amount {
    text-align: right;
    font-weight: 700;
    color: #e74c3c;
    font-size: 16px;
}

.sale-time {
    font-size: 11px;
    color: #95a5a6;
    font-weight: normal;
    margin-top: 5px;
}

/* Activity List */
.activity-item {
    display: flex;
    align-items: flex-start;
    padding: 15px 0;
    border-bottom: 1px solid #f5f5f5;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    background: #f8f9fa;
    color: #3498db;
    font-size: 16px;
}

.activity-content {
    flex: 1;
}

.activity-content p {
    margin: 0 0 5px 0;
    font-size: 14px;
    color: #2c3e50;
}

.activity-time {
    font-size: 12px;
    color: #95a5a6;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #95a5a6;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

.empty-state p {
    margin-bottom: 20px;
    font-size: 16px;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .stat-card {
        padding: 15px;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        font-size: 20px;
        margin-right: 15px;
    }
    
    .stat-info h3 {
        font-size: 24px;
    }
    
    .dashboard-cards {
        gap: 20px;
    }
}

@media (max-width: 480px) {
    .stats-container {
        grid-template-columns: 1fr;
    }
    
    .sale-item {
        flex-direction: column;
        gap: 10px;
    }
    
    .sale-amount {
        text-align: left;
        width: 100%;
    }
}
</style>