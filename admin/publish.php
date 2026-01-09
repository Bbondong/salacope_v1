<h2>Publier un nouveau produit</h2>
<p class="page-description">Remplissez le formulaire ci-dessous pour ajouter un nouveau produit à votre boutique.</p>

<div class="publish-form">
    <form action="#" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="product-name" class="form-label">Nom de la marchandise</label>
            <input type="text" id="product-name" class="form-control" placeholder="Ex: iPhone 14 Pro Max" required>
        </div>
        
        <div class="form-group">
            <label for="product-type" class="form-label">Type de produit</label>
            <select id="product-type" class="form-control" required>
                <option value="">Sélectionnez un type</option>
                <option value="electronics">Électronique</option>
                <option value="fashion">Mode & Vêtements</option>
                <option value="home">Maison & Jardin</option>
                <option value="sports">Sports & Loisirs</option>
                <option value="vehicles">Véhicules</option>
                <option value="real-estate">Immobilier</option>
                <option value="services">Services</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="product-price" class="form-label">Prix (€)</label>
            <input type="number" id="product-price" class="form-control" placeholder="Ex: 999.99" step="0.01" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Images du produit (3 maximum)</label>
            <div class="image-upload">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Cliquez pour téléverser des images</p>
                <small>Formats acceptés: JPG, PNG, GIF (Max 5MB)</small>
            </div>
            <input type="file" id="product-images" accept="image/*" multiple style="display: none;">
        </div>
        
        <div class="form-group">
            <label for="product-description" class="form-label">Description détaillée</label>
            <textarea id="product-description" class="form-control" placeholder="Décrivez votre produit en détail..." rows="6" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="product-stock" class="form-label">Quantité en stock</label>
            <input type="number" id="product-stock" class="form-control" placeholder="Ex: 50" min="0" value="1">
        </div>
        
        <div class="form-actions">
            <button type="reset" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Annuler
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check"></i>
                Publier le produit
            </button>
        </div>
    </form>
</div>