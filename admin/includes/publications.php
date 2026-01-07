<?php
// admin/includes/publications.php
?>

<!-- Page Header -->
<div class="page-header">
    <h1>Gestion des publications</h1>
    <div class="header-actions">
        <button class="btn btn-primary" id="newPublicationBtn">
            <i class="fas fa-plus"></i> Nouvelle publication
        </button>
        <div class="publication-filters">
            <select id="filterStatus">
                <option value="all">Tous les statuts</option>
                <option value="active">Actives</option>
                <option value="pending">En attente</option>
                <option value="rejected">Rejetées</option>
            </select>
            <select id="filterType">
                <option value="all">Tous les types</option>
                <option value="product">Produits</option>
                <option value="service">Services</option>
                <option value="announcement">Annonces</option>
            </select>
        </div>
    </div>
</div>

<!-- Publications Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <h3>Publications totales</h3>
            <i class="fas fa-bullhorn"></i>
        </div>
        <div class="stat-value">1,245</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 12% ce mois
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Publications actives</h3>
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-value">1,089</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 8% ce mois
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Signalements</h3>
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-value">45</div>
        <div class="stat-change negative">
            <i class="fas fa-arrow-down"></i> 3% ce mois
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <h3>Engagement moyen</h3>
            <i class="fas fa-heart"></i>
        </div>
        <div class="stat-value">245</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-up"></i> 15% ce mois
        </div>
    </div>
</div>

<!-- Publications Table -->
<div class="publications-table-container">
    <div class="table-header">
        <div class="table-actions">
            <button class="btn btn-secondary btn-sm" id="bulkApproveBtn">
                <i class="fas fa-check"></i> Approuver
            </button>
            <button class="btn btn-secondary btn-sm" id="bulkRejectBtn">
                <i class="fas fa-times"></i> Rejeter
            </button>
            <button class="btn btn-secondary btn-sm" id="bulkDeleteBtn">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
        <div class="table-search">
            <input type="text" placeholder="Rechercher une publication...">
            <i class="fas fa-search"></i>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="publications-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>Publication</th>
                    <th>Auteur</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Engagement</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="checkbox" class="publication-checkbox"></td>
                    <td>
                        <div class="publication-preview">
                            <img src="assets/images/product1.jpg" alt="Produit">
                            <div class="preview-info">
                                <h4>Smartphone XYZ - Nouvelle arrivée</h4>
                                <p>Caméra 108MP, charge rapide...</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="author-info">
                            <img src="assets/images/seller1.jpg" alt="TechGadgets">
                            <span>TechGadgets</span>
                        </div>
                    </td>
                    <td><span class="badge badge-product">Produit</span></td>
                    <td><span class="status-badge status-active">Active</span></td>
                    <td>
                        <div class="engagement-stats">
                            <span><i class="fas fa-eye"></i> 1.2K</span>
                            <span><i class="fas fa-heart"></i> 245</span>
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
                    <td><input type="checkbox" class="publication-checkbox"></td>
                    <td>
                        <div class="publication-preview">
                            <img src="assets/images/product2.jpg" alt="Produit">
                            <div class="preview-info">
                                <h4>Collection été 2024</h4>
                                <p>Robes légères et accessoires...</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="author-info">
                            <img src="assets/images/seller2.jpg" alt="FashionHub">
                            <span>FashionHub</span>
                        </div>
                    </td>
                    <td><span class="badge badge-product">Produit</span></td>
                    <td><span class="status-badge status-active">Active</span></td>
                    <td>
                        <div class="engagement-stats">
                            <span><i class="fas fa-eye"></i> 2.4K</span>
                            <span><i class="fas fa-heart"></i> 512</span>
                        </div>
                    </td>
                    <td>2024-03-14</td>
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
                    <td><input type="checkbox" class="publication-checkbox"></td>
                    <td>
                        <div class="publication-preview">
                            <img src="assets/images/service1.jpg" alt="Service">
                            <div class="preview-info">
                                <h4>Maintenance informatique</h4>
                                <p>Réparation et entretien d'ordinateurs...</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="author-info">
                            <img src="assets/images/seller3.jpg" alt="TechSupport">
                            <span>TechSupport</span>
                        </div>
                    </td>
                    <td><span class="badge badge-service">Service</span></td>
                    <td><span class="status-badge status-pending">En attente</span></td>
                    <td>
                        <div class="engagement-stats">
                            <span><i class="fas fa-eye"></i> 450</span>
                            <span><i class="fas fa-heart"></i> 89</span>
                        </div>
                    </td>
                    <td>2024-03-14</td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn approve-btn" title="Approuver">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="action-btn reject-btn" title="Rejeter">
                                <i class="fas fa-times"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td><input type="checkbox" class="publication-checkbox"></td>
                    <td>
                        <div class="publication-preview">
                            <img src="assets/images/product3.jpg" alt="Produit">
                            <div class="preview-info">
                                <h4>Meubles en bois massif</h4>
                                <p>Collection exclusive de meubles...</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="author-info">
                            <img src="assets/images/seller4.jpg" alt="WoodCraft">
                            <span>WoodCraft</span>
                        </div>
                    </td>
                    <td><span class="badge badge-product">Produit</span></td>
                    <td><span class="status-badge status-reported">Signalé</span></td>
                    <td>
                        <div class="engagement-stats">
                            <span><i class="fas fa-eye"></i> 890</span>
                            <span><i class="fas fa-heart"></i> 156</span>
                        </div>
                    </td>
                    <td>2024-03-13</td>
                    <td>
                        <div class="table-actions">
                            <button class="action-btn view-btn" title="Voir">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn warn-btn" title="Avertir">
                                <i class="fas fa-exclamation-triangle"></i>
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
            Affichage de 1 à 10 sur 1,245 publications
        </div>
    </div>
</div>