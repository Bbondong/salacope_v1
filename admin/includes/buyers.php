<?php
// admin/includes/buyers.php
?>

<!-- Page Header -->
<div class="page-header">
    <h1>Gestion des acheteurs</h1>
    <div class="header-actions">
        <div class="buyer-filters">
            <select id="filterActivity">
                <option value="all">Tous les acheteurs</option>
                <option value="active">Actifs (30j)</option>
                <option value="inactive">Inactifs (30j)</option>
                <option value="new">Nouveaux (7j)</option>
            </select>
            <select id="filterSpending">
                <option value="all">Tous les budgets</option>
                <option value="high">Haut budget</option>
                <option value="medium">Moyen budget</option>
                <option value="low">Faible budget</option>
            </select>
        </div>
    </div>
</div>

<!-- Buyers Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <h3>Total acheteurs</h3>
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-value">2,458</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 127 nouveaux ce mois
        </div>
        <div class="stat-breakdown">
            <div class="breakdown-item">
                <span class="label">Actifs</span>
                <span class="value">1,845</span>
            </div>
            <div class="breakdown-item">
                <span class="label">Nouveaux</span>
                <span class="value">127</span>
            </div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Achats totaux</h3>
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-value">8,452</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 15% ce mois
        </div>
        <div class="stat-detail">Moyenne: 3.4 achats/acheteur</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Panier moyen</h3>
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="stat-value">24,500 <span class="currency">FC</span></div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 8% ce mois
        </div>
        <div class="stat-trend">
            <div class="trend-label">Record:</div>
            <div class="trend-value">125,000 FC</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Satisfaction</h3>
            <i class="fas fa-smile"></i>
        </div>
        <div class="stat-value">4.7/5</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 0.2 ce mois
        </div>
        <div class="stat-trend">
            <div class="trend-label">Retours positifs:</div>
            <div class="trend-value">94%</div>
        </div>
    </div>
</div>

<!-- Buyers Table -->
<div class="buyers-table-container">
    <div class="table-header">
        <div class="table-actions">
            <button class="btn btn-secondary btn-sm" id="exportBuyersBtn">
                <i class="fas fa-download"></i> Exporter
            </button>
            <button class="btn btn-secondary btn-sm" id="sendNewsletterBtn">
                <i class="fas fa-envelope"></i> Newsletter
            </button>
        </div>
        <div class="table-search">
            <input type="text" placeholder="Rechercher un acheteur...">
            <i class="fas fa-search"></i>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="buyers-table">
            <thead>
                <tr>
                    <th>Acheteur</th>
                    <th>Dernier achat</th>
                    <th>Total dépensé</th>
                    <th>Commandes</th>
                    <th>Panier moyen</th>
                    <th>Statut</th>
                    <th>Inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="buyer-info">
                            <img src="assets/images/buyer1.jpg" alt="Jean Dupont">
                            <div class="buyer-details">
                                <h4>Jean Dupont</h4>
                                <span class="buyer-email">jean.dupont@email.com</span>
                                <span class="buyer-location">Kinshasa</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="purchase-info">
                            <strong>Aujourd'hui</strong>
                            <span>45,000 FC</span>
                        </div>
                    </td>
                    <td>
                        <div class="total-spent">
                            <strong>245,000 FC</strong>
                            <span class="spent-change positive">+25%</span>
                        </div>
                    </td>
                    <td>12</td>
                    <td>20,400 FC</td>
                    <td><span class="status-badge status-vip">VIP</span></td>
                    <td>2023-11-15</td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn view-btn" title="Voir profil">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn message-btn" title="Message">
                                <i class="fas fa-envelope"></i>
                            </button>
                            <button class="action-btn history-btn" title="Historique">
                                <i class="fas fa-history"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td>
                        <div class="buyer-info">
                            <img src="assets/images/buyer2.jpg" alt="Marie Laurent">
                            <div class="buyer-details">
                                <h4>Marie Laurent</h4>
                                <span class="buyer-email">marie.laurent@email.com</span>
                                <span class="buyer-location">Lubumbashi</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="purchase-info">
                            <strong>Hier</strong>
                            <span>32,000 FC</span>
                        </div>
                    </td>
                    <td>
                        <div class="total-spent">
                            <strong>189,000 FC</strong>
                            <span class="spent-change positive">+18%</span>
                        </div>
                    </td>
                    <td>8</td>
                    <td>23,600 FC</td>
                    <td><span class="status-badge status-regular">Régulier</span></td>
                    <td>2024-01-20</td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn view-btn" title="Voir profil">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn message-btn" title="Message">
                                <i class="fas fa-envelope"></i>
                            </button>
                            <button class="action-btn history-btn" title="Historique">
                                <i class="fas fa-history"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td>
                        <div class="buyer-info">
                            <img src="assets/images/buyer3.jpg" alt="Paul Mbala">
                            <div class="buyer-details">
                                <h4>Paul Mbala</h4>
                                <span class="buyer-email">paul.mbala@email.com</span>
                                <span class="buyer-location">Kisangani</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="purchase-info">
                            <strong>Il y a 3 jours</strong>
                            <span>15,000 FC</span>
                        </div>
                    </td>
                    <td>
                        <div class="total-spent">
                            <strong>45,000 FC</strong>
                            <span class="spent-change positive">+5%</span>
                        </div>
                    </td>
                    <td>3</td>
                    <td>15,000 FC</td>
                    <td><span class="status-badge status-new">Nouveau</span></td>
                    <td>2024-03-01</td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn view-btn" title="Voir profil">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn message-btn" title="Message">
                                <i class="fas fa-envelope"></i>
                            </button>
                            <button class="action-btn offer-btn" title="Offre">
                                <i class="fas fa-gift"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td>
                        <div class="buyer-info">
                            <img src="assets/images/buyer4.jpg" alt="Sarah Kabasele">
                            <div class="buyer-details">
                                <h4>Sarah Kabasele</h4>
                                <span class="buyer-email">sarah.k@email.com</span>
                                <span class="buyer-location">Mbuji-Mayi</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="purchase-info">
                            <strong>Il y a 15 jours</strong>
                            <span>8,000 FC</span>
                        </div>
                    </td>
                    <td>
                        <div class="total-spent">
                            <strong>8,000 FC</strong>
                            <span class="spent-change">-</span>
                        </div>
                    </td>
                    <td>1</td>
                    <td>8,000 FC</td>
                    <td><span class="status-badge status-inactive">Inactif</span></td>
                    <td>2024-02-15</td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn view-btn" title="Voir profil">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn message-btn" title="Message">
                                <i class="fas fa-envelope"></i>
                            </button>
                            <button class="action-btn reactivate-btn" title="Réactiver">
                                <i class="fas fa-redo"></i>
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
            Affichage de 1 à 10 sur 2,458 acheteurs
        </div>
    </div>
</div>

<!-- Buyers Insights -->
<div class="buyers-insights">
    <div class="insight-card">
        <h3><i class="fas fa-map-marker-alt"></i> Répartition géographique</h3>
        <div class="location-distribution">
            <div class="location-item">
                <div class="location-name">Kinshasa</div>
                <div class="location-bar">
                    <div class="bar-fill" style="width: 45%"></div>
                </div>
                <div class="location-percent">45%</div>
            </div>
            <div class="location-item">
                <div class="location-name">Lubumbashi</div>
                <div class="location-bar">
                    <div class="bar-fill" style="width: 25%"></div>
                </div>
                <div class="location-percent">25%</div>
            </div>
            <div class="location-item">
                <div class="location-name">Mbuji-Mayi</div>
                <div class="location-bar">
                    <div class="bar-fill" style="width: 15%"></div>
                </div>
                <div class="location-percent">15%</div>
            </div>
            <div class="location-item">
                <div class="location-name">Autres</div>
                <div class="location-bar">
                    <div class="bar-fill" style="width: 15%"></div>
                </div>
                <div class="location-percent">15%</div>
            </div>
        </div>
    </div>
    
    <div class="insight-card">
        <h3><i class="fas fa-tags"></i> Catégories préférées</h3>
        <div class="categories-distribution">
            <div class="category-item">
                <div class="category-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div class="category-info">
                    <span class="category-name">Électronique</span>
                    <span class="category-percent">35%</span>
                </div>
            </div>
            <div class="category-item">
                <div class="category-icon">
                    <i class="fas fa-tshirt"></i>
                </div>
                <div class="category-info">
                    <span class="category-name">Mode</span>
                    <span class="category-percent">28%</span>
                </div>
            </div>
            <div class="category-item">
                <div class="category-icon">
                    <i class="fas fa-home"></i>
                </div>
                <div class="category-info">
                    <span class="category-name">Maison</span>
                    <span class="category-percent">22%</span>
                </div>
            </div>
            <div class="category-item">
                <div class="category-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="category-info">
                    <span class="category-name">Alimentation</span>
                    <span class="category-percent">15%</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="insight-card">
        <h3><i class="fas fa-bell"></i> Campagnes actives</h3>
        <div class="campaigns-list">
            <div class="campaign-item">
                <div class="campaign-info">
                    <h4>Bienvenue nouveaux</h4>
                    <p>Offre de -10% pour les nouveaux acheteurs</p>
                </div>
                <span class="campaign-stats">127 utilisés</span>
            </div>
            <div class="campaign-item">
                <div class="campaign-info">
                    <h4>Fidélité VIP</h4>
                    <p>Programme de fidélité pour acheteurs réguliers</p>
                </div>
                <span class="campaign-stats">45 membres</span>
            </div>
            <div class="campaign-item">
                <div class="campaign-info">
                    <h4>Newsletter hebdo</h4>
                    <p>Promotions et nouvelles arrivées</p>
                </div>
                <span class="campaign-stats">1,845 abonnés</span>
            </div>
        </div>
    </div>
</div>