<?php
// admin/includes/articles.php
?>

<!-- Page Header -->
<div class="page-header">
    <h1>Gestion des articles</h1>
    <div class="header-actions">
        <a href="index.php?page=articles&action=new" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvel article
        </a>
        <div class="article-filters">
            <select id="filterCategory">
                <option value="all">Toutes les catégories</option>
                <option value="news">Actualités</option>
                <option value="tips">Conseils</option>
                <option value="promo">Promotions</option>
                <option value="guide">Guides</option>
            </select>
            <select id="filterStatus">
                <option value="all">Tous les statuts</option>
                <option value="published">Publiés</option>
                <option value="draft">Brouillons</option>
                <option value="scheduled">Programmés</option>
            </select>
        </div>
    </div>
</div>

<!-- Articles Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <h3>Articles totaux</h3>
            <i class="fas fa-newspaper"></i>
        </div>
        <div class="stat-value">145</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 8 nouveaux ce mois
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Articles publiés</h3>
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-value">89</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 12% ce mois
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Vues totales</h3>
            <i class="fas fa-eye"></i>
        </div>
        <div class="stat-value">245K</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 18% ce mois
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Taux d'engagement</h3>
            <i class="fas fa-heart"></i>
        </div>
        <div class="stat-value">24%</div>
        <div class="stat-change negative">
            <i class="fas fa-arrow-down"></i> 2% ce mois
        </div>
    </div>
</div>

<!-- Articles Table -->
<div class="articles-table-container">
    <div class="table-header">
        <div class="table-actions">
            <button class="btn btn-secondary btn-sm" id="bulkPublishBtn">
                <i class="fas fa-check"></i> Publier
            </button>
            <button class="btn btn-secondary btn-sm" id="bulkDraftBtn">
                <i class="fas fa-file-alt"></i> Brouillon
            </button>
            <button class="btn btn-secondary btn-sm" id="bulkDeleteBtn">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
        <div class="table-search">
            <input type="text" placeholder="Rechercher un article...">
            <i class="fas fa-search"></i>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="articles-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllArticles"></th>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Catégorie</th>
                    <th>Statut</th>
                    <th>Vues</th>
                    <th>Date de publication</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox" class="article-checkbox"></td>
                    <td>
                        <div class="article-preview">
                            <img src="assets/images/article1.jpg" alt="Article">
                            <div class="preview-info">
                                <h4>Comment booster vos ventes en ligne</h4>
                                <p>Guide complet pour augmenter votre chiffre d'affaires...</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="author-info">
                            <img src="assets/images/admin_avatar.jpg" alt="Admin">
                            <span>Benjamin Tech</span>
                        </div>
                    </td>
                    <td><span class="badge badge-guide">Guide</span></td>
                    <td><span class="status-badge status-published">Publié</span></td>
                    <td>
                        <div class="views-info">
                            <i class="fas fa-eye"></i> 12.4K
                        </div>
                    </td>
                    <td>2024-03-15</td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn view-btn" title="Voir">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn edit-btn" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td><input type="checkbox" class="article-checkbox"></td>
                    <td>
                        <div class="article-preview">
                            <img src="assets/images/article2.jpg" alt="Article">
                            <div class="preview-info">
                                <h4>Nouvelles tendances e-commerce 2024</h4>
                                <p>Découvrez les tendances à suivre cette année...</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="author-info">
                            <img src="assets/images/admin_avatar.jpg" alt="Admin">
                            <span>Benjamin Tech</span>
                        </div>
                    </td>
                    <td><span class="badge badge-news">Actualités</span></td>
                    <td><span class="status-badge status-published">Publié</span></td>
                    <td>
                        <div class="views-info">
                            <i class="fas fa-eye"></i> 8.7K
                        </div>
                    </td>
                    <td>2024-03-10</td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn view-btn" title="Voir">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn edit-btn" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td><input type="checkbox" class="article-checkbox"></td>
                    <td>
                        <div class="article-preview">
                            <img src="assets/images/article3.jpg" alt="Article">
                            <div class="preview-info">
                                <h4>Optimisez vos photos produits</h4>
                                <p>Conseils pour des photos qui vendent...</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="author-info">
                            <img src="assets/images/admin_avatar.jpg" alt="Admin">
                            <span>Benjamin Tech</span>
                        </div>
                    </td>
                    <td><span class="badge badge-tips">Conseils</span></td>
                    <td><span class="status-badge status-draft">Brouillon</span></td>
                    <td>
                        <div class="views-info">
                            <i class="fas fa-eye"></i> 0
                        </div>
                    </td>
                    <td>-</td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn edit-btn" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn publish-btn" title="Publier">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td><input type="checkbox" class="article-checkbox"></td>
                    <td>
                        <div class="article-preview">
                            <img src="assets/images/article4.jpg" alt="Article">
                            <div class="preview-info">
                                <h4>Promotion spéciale Printemps</h4>
                                <p>Profitez de -30% sur tous nos services...</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="author-info">
                            <img src="assets/images/admin_avatar.jpg" alt="Admin">
                            <span>Benjamin Tech</span>
                        </div>
                    </td>
                    <td><span class="badge badge-promo">Promotions</span></td>
                    <td><span class="status-badge status-scheduled">Programmé</span></td>
                    <td>
                        <div class="views-info">
                            <i class="fas fa-eye"></i> 0
                        </div>
                    </td>
                    <td>2024-03-20</td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn edit-btn" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn cancel-btn" title="Annuler">
                                <i class="fas fa-times"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Supprimer">
                                <i class="fas fa-trash"></i>
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
            Affichage de 1 à 10 sur 145 articles
        </div>
    </div>
</div>

<!-- Top Articles -->
<div class="top-articles-section">
    <h3><i class="fas fa-chart-line"></i> Top articles du mois</h3>
    <div class="top-articles-grid">
        <div class="top-article-item">
            <div class="article-rank">1</div>
            <div class="article-thumbnail">
                <img src="assets/images/article1.jpg" alt="Article">
            </div>
            <div class="article-content">
                <h4>Comment booster vos ventes en ligne</h4>
                <div class="article-stats">
                    <span><i class="fas fa-eye"></i> 12.4K vues</span>
                    <span><i class="fas fa-heart"></i> 245 likes</span>
                    <span><i class="fas fa-comment"></i> 89 commentaires</span>
                </div>
            </div>
        </div>
        
        <div class="top-article-item">
            <div class="article-rank">2</div>
            <div class="article-thumbnail">
                <img src="assets/images/article2.jpg" alt="Article">
            </div>
            <div class="article-content">
                <h4>Nouvelles tendances e-commerce 2024</h4>
                <div class="article-stats">
                    <span><i class="fas fa-eye"></i> 8.7K vues</span>
                    <span><i class="fas fa-heart"></i> 189 likes</span>
                    <span><i class="fas fa-comment"></i> 56 commentaires</span>
                </div>
            </div>
        </div>
        
        <div class="top-article-item">
            <div class="article-rank">3</div>
            <div class="article-thumbnail">
                <img src="assets/images/article5.jpg" alt="Article">
            </div>
            <div class="article-content">
                <h4>Gestion efficace du service client</h4>
                <div class="article-stats">
                    <span><i class="fas fa-eye"></i> 6.2K vues</span>
                    <span><i class="fas fa-heart"></i> 145 likes</span>
                    <span><i class="fas fa-comment"></i> 42 commentaires</span>
                </div>
            </div>
        </div>
    </div>
</div>