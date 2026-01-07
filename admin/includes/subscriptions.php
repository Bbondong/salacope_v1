<?php
// admin/includes/subscriptions.php
?>

<!-- Page Header -->
<div class="page-header">
    <h1>Gestion des abonnements</h1>
    <div class="header-actions">
        <button class="btn btn-primary" id="newSubscriptionBtn">
            <i class="fas fa-plus"></i> Ajouter un abonnement
        </button>
        <div class="subscription-filters">
            <select id="filterPlan">
                <option value="all">Tous les plans</option>
                <option value="premium">Premium</option>
                <option value="standard">Standard</option>
                <option value="trial">Essai</option>
            </select>
            <select id="filterStatus">
                <option value="all">Tous les statuts</option>
                <option value="active">Actifs</option>
                <option value="expired">Expirés</option>
                <option value="pending">En attente</option>
                <option value="cancelled">Annulés</option>
            </select>
        </div>
    </div>
</div>

<!-- Subscriptions Stats -->
<div class="stats-grid">
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
            <h3>Revenus mensuels</h3>
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-value">1.2M <span class="currency">FC</span></div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 12% ce mois
        </div>
        <div class="stat-detail">MRR: 1,245,000 FC</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Taux de renouvellement</h3>
            <i class="fas fa-sync-alt"></i>
        </div>
        <div class="stat-value">92%</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 3% ce mois
        </div>
        <div class="stat-trend">
            <div class="trend-label">Churn rate:</div>
            <div class="trend-value">8%</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Valeur à vie (LTV)</h3>
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-value">85,000 <span class="currency">FC</span></div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 8% ce trimestre
        </div>
        <div class="stat-trend">
            <div class="trend-label">CAC:</div>
            <div class="trend-value">15,000 FC</div>
        </div>
    </div>
</div>

<!-- Subscriptions Table -->
<div class="subscriptions-table-container">
    <div class="table-header">
        <div class="table-actions">
            <button class="btn btn-secondary btn-sm" id="exportSubscriptionsBtn">
                <i class="fas fa-download"></i> Exporter
            </button>
            <button class="btn btn-secondary btn-sm" id="renewAllBtn">
                <i class="fas fa-redo"></i> Renouveler
            </button>
            <button class="btn btn-secondary btn-sm" id="sendRemindersBtn">
                <i class="fas fa-bell"></i> Rappels
            </button>
        </div>
        <div class="table-search">
            <input type="text" placeholder="Rechercher un abonnement...">
            <i class="fas fa-search"></i>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="subscriptions-table">
            <thead>
                <tr>
                    <th>Vendeur</th>
                    <th>Plan</th>
                    <th>Prix</th>
                    <th>Période</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Prochain paiement</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="seller-info">
                            <img src="assets/images/seller1.jpg" alt="TechGadgets">
                            <div class="seller-details">
                                <h4>TechGadgets</h4>
                                <span class="seller-email">contact@techgadgets.com</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="plan-badge plan-premium">Premium</span></td>
                    <td>
                        <div class="price-info">
                            <strong>9,000 FC</strong>
                            <span class="price-period">/mois</span>
                        </div>
                    </td>
                    <td>Mensuel</td>
                    <td>2024-01-15</td>
                    <td>2024-04-15</td>
                    <td>
                        <div class="next-payment">
                            <strong>2024-04-15</strong>
                            <span class="payment-days">Dans 30 jours</span>
                        </div>
                    </td>
                    <td><span class="status-badge status-active">Actif</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn view-btn" title="Voir">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn renew-btn" title="Renouveler">
                                <i class="fas fa-redo"></i>
                            </button>
                            <button class="action-btn cancel-btn" title="Annuler">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td>
                        <div class="seller-info">
                            <img src="assets/images/seller2.jpg" alt="FashionHub">
                            <div class="seller-details">
                                <h4>FashionHub</h4>
                                <span class="seller-email">info@fashionhub.com</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="plan-badge plan-premium">Premium</span></td>
                    <td>
                        <div class="price-info">
                            <strong>9,000 FC</strong>
                            <span class="price-period">/mois</span>
                        </div>
                    </td>
                    <td>Mensuel</td>
                    <td>2024-02-01</td>
                    <td>2024-05-01</td>
                    <td>
                        <div class="next-payment">
                            <strong>2024-05-01</strong>
                            <span class="payment-days">Dans 45 jours</span>
                        </div>
                    </td>
                    <td><span class="status-badge status-active">Actif</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn view-btn" title="Voir">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn renew-btn" title="Renouveler">
                                <i class="fas fa-redo"></i>
                            </button>
                            <button class="action-btn upgrade-btn" title="Changer de plan">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td>
                        <div class="seller-info">
                            <img src="assets/images/seller3.jpg" alt="HomeDecor">
                            <div class="seller-details">
                                <h4>HomeDecor</h4>
                                <span class="seller-email">sales@homedecor.com</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="plan-badge plan-standard">Standard</span></td>
                    <td>
                        <div class="price-info">
                            <strong>7,000 FC</strong>
                            <span class="price-period">/mois</span>
                        </div>
                    </td>
                    <td>Mensuel</td>
                    <td>2024-02-15</td>
                    <td>2024-03-15</td>
                    <td>
                        <div class="next-payment">
                            <strong>2024-03-15</strong>
                            <span class="payment-days warning">Aujourd'hui</span>
                        </div>
                    </td>
                    <td><span class="status-badge status-pending">En attente</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn pay-btn" title="Marquer comme payé">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="action-btn remind-btn" title="Envoyer un rappel">
                                <i class="fas fa-bell"></i>
                            </button>
                            <button class="action-btn cancel-btn" title="Annuler">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td>
                        <div class="seller-info">
                            <img src="assets/images/seller4.jpg" alt="BeautyShop">
                            <div class="seller-details">
                                <h4>BeautyShop</h4>
                                <span class="seller-email">contact@beautyshop.com</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="plan-badge plan-trial">Essai</span></td>
                    <td>
                        <div class="price-info">
                            <strong>0 FC</strong>
                            <span class="price-period">/30 jours</span>
                        </div>
                    </td>
                    <td>Essai</td>
                    <td>2024-03-01</td>
                    <td>2024-03-31</td>
                    <td>
                        <div class="next-payment">
                            <strong>2024-03-31</strong>
                            <span class="payment-days">Dans 16 jours</span>
                        </div>
                    </td>
                    <td><span class="status-badge status-active">Actif</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn upgrade-btn" title="Upgrader">
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            <button class="action-btn extend-btn" title="Prolonger">
                                <i class="fas fa-calendar-plus"></i>
                            </button>
                            <button class="action-btn cancel-btn" title="Annuler">
                                <i class="fas fa-times"></i>
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
            Affichage de 1 à 10 sur 156 abonnements
        </div>
    </div>
</div>

<!-- Subscriptions Analytics -->
<div class="subscriptions-analytics">
    <div class="analytics-card">
        <h3><i class="fas fa-chart-pie"></i> Répartition des revenus</h3>
        <div class="revenue-distribution">
            <div class="revenue-item">
                <div class="revenue-color premium"></div>
                <div class="revenue-info">
                    <span class="revenue-source">Premium</span>
                    <span class="revenue-amount">801,000 FC</span>
                </div>
                <div class="revenue-percent">67%</div>
            </div>
            <div class="revenue-item">
                <div class="revenue-color standard"></div>
                <div class="revenue-info">
                    <span class="revenue-source">Standard</span>
                    <span class="revenue-amount">394,000 FC</span>
                </div>
                <div class="revenue-percent">33%</div>
            </div>
        </div>
    </div>
    
    <div class="analytics-card">
        <h3><i class="fas fa-calendar-alt"></i> Renouvellements à venir</h3>
        <div class="upcoming-renewals">
            <div class="renewal-item">
                <div class="renewal-date">
                    <span class="date-day">15</span>
                    <span class="date-month">Mars</span>
                </div>
                <div class="renewal-info">
                    <h4>TechGadgets</h4>
                    <span class="renewal-plan">Premium - 9,000 FC</span>
                </div>
                <span class="renewal-status active">À renouveler</span>
            </div>
            <div class="renewal-item">
                <div class="renewal-date">
                    <span class="date-day">01</span>
                    <span class="date-month">Avr</span>
                </div>
                <div class="renewal-info">
                    <h4>FashionHub</h4>
                    <span class="renewal-plan">Premium - 9,000 FC</span>
                </div>
                <span class="renewal-status warning">Rappel à envoyer</span>
            </div>
            <div class="renewal-item">
                <div class="renewal-date">
                    <span class="date-day">15</span>
                    <span class="date-month">Avr</span>
                </div>
                <div class="renewal-info">
                    <h4>HomeDecor</h4>
                    <span class="renewal-plan">Standard - 7,000 FC</span>
                </div>
                <span class="renewal-status pending">Paiement en attente</span>
            </div>
        </div>
    </div>
    
    <div class="analytics-card">
        <h3><i class="fas fa-chart-line"></i> Tendance des abonnements</h3>
        <div class="subscription-trend">
            <div class="trend-stats">
                <div class="trend-stat">
                    <span class="stat-label">Nouveaux ce mois</span>
                    <span class="stat-value positive">+12</span>
                </div>
                <div class="trend-stat">
                    <span class="stat-label">Annulations ce mois</span>
                    <span class="stat-value negative">-3</span>
                </div>
                <div class="trend-stat">
                    <span class="stat-label">Taux de croissance</span>
                    <span class="stat-value positive">+8.3%</span>
                </div>
            </div>
            <div class="trend-chart">
                <!-- Simple chart visualization -->
                <div class="chart-bars">
                    <div class="chart-bar" style="height: 60%"></div>
                    <div class="chart-bar" style="height: 75%"></div>
                    <div class="chart-bar" style="height: 85%"></div>
                    <div class="chart-bar" style="height: 92%"></div>
                    <div class="chart-bar" style="height: 100%"></div>
                </div>
                <div class="chart-labels">
                    <span>Nov</span>
                    <span>Déc</span>
                    <span>Jan</span>
                    <span>Fév</span>
                    <span>Mar</span>
                </div>
            </div>
        </div>
    </div>
</div>