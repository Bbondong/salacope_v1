// Configuration
const MAX_IMAGES = 3;
const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

// Données des sous-catégories
const subcategoriesData = {
    '1': [ // Électronique
        {id: 2, name: 'Téléphones & Tablettes'},
        {id: 3, name: 'Ordinateurs & Accessoires'},
        {id: 4, name: 'Télévisions & Vidéo'},
        {id: 5, name: 'Audio & Hi-Fi'},
        {id: 6, name: 'Jeux vidéo & Consoles'},
        {id: 7, name: 'Appareils photo & Caméras'}
    ],
    '8': [ // Mode & Vêtements
        {id: 9, name: 'Vêtements Homme'},
        {id: 10, name: 'Vêtements Femme'},
        {id: 11, name: 'Vêtements Enfant'},
        {id: 12, name: 'Chaussures'},
        {id: 13, name: 'Accessoires de mode'},
        {id: 14, name: 'Bijoux & Montres'}
    ],
    '15': [ // Maison & Jardin
        {id: 16, name: 'Meubles'},
        {id: 17, name: 'Électroménager'},
        {id: 18, name: 'Décoration'},
        {id: 19, name: 'Jardin & Extérieur'},
        {id: 20, name: 'Bricolage & Outillage'},
        {id: 21, name: 'Cuisine & Arts de la table'}
    ],
    '22': [ // Sports & Loisirs
        {id: 23, name: 'Équipement sportif'},
        {id: 24, name: 'Vélos'},
        {id: 25, name: 'Camping & Randonnée'},
        {id: 26, name: 'Fitness & Musculation'},
        {id: 27, name: 'Pêche & Chasse'},
        {id: 28, name: 'Instruments de musique'}
    ],
    '29': [ // Véhicules
        {id: 30, name: 'Voitures'},
        {id: 31, name: 'Motos & Scooters'},
        {id: 32, name: 'Véhicules utilitaires'},
        {id: 33, name: 'Pièces auto & Accessoires'},
        {id: 34, name: 'Bateaux & Nautisme'}
    ],
    '35': [ // Immobilier
        {id: 36, name: 'Appartements à louer'},
        {id: 37, name: 'Appartements à vendre'},
        {id: 38, name: 'Maisons à louer'},
        {id: 39, name: 'Maisons à vendre'},
        {id: 40, name: 'Terrains'},
        {id: 41, name: 'Bureaux & Commerces'}
    ],
    '42': [ // Services
        {id: 43, name: 'Cours particuliers'},
        {id: 44, name: 'Réparation & Dépannage'},
        {id: 45, name: 'Nettoyage & Ménage'},
        {id: 46, name: 'Garde d\'enfants'},
        {id: 47, name: 'Jardinage & Bricolage'},
        {id: 48, name: 'Covoiturage & Transport'}
    ],
    '49': [ // Autres
        {id: 50, name: 'Livres & Papeterie'},
        {id: 51, name: 'Animaux'},
        {id: 52, name: 'Matériel professionnel'},
        {id: 53, name: 'Collection & Art'},
        {id: 54, name: 'Alimentation & Boissons'}
    ]
};

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Élément DOM
    const mainCategory = document.getElementById('main-category');
    const imageUploadArea = document.getElementById('image-upload-area');
    const imageInput = document.getElementById('product-images');
    const deliveryCheckbox = document.getElementById('delivery-available');
    const cancelBtn = document.getElementById('cancelBtn');
    const form = document.getElementById('publishProductForm');
    
    // Charger les sous-catégories
    if (mainCategory) {
        mainCategory.addEventListener('change', loadSubcategories);
    }
    
    // Gestion du click sur la zone d'upload
    if (imageUploadArea && imageInput) {
        imageUploadArea.addEventListener('click', () => imageInput.click());
        imageInput.addEventListener('change', previewImages);
    }
    
    // Gestion de la livraison
    if (deliveryCheckbox) {
        deliveryCheckbox.addEventListener('change', toggleDeliveryCost);
        // Initialiser l'état
        toggleDeliveryCost();
    }
    
    // Bouton annuler
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            if (confirm('Voulez-vous vraiment annuler ? Les données saisies seront perdues.')) {
                window.location.href = '../admin/dashboard.php';
            }
        });
    }
    
    // Soumission du formulaire
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
});

// Charger les sous-catégories
function loadSubcategories() {
    const mainCategory = document.getElementById('main-category');
    const subcategoryGroup = document.getElementById('subcategory-group');
    const subcategorySelect = document.getElementById('sub-category');
    
    if (!mainCategory || !subcategoryGroup || !subcategorySelect) return;
    
    const selectedValue = mainCategory.value;
    
    if (!selectedValue) {
        subcategoryGroup.style.display = 'none';
        subcategorySelect.innerHTML = '<option value="">Sélectionnez une sous-catégorie</option>';
        subcategorySelect.required = false;
        return;
    }
    
    subcategorySelect.innerHTML = '<option value="">Sélectionnez une sous-catégorie</option>';
    
    if (subcategoriesData[selectedValue]) {
        subcategoriesData[selectedValue].forEach(sub => {
            const option = document.createElement('option');
            option.value = sub.id;
            option.textContent = sub.name;
            subcategorySelect.appendChild(option);
        });
        subcategoryGroup.style.display = 'block';
        subcategorySelect.required = true;
    } else {
        subcategoryGroup.style.display = 'none';
        subcategorySelect.required = false;
    }
}

// Aperçu des images
function previewImages(event) {
    const input = event.target;
    const preview = document.getElementById('image-preview');
    const errorMsg = document.getElementById('image-error');
    
    if (!preview || !errorMsg) return;
    
    preview.innerHTML = '';
    errorMsg.style.display = 'none';
    
    const files = Array.from(input.files);
    
    // Vérifier le nombre d'images
    if (files.length > MAX_IMAGES) {
        errorMsg.textContent = `Maximum ${MAX_IMAGES} images autorisées`;
        errorMsg.style.display = 'block';
        input.value = '';
        return;
    }
    
    // Vérifier chaque fichier
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        // Vérifier la taille
        if (file.size > MAX_FILE_SIZE) {
            errorMsg.textContent = `L'image "${file.name}" dépasse 5MB`;
            errorMsg.style.display = 'block';
            input.value = '';
            return;
        }
        
        // Vérifier le type
        if (!ALLOWED_TYPES.includes(file.type)) {
            errorMsg.textContent = `Format non supporté pour "${file.name}"`;
            errorMsg.style.display = 'block';
            input.value = '';
            return;
        }
        
        // Créer l'aperçu
        const reader = new FileReader();
        reader.onload = function(e) {
            const container = document.createElement('div');
            container.className = 'image-preview-item';
            
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = `Préview ${i + 1}`;
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.innerHTML = '×';
            removeBtn.title = 'Supprimer cette image';
            removeBtn.onclick = function() {
                container.remove();
                updateFileInput(input, files, i);
            };
            
            container.appendChild(img);
            container.appendChild(removeBtn);
            preview.appendChild(container);
        };
        reader.readAsDataURL(file);
    }
}

// Mettre à jour l'input file après suppression
function updateFileInput(input, files, indexToRemove) {
    const newFiles = files.filter((_, index) => index !== indexToRemove);
    const dataTransfer = new DataTransfer();
    
    newFiles.forEach(file => {
        dataTransfer.items.add(file);
    });
    
    input.files = dataTransfer.files;
}

// Basculer l'affichage des frais de livraison
function toggleDeliveryCost() {
    const deliveryCheckbox = document.getElementById('delivery-available');
    const deliveryCostGroup = document.getElementById('delivery-cost-group');
    
    if (!deliveryCheckbox || !deliveryCostGroup) return;
    
    deliveryCostGroup.style.display = deliveryCheckbox.checked ? 'block' : 'none';
}

// Validation du formulaire
function validateForm() {
    const errors = [];
    
    // Vérifier les images
    const imageInput = document.getElementById('product-images');
    if (!imageInput || imageInput.files.length === 0) {
        errors.push('Veuillez sélectionner au moins une image');
    } else if (imageInput.files.length > MAX_IMAGES) {
        errors.push(`Maximum ${MAX_IMAGES} images autorisées`);
    }
    
    // Vérifier la sous-catégorie si catégorie principale sélectionnée
    const mainCategory = document.getElementById('main-category');
    const subCategory = document.getElementById('sub-category');
    
    if (mainCategory && mainCategory.value && (!subCategory || !subCategory.value)) {
        errors.push('Veuillez sélectionner une sous-catégorie');
    }
    
    // Vérifier le prix
    const price = document.getElementById('product-price');
    if (price && (price.value <= 0 || isNaN(price.value))) {
        errors.push('Le prix doit être supérieur à 0');
    }
    
    // Vérifier la quantité
    const stock = document.getElementById('product-stock');
    if (stock && (stock.value < 1 || isNaN(stock.value))) {
        errors.push('La quantité doit être au moins 1');
    }
    
    // Vérifier la ville
    const city = document.getElementById('product-city');
    if (city && !city.value.trim()) {
        errors.push('La ville est requise');
    }
    
    return errors;
}

// Afficher les messages
function showMessage(text, type) {
    const messageDiv = document.getElementById('message');
    if (!messageDiv) return;
    
    messageDiv.textContent = text;
    messageDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'}`;
    messageDiv.style.display = 'block';
    
    if (type === 'success') {
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 5000);
    }
}

// Soumission du formulaire
async function handleFormSubmit(event) {
    event.preventDefault();
    
    // Validation
    const errors = validateForm();
    if (errors.length > 0) {
        showMessage(errors.join(', '), 'error');
        return;
    }
    
    const submitBtn = document.getElementById('submitBtn');
    if (!submitBtn) return;
    
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publication...';
    
    try {
        const formData = new FormData(event.target);
        
        // URL du backend (chemin relatif depuis admin/)
        const response = await fetch('../../backend/traitement/publish_product.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage(data.message || 'Produit publié avec succès !', 'success');
            
            // Redirection après 2 secondes
            setTimeout(() => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else if (data.product_id) {
                    window.location.href = `../products.php?id=${data.product_id}`;
                } else {
                    window.location.href = '../admin/dashboard.php';
                }
            }, 2000);
        } else {
            showMessage(data.message || 'Erreur lors de la publication', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Erreur:', error);
        showMessage('Erreur réseau: ' + error.message, 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}