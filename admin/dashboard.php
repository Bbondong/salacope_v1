<?php
session_start();
require_once '../backend/config.php';

// Récupérer les statistiques
try {
    // Produits en ligne (seule table que vous avez pour le moment)
    $stmtProducts = $bd->prepare("SELECT COUNT(*) as total FROM products WHERE status = 'published'");
    $stmtProducts->execute();
    $productsCount = $stmtProducts->fetch()['total'];
    
    // Utilisateurs actifs (clients)
    $stmtClients = $bd->prepare("SELECT COUNT(*) as total FROM client");
    $stmtClients->execute();
    $clientsCount = $stmtClients->fetch()['total'];
    
    // Admins
    $stmtAdmins = $bd->prepare("SELECT COUNT(*) as total FROM admin");
    $stmtAdmins->execute();
    $adminsCount = $stmtAdmins->fetch()['total'];
    
    // Derniers produits publiés
    $stmtRecentProducts = $bd->prepare("
        SELECT p.title, p.price, p.created_at, p.city, 
               c.name as category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.status = 'published'
        ORDER BY p.created_at DESC
        LIMIT 5
    ");
    $stmtRecentProducts->execute();
    $recentProducts = $stmtRecentProducts->fetchAll(PDO::FETCH_ASSOC);
    
    // Derniers clients inscrits
    $stmtRecentClients = $bd->prepare("
        SELECT nom, prenom, tel, created_at 
        FROM client 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $stmtRecentClients->execute();
    $recentClients = $stmtRecentClients->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Erreur dashboard stats: " . $e->getMessage());
    $productsCount = 0;
    $clientsCount = 0;
    $adminsCount = 0;
    $recentProducts = [];
    $recentClients = [];
}
?>

<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon icon-products">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $productsCount; ?></h3>
            <p>Produits en ligne</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon icon-users">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $clientsCount; ?></h3>
            <p>Clients inscrits</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon icon-admin">
            <i class="fas fa-user-shield"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $adminsCount; ?></h3>
            <p>Administrateurs</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon icon-categories">
            <i class="fas fa-tags"></i>
        </div>
        <div class="stat-info">
            <h3 id="categories-count">0</h3>
            <p>Catégories</p>
        </div>
    </div>
</div>

<div class="dashboard-cards">
    <div class="card">
        <div class="card-header">
            <h3>Produits récemment publiés</h3>
            <a href="?page=produits">Voir tout</a>
        </div>
        <div class="card-body">
            <?php if (empty($recentProducts)): ?>
                <div style="text-align: center; padding: 20px; color: #7f8c8d;">
                    <i class="fas fa-box-open" style="font-size: 2rem; margin-bottom: 10px;"></i>
                    <p>Aucun produit publié</p>
                </div>
            <?php else: ?>
                <ul class="sales-list">
                    <?php foreach ($recentProducts as $product): ?>
                        <li class="sale-item">
                            <div class="sale-info">
                                <h4><?php echo htmlspecialchars(substr($product['title'], 0, 30)); ?><?php echo strlen($product['title']) > 30 ? '...' : ''; ?></h4>
                                <p>
                                    <span class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'Non catégorisé'); ?></span>
                                    <span class="product-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($product['city']); ?></span>
                                </p>
                            </div>
                            <div class="sale-amount">
                                <?php echo number_format($product['price'], 2, ',', ' '); ?> €
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3>Nouveaux clients</h3>
            <a href="?page=clients">Voir tout</a>
        </div>
        <div class="card-body">
            <?php if (empty($recentClients)): ?>
                <div style="text-align: center; padding: 20px; color: #7f8c8d;">
                    <i class="fas fa-user-plus" style="font-size: 2rem; margin-bottom: 10px;"></i>
                    <p>Aucun nouveau client</p>
                </div>
            <?php else: ?>
                <ul class="activity-list">
                    <?php foreach ($recentClients as $client): ?>
                        <li class="activity-item">
                            <i class="fas fa-user-plus activity-icon"></i>
                            <div class="activity-content">
                                <p><strong><?php echo htmlspecialchars($client['nom'] . ' ' . $client['prenom']); ?></strong></p>
                                <p>Tél: <?php echo htmlspecialchars($client['tel']); ?></p>
                                <span class="activity-time">
                                    <?php 
                                        $time = strtotime($client['created_at']);
                                        $now = time();
                                        $diff = $now - $time;
                                        
                                        if ($diff < 60) {
                                            echo 'À l\'instant';
                                        } elseif ($diff < 3600) {
                                            echo 'Il y a ' . floor($diff/60) . ' min';
                                        } elseif ($diff < 86400) {
                                            echo 'Il y a ' . floor($diff/3600) . ' h';
                                        } else {
                                            echo 'Il y a ' . floor($diff/86400) . ' j';
                                        }
                                    ?>
                                </span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
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
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
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

.icon-products { background: #e3f2fd; color: #1976d2; }
.icon-users { background: #f3e5f5; color: #7b1fa2; }
.icon-admin { background: #e8f5e9; color: #388e3c; }
.icon-categories { background: #fff3e0; color: #f57c00; }

.stat-info h3 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 5px 0;
    color: #2c3e50;
}

.stat-info p {
    margin: 0;
    color: #7f8c8d;
    font-size: 14px;
}

.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 25px;
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

.sales-list, .activity-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sale-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
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
}

.sale-info p {
    margin: 0;
    font-size: 13px;
    color: #7f8c8d;
    display: flex;
    gap: 15px;
    align-items: center;
}

.product-category {
    background: #e8f5e9;
    color: #388e3c;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 11px;
}

.product-location {
    font-size: 12px;
    color: #7f8c8d;
}

.product-location i {
    margin-right: 3px;
}

.sale-amount {
    font-weight: 700;
    color: #e74c3c;
    font-size: 16px;
}

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

@media (max-width: 768px) {
    .stats-container {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    
    .dashboard-cards {
        grid-template-columns: 1fr;
    }
    
    .card {
        margin-bottom: 20px;
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
}
</style>

<script>
// Charger le nombre de catégories via AJAX
document.addEventListener('DOMContentLoaded', function() {
    loadCategoriesCount();
});

async function loadCategoriesCount() {
    try {
        const response = await fetch('../backend/traitement/get_categories_count.php');
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('categories-count').textContent = data.count;
        }
    } catch (error) {
        console.error('Erreur chargement catégories:', error);
    }
}
</script>