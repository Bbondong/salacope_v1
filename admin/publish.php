<h2>Publier un nouveau produit</h2>
<p class="page-description">Remplissez le formulaire ci-dessous pour ajouter un nouveau produit à votre boutique.</p>

<div class="publish-form">
    <form id="publishProductForm" method="POST" enctype="multipart/form-data">
        <!-- Informations de base -->
        <div class="form-group">
            <label for="product-name" class="form-label">Nom du produit *</label>
            <input type="text" id="product-name" name="product_name" class="form-control" placeholder="Ex: iPhone 14 Pro Max" required>
        </div>
        
        <!-- Catégorie principale -->
        <div class="form-group">
            <label for="main-category" class="form-label">Catégorie principale *</label>
            <select id="main-category" name="main_category" class="form-control" required>
                <option value="">Sélectionnez une catégorie</option>
                <option value="1">Électronique</option>
                <option value="8">Mode & Vêtements</option>
                <option value="15">Maison & Jardin</option>
                <option value="22">Sports & Loisirs</option>
                <option value="29">Véhicules</option>
                <option value="35">Immobilier</option>
                <option value="42">Services</option>
                <option value="49">Autres</option>
            </select>
        </div>
        
        <!-- Sous-catégorie -->
        <div class="form-group" id="subcategory-group" style="display: none;">
            <label for="sub-category" class="form-label">Sous-catégorie *</label>
            <select id="sub-category" name="category_id" class="form-control" required>
                <option value="">Sélectionnez une sous-catégorie</option>
            </select>
        </div>
        
        <!-- Prix et état -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="product-price" class="form-label">Prix (€) *</label>
                    <input type="number" id="product-price" name="price" class="form-control" placeholder="Ex: 999.99" step="0.01" min="0" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="product-condition" class="form-label">État du produit *</label>
                    <select id="product-condition" name="product_condition" class="form-control" required>
                        <option value="new">Neuf</option>
                        <option value="used">Occasion</option>
                        <option value="refurbished">Reconditionné</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Marque et modèle -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="product-brand" class="form-label">Marque</label>
                    <input type="text" id="product-brand" name="brand" class="form-control" placeholder="Ex: Apple, Samsung, Nike">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="product-model" class="form-label">Modèle</label>
                    <input type="text" id="product-model" name="model" class="form-control" placeholder="Ex: iPhone 14 Pro, Galaxy S23">
                </div>
            </div>
        </div>
        
        <!-- Images -->
        <div class="form-group">
            <label class="form-label">Images du produit * (3 maximum)</label>
            <div class="image-upload" id="image-upload-area">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Cliquez pour téléverser des images</p>
                <small>Formats acceptés: JPG, PNG, GIF (Max 5MB par image)</small>
            </div>
            <input type="file" id="product-images" name="product_images[]" accept="image/*" multiple style="display: none;">
            <div id="image-preview" class="image-preview-container"></div>
            <small id="image-error" class="text-danger" style="display: none;"></small>
        </div>
        
        <!-- Description -->
        <div class="form-group">
            <label for="product-description" class="form-label">Description détaillée *</label>
            <textarea id="product-description" name="description" class="form-control" placeholder="Décrivez votre produit en détail..." rows="6" required></textarea>
        </div>
        
        <!-- Stock et négociation -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="product-stock" class="form-label">Quantité en stock *</label>
                    <input type="number" id="product-stock" name="stock_quantity" class="form-control" placeholder="Ex: 50" min="1" value="1" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Prix négociable</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" id="is-negotiable" name="is_negotiable" class="form-check-input" value="1">
                        <label for="is-negotiable" class="form-check-label">Le prix est négociable</label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Localisation -->
        <div class="form-group">
            <label for="product-city" class="form-label">Ville *</label>
            <input type="text" id="product-city" name="city" class="form-control" placeholder="Ex: Paris, Lyon, Marseille" required>
        </div>
        
        <div class="form-group">
            <label for="product-address" class="form-label">Adresse (optionnel)</label>
            <input type="text" id="product-address" name="address" class="form-control" placeholder="Adresse précise pour la récupération">
        </div>
        
        <!-- Livraison -->
        <div class="form-group">
            <div class="form-check mb-2">
                <input type="checkbox" id="delivery-available" name="delivery_available" class="form-check-input" value="1">
                <label for="delivery-available" class="form-check-label">Je propose la livraison</label>
            </div>
            <div id="delivery-cost-group" style="display: none;">
                <label for="delivery-cost" class="form-label">Frais de livraison (€)</label>
                <input type="number" id="delivery-cost" name="delivery_cost" class="form-control" placeholder="0.00" step="0.01" min="0" value="0">
            </div>
        </div>
        
        <!-- Garantie -->
        <div class="form-group">
            <label for="warranty-months" class="form-label">Garantie (mois)</label>
            <input type="number" id="warranty-months" name="warranty_months" class="form-control" placeholder="0" min="0" value="0">
            <small class="text-muted">Laisser 0 si pas de garantie</small>
        </div>
        
        <!-- Boutons -->
        <div class="form-actions">
            <button type="reset" class="btn btn-secondary" id="cancelBtn">
                <i class="fas fa-times"></i>
                Annuler
            </button>
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-check"></i>
                Publier le produit
            </button>
        </div>
        
        <!-- Messages -->
        <div id="message" class="mt-3" style="display: none;"></div>
    </form>
</div>

<!-- Script pour la gestion des sous-catégories et images -->
<script src="./js/publish-product.js"></script>

<style>
.image-upload {
    border: 2px dashed #ccc;
    border-radius: 10px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    background: #f8f9fa;
}

.image-upload:hover {
    border-color: #007bff;
    background: #e9f7fe;
}

.image-preview-container {
    margin-top: 15px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.image-preview-item {
    position: relative;
    width: 100px;
    height: 100px;
    border: 1px solid #ddd;
    border-radius: 5px;
    overflow: hidden;
}

.image-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-preview-item button {
    position: absolute;
    top: 5px;
    right: 5px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.alert {
    padding: 12px 15px;
    border-radius: 5px;
    margin-bottom: 15px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>