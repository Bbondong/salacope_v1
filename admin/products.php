<?php
session_start();
require_once '../backend/config.php';

// Nombre de produits par page
$perPage = 12;
?>

<div class="container">
    <div class="header">
        <h1>📦 Tous les produits</h1>
        <p>Découvrez les meilleures offres publiées par notre communauté</p>
    </div>

    <!-- Filtres -->
    <div class="filters-section">
        <div class="filters-grid">
            <div class="filter-group">
                <label for="search"><i class="fas fa-search"></i> Rechercher</label>
                <input type="text" id="search" class="search-input" placeholder="Nom du produit, marque...">
            </div>
            
            <div class="filter-group">
                <label for="category"><i class="fas fa-tags"></i> Catégorie</label>
                <select id="category" class="filter-select">
                    <option value="">Toutes les catégories</option>
                    <?php
                    // Récupérer les catégories
                    try {
                        $catStmt = $bd->prepare("SELECT category_id, name FROM categories WHERE parent_id IS NOT NULL ORDER BY name");
                        $catStmt->execute();
                        $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach ($categories as $cat) {
                            echo "<option value='{$cat['category_id']}'>{$cat['name']}</option>";
                        }
                    } catch (PDOException $e) {
                        echo "<option value=''>Erreur chargement</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="condition"><i class="fas fa-star"></i> État</label>
                <select id="condition" class="filter-select">
                    <option value="">Tous les états</option>
                    <option value="new">Neuf</option>
                    <option value="used">Occasion</option>
                    <option value="refurbished">Reconditionné</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="city"><i class="fas fa-map-marker-alt"></i> Ville</label>
                <input type="text" id="city" class="search-input" placeholder="Filtrer par ville">
            </div>
        </div>
    </div>

    <!-- Grille des produits -->
    <div id="products-container" class="products-grid">
        <!-- Les produits seront chargés ici par AJAX -->
    </div>

    <!-- Loading spinner -->
    <div id="loading-spinner" class="loading-spinner">
        <div class="spinner"></div>
        <p>Chargement des produits...</p>
    </div>

    <!-- Message de fin -->
    <div id="end-message" class="end-message">
        <p><i class="fas fa-check-circle"></i> Vous avez vu tous les produits !</p>
    </div>

    <!-- Modal des détails -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalProductTitle">Détails du produit</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body" id="productDetails">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>
</div>

<style>
    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    .header {
        text-align: center;
        margin-bottom: 40px;
        padding: 30px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .header h1 {
        color: #2c3e50;
        font-size: 2.5rem;
        margin-bottom: 10px;
    }

    .header p {
        color: #7f8c8d;
        font-size: 1.1rem;
    }

    /* Filtres */
    .filters-section {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-group label {
        margin-bottom: 8px;
        color: #2c3e50;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .filter-select, .search-input {
        padding: 12px 15px;
        border: 2px solid #e0e6ed;
        border-radius: 10px;
        font-size: 16px;
        background: white;
        transition: all 0.3s;
    }

    .search-input {
        width: 100%;
    }

    .filter-select:focus, .search-input:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    /* Grille des produits */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        border: 1px solid #f1f1f1;
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .product-img-container {
        height: 200px;
        overflow: hidden;
        position: relative;
    }

    .product-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .product-card:hover .product-img {
        transform: scale(1.05);
    }

    .product-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 2;
    }

    .badge-new { background: #2ecc71; color: white; }
    .badge-used { background: #e74c3c; color: white; }
    .badge-refurbished { background: #f39c12; color: white; }

    .product-info {
        padding: 20px;
    }

    .product-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-description {
        color: #5d6d7e;
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        font-size: 0.85rem;
        color: #7f8c8d;
    }

    .product-location {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .product-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #ecf0f1;
    }

    .product-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: #e74c3c;
    }

    .btn-action {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 1rem;
        color: white;
    }

    .btn-view {
        background: #3498db;
    }

    .btn-view:hover {
        background: #2980b9;
        transform: scale(1.1);
    }

    /* Loading spinner */
    .loading-spinner {
        text-align: center;
        padding: 30px;
        display: none;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 15px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .end-message {
        text-align: center;
        padding: 30px;
        color: #7f8c8d;
        display: none;
    }

    /* Modal - Version améliorée pour mobile */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.7);
        animation: fadeIn 0.3s;
        overflow-y: auto;
        padding: 10px;
        box-sizing: border-box;
    }

    .modal-content {
        background-color: white;
        margin: 20px auto;
        padding: 0;
        border-radius: 15px;
        width: 95%;
        max-width: 900px;
        min-height: auto;
        max-height: 95vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        animation: slideUp 0.4s;
        position: relative;
    }

    @keyframes slideUp {
        from { transform: translateY(50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        background: white;
        z-index: 2;
    }

    .modal-header h3 {
        color: #2c3e50;
        font-size: 1.5rem;
        margin: 0;
        padding-right: 20px;
        word-break: break-word;
    }

    .close-modal {
        font-size: 28px;
        cursor: pointer;
        color: #7f8c8d;
        background: none;
        border: none;
        transition: color 0.3s;
        flex-shrink: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .close-modal:hover {
        color: #e74c3c;
    }

    .modal-body {
        padding: 20px;
    }

    /* Styles pour le contenu du modal */
    .product-images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 10px;
        margin-bottom: 20px;
    }

    .product-image {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.3s;
    }

    .product-image:hover {
        transform: scale(1.05);
    }

    .product-details-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
        margin-bottom: 20px;
    }

    .product-features {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .product-features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px;
    }

    .seller-info {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-top: 20px;
    }

    .price-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #ddd;
    }

    .modal-price {
        color: #e74c3c;
        font-size: 1.8rem;
        margin-bottom: 15px;
        font-weight: bold;
    }

    .contact-btn {
        width: 100%;
        padding: 12px;
        background: #3498db;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1rem;
        transition: background 0.3s;
    }

    .contact-btn:hover {
        background: #2980b9;
    }

    /* Responsive amélioré */
    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
        }
        
        .container {
            padding: 15px;
        }
        
        .header {
            padding: 20px;
        }
        
        .header h1 {
            font-size: 2rem;
        }
        
        /* Modal responsive pour mobile */
        .modal-content {
            width: 100%;
            max-width: 100%;
            margin: 0;
            border-radius: 0;
            height: 100vh;
            max-height: 100vh;
        }
        
        .modal {
            padding: 0;
        }
        
        .modal-header {
            padding: 15px;
        }
        
        .modal-header h3 {
            font-size: 1.3rem;
        }
        
        .modal-body {
            padding: 15px;
        }
        
        .product-details-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .product-images-grid {
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        }
        
        .product-image {
            height: 100px;
        }
        
        .product-features-grid {
            grid-template-columns: 1fr;
        }
        
        .modal-price {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .products-grid {
            grid-template-columns: 1fr;
        }
        
        .filters-grid {
            grid-template-columns: 1fr;
        }
        
        .filter-select, .search-input {
            font-size: 14px;
            padding: 10px;
        }
        
        .product-images-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .product-image {
            height: 90px;
        }
        
        .modal-header h3 {
            font-size: 1.2rem;
        }
        
        .contact-btn {
            padding: 15px;
            font-size: 1.1rem;
        }
    }

    @media (max-width: 360px) {
        .product-images-grid {
            grid-template-columns: 1fr;
        }
        
        .product-image {
            height: 150px;
        }
    }

    /* Pour les très grands écrans */
    @media (min-width: 1400px) {
        .container {
            max-width: 1600px;
        }
        
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        }
    }
</style>

<script>
    // Configuration
    let currentPage = 1;
    let loading = false;
    let hasMore = true;
    let filters = {
        search: '',
        category: '',
        condition: '',
        city: ''
    };

    // Initialiser le chargement
    document.addEventListener('DOMContentLoaded', function() {
        loadProducts();
        setupEventListeners();
        setupInfiniteScroll();
    });

    // Configurer les écouteurs d'événements
    function setupEventListeners() {
        // Filtres
        document.getElementById('search').addEventListener('input', debounce(function(e) {
            filters.search = e.target.value;
            resetAndLoad();
        }, 500));

        document.getElementById('category').addEventListener('change', function(e) {
            filters.category = e.target.value;
            resetAndLoad();
        });

        document.getElementById('condition').addEventListener('change', function(e) {
            filters.condition = e.target.value;
            resetAndLoad();
        });

        document.getElementById('city').addEventListener('input', debounce(function(e) {
            filters.city = e.target.value;
            resetAndLoad();
        }, 500));

        // Modal
        const modal = document.getElementById('productModal');
        const closeButtons = document.querySelectorAll('.close-modal');
        
        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
        });

        // Fermer la modal en cliquant à l'extérieur
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });

        // Empêcher la fermeture en cliquant à l'intérieur du contenu
        modal.querySelector('.modal-content').addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Gérer les touches du clavier
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.style.display === 'block') {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }

    // Configurer l'infinite scroll
    function setupInfiniteScroll() {
        window.addEventListener('scroll', debounce(() => {
            const scrollPosition = window.innerHeight + window.pageYOffset;
            const pageHeight = document.documentElement.offsetHeight - 300;
            
            if (scrollPosition >= pageHeight && !loading && hasMore) {
                loadProducts();
            }
        }, 200));
    }

    // Réinitialiser et recharger
    function resetAndLoad() {
        currentPage = 1;
        hasMore = true;
        document.getElementById('products-container').innerHTML = '';
        document.getElementById('end-message').style.display = 'none';
        loadProducts();
    }

    // Charger les produits
    async function loadProducts() {
        if (loading || !hasMore) return;
        
        loading = true;
        document.getElementById('loading-spinner').style.display = 'block';
        
        try {
            const formData = new FormData();
            formData.append('page', currentPage);
            formData.append('per_page', <?php echo $perPage; ?>);
            formData.append('search', filters.search);
            formData.append('category', filters.category);
            formData.append('condition', filters.condition);
            formData.append('city', filters.city);
            
            const response = await fetch('../backend/traitement/get_products.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success && data.products.length > 0) {
                displayProducts(data.products);
                currentPage++;
                hasMore = data.has_more;
            } else {
                hasMore = false;
                if (currentPage === 1) {
                    document.getElementById('products-container').innerHTML = 
                        '<div style="text-align: center; padding: 50px; color: #7f8c8d; grid-column: 1/-1;">' +
                        '<i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 20px;"></i>' +
                        '<h3>Aucun produit trouvé</h3>' +
                        '<p>Essayez de modifier vos critères de recherche</p>' +
                        '</div>';
                }
            }
            
            // Afficher le message de fin si plus de produits
            if (!hasMore && currentPage > 1) {
                document.getElementById('end-message').style.display = 'block';
            }
            
        } catch (error) {
            console.error('Erreur:', error);
            showError('Erreur de chargement des produits');
        } finally {
            loading = false;
            document.getElementById('loading-spinner').style.display = 'none';
        }
    }

    // Afficher les produits
    function displayProducts(products) {
        const container = document.getElementById('products-container');
        
        products.forEach(product => {
            const productCard = createProductCard(product);
            container.appendChild(productCard);
        });
    }

    // Créer une carte produit
    function createProductCard(product) {
        const card = document.createElement('div');
        card.className = 'product-card';
        
        // Badge selon l'état
        let badgeClass = '';
        let badgeText = '';
        switch(product.product_condition) {
            case 'new': badgeClass = 'badge-new'; badgeText = 'Neuf'; break;
            case 'used': badgeClass = 'badge-used'; badgeText = 'Occasion'; break;
            case 'refurbished': badgeClass = 'badge-refurbished'; badgeText = 'Reconditionné'; break;
        }
        
        // Image par défaut si aucune image
        const imageUrl = product.main_image ? '../' + product.main_image : 'https://via.placeholder.com/300x200?text=No+Image';
        
        // Format du prix
        const price = parseFloat(product.price).toFixed(2).replace('.', ',') + ' €';
        
        card.innerHTML = `
            <div class="product-img-container">
                <img src="${imageUrl}" alt="${product.title}" class="product-img" loading="lazy">
                ${badgeText ? `<span class="product-badge ${badgeClass}">${badgeText}</span>` : ''}
            </div>
            <div class="product-info">
                <h3 class="product-title" title="${product.title}">${product.title}</h3>
                <p class="product-description">${product.description.substring(0, 100)}${product.description.length > 100 ? '...' : ''}</p>
                
                <div class="product-meta">
                    <span class="product-category">
                        <i class="fas fa-tag"></i> ${product.category_name || 'Non catégorisé'}
                    </span>
                    <span class="product-location">
                        <i class="fas fa-map-marker-alt"></i> ${product.city}
                    </span>
                </div>
                
                <div class="product-footer">
                    <div class="product-price">${price}</div>
                    <div class="product-actions">
                        <button class="btn-action btn-view" onclick="showProductDetails(${product.product_id})" title="Voir les détails">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        return card;
    }

    // Afficher les détails du produit
    async function showProductDetails(productId) {
        try {
            const formData = new FormData();
            formData.append('product_id', productId);
            
            const response = await fetch('../backend/traitement/get_product_details.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                const product = data.product;
                const modal = document.getElementById('productModal');
                const modalBody = document.getElementById('productDetails');
                
                // Construire le contenu de la modal
                let imagesHtml = '';
                if (data.images && data.images.length > 0) {
                    imagesHtml = `
                        <div class="product-images-grid">
                            ${data.images.map(img => 
                                `<img src="../${img.image_path}" alt="Image produit" class="product-image" loading="lazy">`
                            ).join('')}
                        </div>
                    `;
                } else {
                    imagesHtml = `
                        <div style="text-align: center; margin-bottom: 20px;">
                            <img src="https://via.placeholder.com/600x400?text=No+Image" 
                                 alt="Aucune image disponible" 
                                 style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 10px;">
                        </div>
                    `;
                }
                
                // Formater le prix
                const price = parseFloat(product.price).toFixed(2).replace('.', ',') + ' €';
                
                modalBody.innerHTML = `
                    ${imagesHtml}
                    <div class="product-details-grid">
                        <div>
                            <h4 style="color: #2c3e50; margin-bottom: 15px;">Description</h4>
                            <p style="color: #5d6d7e; line-height: 1.6; margin-bottom: 20px; white-space: pre-line;">${product.description}</p>
                            
                            <div class="product-features">
                                <h5 style="color: #2c3e50; margin-bottom: 10px;">Caractéristiques</h5>
                                <div class="product-features-grid">
                                    ${product.brand ? `<div><strong>Marque:</strong> ${product.brand}</div>` : ''}
                                    ${product.model ? `<div><strong>Modèle:</strong> ${product.model}</div>` : ''}
                                    <div><strong>État:</strong> ${getConditionText(product.product_condition)}</div>
                                    <div><strong>Quantité:</strong> ${product.stock_quantity}</div>
                                    ${product.warranty_months > 0 ? `<div><strong>Garantie:</strong> ${product.warranty_months} mois</div>` : ''}
                                </div>
                            </div>
                        </div>
                        
                        <div class="seller-info">
                            <h4 style="color: #2c3e50; margin-bottom: 15px;">Informations vendeur</h4>
                            <p style="margin-bottom: 10px;"><strong>Ville:</strong> ${product.city}</p>
                            ${product.address ? `<p style="margin-bottom: 10px;"><strong>Adresse:</strong> ${product.address}</p>` : ''}
                            ${product.delivery_available ? `<p style="margin-bottom: 10px;"><strong>Livraison:</strong> Oui (${product.delivery_cost} €)</p>` : ''}
                            ${product.is_negotiable ? `<p style="margin-bottom: 20px;"><strong>Prix négociable:</strong> Oui</p>` : ''}
                            
                            <div class="price-section">
                                <div class="modal-price">${price}</div>
                                <button class="contact-btn" onclick="contactSeller(${product.user_id})">
                                    <i class="fas fa-comment"></i> Contacter le vendeur
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('modalProductTitle').textContent = product.title;
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden'; // Empêcher le scroll du body
                
                // Ajouter des écouteurs pour les images
                setupImageZoom();
                
            } else {
                showErrorModal('Erreur: ' + data.message);
            }
        } catch (error) {
            console.error('Erreur:', error);
            showErrorModal('Erreur lors du chargement des détails');
        }
    }

    // Fonction pour contacter le vendeur
    function contactSeller(userId) {
        alert(`Fonction de contact pour l'utilisateur ${userId} - À implémenter`);
        // Ici vous pourriez rediriger vers une page de messagerie ou ouvrir un formulaire
    }

    // Setup pour le zoom des images
    function setupImageZoom() {
        const images = document.querySelectorAll('.product-image');
        images.forEach(img => {
            img.addEventListener('click', function() {
                // Créer une modal pour voir l'image en grand
                const modal = document.createElement('div');
                modal.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.9);
                    z-index: 2000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    animation: fadeIn 0.3s;
                `;
                
                const imgLarge = document.createElement('img');
                imgLarge.src = this.src;
                imgLarge.style.cssText = `
                    max-width: 90%;
                    max-height: 90%;
                    object-fit: contain;
                    border-radius: 5px;
                `;
                
                modal.appendChild(imgLarge);
                modal.addEventListener('click', () => modal.remove());
                document.body.appendChild(modal);
            });
        });
    }

    // Helper functions
    function getConditionText(condition) {
        switch(condition) {
            case 'new': return 'Neuf';
            case 'used': return 'Occasion';
            case 'refurbished': return 'Reconditionné';
            default: return condition;
        }
    }

    function showErrorModal(message) {
        const modal = document.getElementById('productModal');
        const modalBody = document.getElementById('productDetails');
        
        modalBody.innerHTML = `
            <div style="text-align: center; padding: 40px 20px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #e74c3c; margin-bottom: 20px;"></i>
                <h3 style="color: #2c3e50; margin-bottom: 15px;">Erreur</h3>
                <p style="color: #7f8c8d; margin-bottom: 25px;">${message}</p>
                <button onclick="document.getElementById('productModal').style.display='none'; document.body.style.overflow='auto';" 
                        style="padding: 10px 25px; background: #3498db; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem;">
                    Fermer
                </button>
            </div>
        `;
        
        document.getElementById('modalProductTitle').textContent = 'Erreur';
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function showError(message) {
        const container = document.getElementById('products-container');
        container.innerHTML = `
            <div style="text-align: center; padding: 50px; color: #e74c3c; grid-column: 1/-1;">
                <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 20px;"></i>
                <h3>Erreur</h3>
                <p>${message}</p>
                <button onclick="resetAndLoad()" style="margin-top: 20px; padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Réessayer
                </button>
            </div>
        `;
    }
</script>