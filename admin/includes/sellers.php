<?php
// admin/includes/sellers.php
?>

<!-- Page Header -->
<div class="page-header">
    <h1>Gestion des vendeurs</h1>
    <div class="header-actions">
        <button class="btn btn-primary" id="newSellerBtn">
            <i class="fas fa-plus"></i> Ajouter un vendeur
        </button>
        <div class="seller-filters">
            <select id="filterPlan">
                <option value="all">Tous les plans</option>
                <option value="premium">Premium</option>
                <option value="standard">Standard</option>
                <option value="trial">Essai</option>
            </select>
            <select id="filterStatus">
                <option value="all">Tous les statuts</option>
                <option value="active">Actifs</option>
                <option value="pending">En attente</option>
                <option value="suspended">Suspendus</option>
            </select>
        </div>
    </div>
</div>

<!-- Sellers Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <h3>Total vendeurs</h3>
            <i class="fas fa-store"></i>
        </div>
        <div class="stat-value">89</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 12% ce mois
        </div>
        <div class="stat-breakdown">
            <div class="breakdown-item">
                <span class="label">Actifs</span>
                <span class="value">76</span>
            </div>
            <div class="breakdown-item">
                <span class="label">Essai</span>
                <span class="value">13</span>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Revenus vendeurs</h3>
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-value">4.2M <span class="currency">FC</span></div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 18% ce mois
        </div>
        <div class="stat-detail">Moyenne: 52K FC/vendeur</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Nouveaux vendeurs</h3>
            <i class="fas fa-user-plus"></i>
        </div>
        <div class="stat-value">24</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 8 ce mois
        </div>
        <div class="stat-trend">
            <div class="trend-label">Taux de conversion:</div>
            <div class="trend-value">65%</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Engagement moyen</h3>
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-value">78%</div>
        <div class="stat-change negative">
            <i class="fas fa-arrow-down"></i> 3% ce mois
        </div>
        <div class="stat-trend">
            <div class="trend-label">Satisfaction:</div>
            <div class="trend-value">4.5/5</div>
        </div>
    </div>
</div>

<!-- Sellers Table -->
<div class="sellers-table-container">
    <div class="table-header">
        <div class="table-actions">
            <button class="btn btn-secondary btn-sm" id="bulkActivateBtn">
                <i class="fas fa-check"></i> Activer
            </button>
            <button class="btn btn-secondary btn-sm" id="bulkSuspendBtn">
                <i class="fas fa-ban"></i> Suspendre
            </button>
            <button class="btn btn-secondary btn-sm" id="bulkDeleteBtn">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
        <div class="table-search">
            <input type="text" placeholder="Rechercher un vendeur...">
            <i class="fas fa-search"></i>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="sellers-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllSellers"></th>
                    <th>Vendeur</th>
                    <th>Plan</th>
                    <th>Statut</th>
                    <th>Revenus</th>
                    <th>Produits</th>
                    <th>Note</th>
                    <th>Inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox" class="seller-checkbox"></td>
                    <td>
                        <div class="seller-info">
                            <img src="assets/images/seller1.jpg" alt="TechGadgets">
                            <div class="seller-details">
                                <h4>TechGadgets</h4>
                                <span class="seller-email">contact@techgadgets.com</span>
                                <span class="seller-phone">+243 81 234 5678</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="plan-badge plan-premium">Premium</span></td>
                    <td><span class="status-badge status-active">Actif</span></td>
                    <td>
                        <div class="revenue-info">
                            <strong>245,000 FC</strong>
                            <span class="revenue-change positive">+12%</span>
                        </div>
                    </td>
                    <td>45</td>
                    <td>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            <span>4.5</span>
                        </div>
                    </td>
                    <td>2024-01-15</td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn view-btn" title="Voir">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn edit-btn" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn message-btn" title="Message">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td><input type="checkbox" class="seller-checkbox"></td>
                    <td>
                        <div class="seller-info">
                            <img src="assets/images/seller2.jpg" alt="FashionHub">
                            <div class="seller-details">
                                <h4>FashionHub</h4>
                                <span class="seller-email">info@fashionhub.com</span>
                                <span class="seller-phone">+243 97 876 5432</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="plan-badge plan-premium">Premium</span></td>
                    <td><span class="status-badge status-active">Actif</span></td>
                    <td>
                        <div class="revenue-info">
                            <strong>189,000 FC</strong>
                            <span class="revenue-change positive">+8%</span>
                        </div>
                    </td>
                    <td>78</td>
                    <td>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <span>4.0</span>
                        </div>
                    </td>
                    <td>2024-02-01</td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn view-btn" title="Voir">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn edit-btn" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn message-btn" title="Message">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td><input type="checkbox" class="seller-checkbox"></td>
                    <td>
                        <div class="seller-info">
                            <img src="assets/images/seller3.jpg" alt="HomeDecor">
                            <div class="seller-details">
                                <h4>HomeDecor</h4>
                                <span class="seller-email">sales@homedecor.com</span>
                                <span class="seller-phone">+243 99 123 4567</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="plan-badge plan-standard">Standard</span></td>
                    <td><span class="status-badge status-active">Actif</span></td>
                    <td>
                        <div class="revenue-info">
                            <strong>156,000 FC</strong>
                            <span class="revenue-change positive">+15%</span>
                        </div>
                    </td>
                    <td>32</td>
                    <td>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <span>5.0</span>
                        </div>
                    </td>
                    <td>2024-02-15</td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn view-btn" title="Voir">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn edit-btn" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn upgrade-btn" title="Upgrade">
                                <i class="fas fa-crown"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td><input type="checkbox" class="seller-checkbox"></td>
                    <td>
                        <div class="seller-info">
                            <img src="assets/images/seller4.jpg" alt="BeautyShop">
                            <div class="seller-details">
                                <h4>BeautyShop</h4>
                                <span class="seller-email">contact@beautyshop.com</span>
                                <span class="seller-phone">+243 81 987 6543</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="plan-badge plan-trial">Essai</span></td>
                    <td><span class="status-badge status-pending">En attente</span></td>
                    <td>
                        <div class="revenue-info">
                            <strong>0 FC</strong>
                            <span class="revenue-change">-</span>
                        </div>
                    </td>
                    <td>5</td>
                    <td>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                            <i class="far fa-star"></i>
                            <span>3.0</span>
                        </div>
                    </td>
                    <td>2024-03-01</td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn approve-btn" title="Approuver">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="action-btn reject-btn" title="Rejeter">
                                <i class="fas fa-times"></i>
                            </button>
                            <button class="action-btn message-btn" title="Message">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="table-footer">
        <div class="table-pagination">
            <button class="pagination-btn" disabled>
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="pagination-btn active">1</button>
            <button class="pagination-btn">2</button>
            <button class="pagination-btn">3</button>
            <button class="pagination-btn">4</button>
            <button class="pagination-btn">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <div class="table-info">
            Affichage de 1 à 10 sur 89 vendeurs
        </div>
    </div>
</div>

<!-- Sellers Overview -->
<div class="sellers-overview">
    <div class="overview-card">
        <h3><i class="fas fa-chart-pie"></i> Répartition par plan</h3>
        <div class="plan-distribution">
            <div class="plan-item">
                <div class="plan-color premium"></div>
                <div class="plan-info">
                    <span class="plan-name">Premium</span>
                    <span class="plan-count">56 vendeurs</span>
                </div>
                <div class="plan-percent">63%</div>
            </div>
            <div class="plan-item">
                <div class="plan-color standard"></div>
                <div class="plan-info">
                    <span class="plan-name">Standard</span>
                    <span class="plan-count">20 vendeurs</span>
                </div>
                <div class="plan-percent">22%</div>
            </div>
            <div class="plan-item">
                <div class="plan-color trial"></div>
                <div class="plan-info">
                    <span class="plan-name">Essai</span>
                    <span class="plan-count">13 vendeurs</span>
                </div>
                <div class="plan-percent">15%</div>
            </div>
        </div>
    </div>
    
    <div class="overview-card">
        <h3><i class="fas fa-bullseye"></i> Objectifs du mois</h3>
        <div class="goals-progress">
            <div class="goal-item">
                <div class="goal-info">
                    <span class="goal-name">Nouveaux vendeurs</span>
                    <span class="goal-progress">24/30</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 80%"></div>
                </div>
            </div>
            <div class="goal-item">
                <div class="goal-info">
                    <span class="goal-name">Conversion Premium</span>
                    <span class="goal-progress">12/20</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 60%"></div>
                </div>
            </div>
            <div class="goal-item">
                <div class="goal-info">
                    <span class="goal-name">Satisfaction</span>
                    <span class="goal-progress">4.5/5</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 90%"></div>
                </div>
            </div>
        </div>
    </div>
</div>