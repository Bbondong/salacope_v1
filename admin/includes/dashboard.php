<?php
// admin/includes/dashboard.php
?>

<!-- Page Header -->
<div class="page-header">
    <h1>Tableau de bord principal</h1>
    <div class="header-actions">
        <button class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle publication
        </button>
        <button class="btn btn-secondary">
            <i class="fas fa-download"></i> Exporter
        </button>
        <div class="date-selector">
            <i class="fas fa-calendar-alt"></i>
            <select id="periodSelect">
                <option value="today">Aujourd'hui</option>
                <option value="week" selected>Cette semaine</option>
                <option value="month">Ce mois</option>
                <option value="quarter">Ce trimestre</option>
                <option value="year">Cette année</option>
            </select>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <h3>Revenus totaux</h3>
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-value">12 450 000 <span class="currency">FC</span></div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 12.5% depuis la semaine dernière
        </div>
        <div class="stat-detail">Abonnements: 8.2M FC | Ventes: 4.25M FC</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Abonnements actifs</h3>
            <i class="fas fa-crown"></i>
        </div>
        <div class="stat-value">156</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 8 nouveaux cette semaine
        </div>
        <div class="stat-breakdown">
            <div class="breakdown-item">
                <span class="label">Premium</span>
                <span class="value">89</span>
            </div>
            <div class="breakdown-item">
                <span class="label">Standard</span>
                <span class="value">67</span>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Nouveaux vendeurs</h3>
            <i class="fas fa-store"></i>
        </div>
        <div class="stat-value">24</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 15% croissance
        </div>
        <div class="stat-trend">
            <div class="trend-label">Moyenne mensuelle:</div>
            <div class="trend-value">18 vendeurs/mois</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Nouveaux acheteurs</h3>
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-value">127</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 22% croissance
        </div>
        <div class="stat-trend">
            <div class="trend-label">Total acheteurs:</div>
            <div class="trend-value">2,458</div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-section">
    <div class="chart-card large">
        <div class="chart-header">
            <h3>Revenus par type d'abonnement</h3>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-color" style="background: #e74c3c"></span>
                    <span>Premium</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color" style="background: #3498db"></span>
                    <span>Standard</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color" style="background: #f39c12"></span>
                    <span>Essai</span>
                </div>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="subscriptionRevenueChart"></canvas>
        </div>
    </div>
    
    <div class="chart-card">
        <div class="chart-header">
            <h3>Distribution des abonnements</h3>
        </div>
        <div class="chart-container">
            <canvas id="subscriptionDistributionChart"></canvas>
        </div>
        <div class="chart-summary">
            <div class="summary-item">
                <span class="summary-label">Premium</span>
                <span class="summary-value">57%</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Standard</span>
                <span class="summary-value">43%</span>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & Top Sellers -->
<div class="content-grid">
    <div class="activity-card">
        <div class="card-header">
            <h3>Activité récente</h3>
            <a href="#" class="view-all">Voir tout</a>
        </div>
        <div class="activity-list">
            <div class="activity-item">
                <div class="activity-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="activity-content">
                    <p><strong>Marie Laurent</strong> a souscrit à Premium</p>
                    <span class="activity-time">Il y a 5 minutes</span>
                </div>
                <div class="activity-amount">+9,000 FC</div>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="activity-content">
                    <p>Paiement échoué pour <strong>Tech Solutions</strong></p>
                    <span class="activity-time">Il y a 25 minutes</span>
                </div>
                <div class="activity-amount">-7,000 FC</div>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon info">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="activity-content">
                    <p>Nouveau vendeur: <strong>Fashion Store</strong></p>
                    <span class="activity-time">Il y a 1 heure</span>
                </div>
                <div class="activity-amount">Nouveau</div>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon primary">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="activity-content">
                    <p>Vente importante chez <strong>Electro Hub</strong></p>
                    <span class="activity-time">Il y a 2 heures</span>
                </div>
                <div class="activity-amount">+45,000 FC</div>
            </div>
            
            <div class="activity-item">
                <div class="activity-icon success">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="activity-content">
                    <p>Renouvellement Standard pour <strong>Auto Parts</strong></p>
                    <span class="activity-time">Il y a 3 heures</span>
                </div>
                <div class="activity-amount">+7,000 FC</div>
            </div>
        </div>
    </div>
    
    <div class="sellers-card">
        <div class="card-header">
            <h3>Top vendeurs du mois</h3>
            <a href="#" class="view-all">Classement complet</a>
        </div>
        <div class="sellers-list">
            <div class="seller-item top">
                <div class="seller-rank">1</div>
                <div class="seller-avatar">
                    <img src="assets/images/seller1.jpg" alt="TechGadgets">
                </div>
                <div class="seller-info">
                    <h4>TechGadgets</h4>
                    <span class="seller-category">Électronique</span>
                </div>
                <div class="seller-stats">
                    <div class="stat">
                        <i class="fas fa-chart-line"></i>
                        <span>245,000 FC</span>
                    </div>
                    <div class="stat">
                        <i class="fas fa-shopping-cart"></i>
                        <span>89 ventes</span>
                    </div>
                </div>
            </div>
            
            <div class="seller-item">
                <div class="seller-rank">2</div>
                <div class="seller-avatar">
                    <img src="assets/images/seller2.jpg" alt="FashionHub">
                </div>
                <div class="seller-info">
                    <h4>FashionHub</h4>
                    <span class="seller-category">Mode</span>
                </div>
                <div class="seller-stats">
                    <div class="stat">
                        <i class="fas fa-chart-line"></i>
                        <span>189,000 FC</span>
                    </div>
                    <div class="stat">
                        <i class="fas fa-shopping-cart"></i>
                        <span>124 ventes</span>
                    </div>
                </div>
            </div>
            
            <div class="seller-item">
                <div class="seller-rank">3</div>
                <div class="seller-avatar">
                    <img src="assets/images/seller3.jpg" alt="HomeDecor">
                </div>
                <div class="seller-info">
                    <h4>HomeDecor</h4>
                    <span class="seller-category">Décoration</span>
                </div>
                <div class="seller-stats">
                    <div class="stat">
                        <i class="fas fa-chart-line"></i>
                        <span>156,000 FC</span>
                    </div>
                    <div class="stat">
                        <i class="fas fa-shopping-cart"></i>
                        <span>78 ventes</span>
                    </div>
                </div>
            </div>
            
            <div class="seller-item">
                <div class="seller-rank">4</div>
                <div class="seller-avatar">
                    <img src="assets/images/seller4.jpg" alt="BeautyShop">
                </div>
                <div class="seller-info">
                    <h4>BeautyShop</h4>
                    <span class="seller-category">Beauté</span>
                </div>
                <div class="seller-stats">
                    <div class="stat">
                        <i class="fas fa-chart-line"></i>
                        <span>132,000 FC</span>
                    </div>
                    <div class="stat">
                        <i class="fas fa-shopping-cart"></i>
                        <span>95 ventes</span>
                    </div>
                </div>
            </div>
            
            <div class="seller-item">
                <div class="seller-rank">5</div>
                <div class="seller-avatar">
                    <img src="assets/images/seller5.jpg" alt="SportZone">
                </div>
                <div class="seller-info">
                    <h4>SportZone</h4>
                    <span class="seller-category">Sports</span>
                </div>
                <div class="seller-stats">
                    <div class="stat">
                        <i class="fas fa-chart-line"></i>
                        <span>118,000 FC</span>
                    </div>
                    <div class="stat">
                        <i class="fas fa-shopping-cart"></i>
                        <span>67 ventes</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Publications -->
<div class="publications-card">
    <div class="card-header">
        <h3>Publications récentes</h3>
        <button class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Publier
        </button>
    </div>
    <div class="publications-grid">
        <div class="publication-item">
            <div class="publication-header">
                <div class="publisher">
                    <img src="assets/images/seller1.jpg" alt="TechGadgets">
                    <div class="publisher-info">
                        <h4>TechGadgets</h4>
                        <span>Il y a 2 heures</span>
                    </div>
                </div>
                <div class="publication-actions">
                    <button class="action-btn">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
            <div class="publication-content">
                <p>Nouvelle arrivée! Smartphone XYZ avec caméra 108MP et charge rapide 120W. Disponible dès maintenant! 📱⚡</p>
                <div class="publication-image">
                    <img src="assets/images/product1.jpg" alt="Produit">
                </div>
            </div>
            <div class="publication-stats">
                <div class="stat">
                    <i class="fas fa-eye"></i> 1.2K vues
                </div>
                <div class="stat">
                    <i class="fas fa-heart"></i> 245 j'aime
                </div>
                <div class="stat">
                    <i class="fas fa-comment"></i> 89 commentaires
                </div>
                <div class="stat">
                    <i class="fas fa-share"></i> 45 partages
                </div>
            </div>
        </div>
        
        <div class="publication-item">
            <div class="publication-header">
                <div class="publisher">
                    <img src="assets/images/seller2.jpg" alt="FashionHub">
                    <div class="publisher-info">
                        <h4>FashionHub</h4>
                        <span>Il y a 5 heures</span>
                    </div>
                </div>
                <div class="publication-actions">
                    <button class="action-btn">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>
            <div class="publication-content">
                <p>Collection été 2024 disponible! Robes légères et accessoires tendance. Profitez de -30% sur toute la collection! 👗✨</p>
                <div class="publication-image">
                    <img src="assets/images/product2.jpg" alt="Produit">
                </div>
            </div>
            <div class="publication-stats">
                <div class="stat">
                    <i class="fas fa-eye"></i> 2.4K vues
                </div>
                <div class="stat">
                    <i class="fas fa-heart"></i> 512 j'aime
                </div>
                <div class="stat">
                    <i class="fas fa-comment"></i> 156 commentaires
                </div>
                <div class="stat">
                    <i class="fas fa-share"></i> 89 partages
                </div>
            </div>
        </div>
    </div>
</div>