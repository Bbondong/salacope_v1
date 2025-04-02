<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ../');
    exit;
}

$admin = $_SESSION['user'];
$name = $_SESSION['name'];
$tel = $_SESSION['username'];
$id_client = $_SESSION['user_id'] ;
$date_creation = $admin['created_at'];

// requette pour savoir nombre commande
$stmt = $bd->prepare("
    SELECT COUNT(*) AS nbr_commande
    FROM info_clients
    WHERE id_client = :u
");
$stmt->execute([':u' => $id_client]);

$nbr_commande = $stmt->fetch(PDO::FETCH_ASSOC)['nbr_commande'];

// Simuler les données utilisateur
$userData = [
    'name' => $name,
    'phone' => $tel,
    'member_since' => $date_creation,
    'total_orders' => $nbr_commande,
    'total_spent' => '1 845,50€'
];
?>

<div class="profile-page">
    <div class="profile-container">
        <!-- HEADER RESPONSIVE -->
        <header class="profile-header">
            <div class="profile-info">
                <div class="avatar-container">
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face" 
                         alt="Photo de profil" class="profile-avatar"
                         loading="lazy">
                    <button class="edit-avatar-btn" aria-label="Changer la photo de profil"
                            onclick="document.getElementById('avatarInput').click()">
                        <i class="fas fa-camera"></i>
                    </button>
                    <input type="file" id="avatarInput" accept="image/*" hidden onchange="previewAvatar(event)">
                </div>
                <div class="profile-details">
                    <h1><?php echo htmlspecialchars($userData['name']); ?></h1>
                    <div class="level-badge">
                        <i class="fas fa-star"></i>
                        Client Premium
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <span><?php echo htmlspecialchars($userData['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span><?php echo htmlspecialchars($userData['phone']); ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Membre depuis <?php echo date('d/m/Y', strtotime($userData['member_since'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- STATISTIQUES SIMPLIFIÉES -->
        <section class="stats-section">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-value"><?php echo $userData['total_orders']; ?></div>
                <div class="stat-label">Commandes</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-euro-sign"></i>
                </div>
                <div class="stat-value"><?php echo $userData['total_spent']; ?></div>
                <div class="stat-label">Total dépensé</div>
            </div>
        </section>

        <!-- NAVIGATION RESPONSIVE -->
        <nav class="profile-nav" role="tablist" aria-label="Navigation du profil">
            <button class="nav-btn active" role="tab" aria-selected="true" 
                    onclick="showSection('infos')" aria-controls="infos">
                <i class="fas fa-user"></i>
                <span>Mes Infos</span>
            </button>
            <button class="nav-btn" role="tab" aria-selected="false"
                    onclick="showSection('orders')" aria-controls="orders">
                <i class="fas fa-shopping-cart"></i>
                <span>Mes Achats</span>
            </button>
            <button class="nav-btn" role="tab" aria-selected="false"
                    onclick="showSection('addresses')" aria-controls="addresses">
                <i class="fas fa-home"></i>
                <span>Adresses</span>
            </button>
            <button class="nav-btn" role="tab" aria-selected="false"
                    onclick="showSection('settings')" aria-controls="settings">
                <i class="fas fa-cog"></i>
                <span>Paramètres</span>
            </button>
        </nav>

        <!-- SECTION INFORMATIONS -->
        <section id="infos" class="content-section active" role="tabpanel" aria-labelledby="infos-tab">
            <div class="section-header">
                <h2>
                    <i class="fas fa-user-circle"></i>
                    Informations Personnelles
                </h2>
                <button class="edit-btn" onclick="toggleEdit()">
                    <i class="fas fa-pen"></i>
                    <span>Modifier</span>
                </button>
            </div>
            
            <form id="profileForm" class="profile-form">
                <div class="form-group">
                    <label for="name">Nom complet</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="name" class="form-input" 
                               value="<?php echo htmlspecialchars($userData['name']); ?>" 
                               placeholder="Votre nom complet" disabled>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" class="form-input" 
                               value="<?php echo htmlspecialchars($userData['email']); ?>" 
                               placeholder="votre@email.com" disabled>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Téléphone</label>
                    <div class="input-with-icon">
                        <i class="fas fa-phone"></i>
                        <input type="tel" id="phone" class="form-input" 
                               value="<?php echo htmlspecialchars($userData['phone']); ?>" 
                               placeholder="+33 6 12 34 56 78" disabled>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="action-btn btn-secondary" onclick="cancelEdit()" style="display: none;">
                        Annuler
                    </button>
                    <button type="button" class="action-btn btn-primary" id="saveBtn" style="display: none;" onclick="saveProfile()">
                        Enregistrer
                    </button>
                </div>
            </form>
        </section>

        <!-- SECTION COMMANDES -->
        <section id="orders" class="content-section" role="tabpanel" aria-labelledby="orders-tab" hidden>
            <div class="section-header">
                <h2>
                    <i class="fas fa-history"></i>
                    Mes Achats
                </h2>
            </div>
            
            <div class="orders-list">
                <!-- Commande récente -->
                <article class="order-card">
                    <div class="order-header">
                        <span class="order-id">CMD-2024-00125</span>
                        <span class="order-date">
                            <i class="far fa-calendar"></i>
                            15 jan. 2024
                        </span>
                        <span class="order-status status-delivered">
                            <i class="fas fa-check"></i>
                            Livrée
                        </span>
                    </div>
                    <div class="order-products">
                        <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=200&h=200&fit=crop" 
                             alt="Casque audio" class="product-thumb" loading="lazy">
                        <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=200&h=200&fit=crop" 
                             alt="Montre" class="product-thumb" loading="lazy">
                        <img src="https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=200&h=200&fit=crop" 
                             alt="Smartphone" class="product-thumb" loading="lazy">
                    </div>
                    <div class="order-footer">
                        <div class="order-total">149,99€</div>
                        <div class="order-actions">
                            <button class="action-btn btn-secondary">
                                Détails
                            </button>
                            <button class="action-btn btn-primary">
                                Recommander
                            </button>
                        </div>
                    </div>
                </article>
                
                <!-- Commande en cours -->
                <article class="order-card">
                    <div class="order-header">
                        <span class="order-id">CMD-2024-00120</span>
                        <span class="order-date">
                            <i class="far fa-calendar"></i>
                            10 jan. 2024
                        </span>
                        <span class="order-status status-pending">
                            <i class="fas fa-clock"></i>
                            En cours
                        </span>
                    </div>
                    <div class="order-products">
                        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=200&h=200&fit=crop" 
                             alt="Chaussures" class="product-thumb" loading="lazy">
                    </div>
                    <div class="order-footer">
                        <div class="order-total">79,99€</div>
                        <div class="order-actions">
                            <button class="action-btn btn-secondary">
                                Suivre
                            </button>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <!-- SECTION ADRESSES -->
        <section id="addresses" class="content-section" role="tabpanel" aria-labelledby="addresses-tab" hidden>
            <div class="section-header">
                <h2>
                    <i class="fas fa-map-marker-alt"></i>
                    Mes Adresses
                </h2>
                <button class="edit-btn" onclick="addAddress()">
                    <i class="fas fa-plus"></i>
                    Ajouter
                </button>
            </div>
            
            <div class="addresses-grid">
                <div class="address-card default">
                    <span class="default-badge">Par défaut</span>
                    <h3>Domicile</h3>
                    <p>
                        123 Rue de la République<br>
                        75001 Paris<br>
                        France
                    </p>
                    <div class="address-actions">
                        <button class="action-btn btn-secondary">
                            Modifier
                        </button>
                    </div>
                </div>
                
                <div class="address-card">
                    <h3>Bureau</h3>
                    <p>
                        456 Avenue des Champs<br>
                        75008 Paris<br>
                        France
                    </p>
                    <div class="address-actions">
                        <button class="action-btn btn-secondary">
                            Modifier
                        </button>
                        <button class="action-btn btn-primary">
                            Définir par défaut
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION PARAMÈTRES -->
        <section id="settings" class="content-section" role="tabpanel" aria-labelledby="settings-tab" hidden>
            <div class="section-header">
                <h2>
                    <i class="fas fa-sliders-h"></i>
                    Paramètres
                </h2>
            </div>
            
            <div class="settings-list">
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Notifications email</h3>
                        <p>Promotions et actualités</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Notifications push</h3>
                        <p>Alertes de livraison</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                
                <div class="setting-item">
                    <div class="setting-info">
                        <h3>Mode sombre auto</h3>
                        <p>Adapter au système</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            
            <div class="logout-section">
                <button class="logout-btn" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i>
                    Déconnexion
                </button>
            </div>
        </section>
    </div>
</div>

<script>
// Gestion responsive des sections
function showSection(sectionId) {
    // Masquer toutes les sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
        section.setAttribute('hidden', 'true');
        section.classList.remove('active');
    });
    
    // Désactiver tous les boutons
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.setAttribute('aria-selected', 'false');
    });
    
    // Activer la section et le bouton
    const section = document.getElementById(sectionId);
    const btn = event.target;
    
    section.style.display = 'block';
    section.removeAttribute('hidden');
    section.classList.add('active');
    btn.classList.add('active');
    btn.setAttribute('aria-selected', 'true');
    
    // Pour mobile, fermer le clavier si ouvert
    if (window.innerWidth < 768) {
        document.activeElement.blur();
    }
}

// Édition du profil
let isEditing = false;

function toggleEdit() {
    isEditing = !isEditing;
    const inputs = document.querySelectorAll('#profileForm input');
    const cancelBtn = document.querySelector('.action-btn.btn-secondary');
    const saveBtn = document.getElementById('saveBtn');
    const editBtn = document.querySelector('.edit-btn');
    
    inputs.forEach(input => {
        input.disabled = !isEditing;
        if (isEditing && input.type === 'password') {
            input.value = '';
        }
    });
    
    if (isEditing) {
        editBtn.innerHTML = '<i class="fas fa-times"></i><span>Annuler</span>';
        cancelBtn.style.display = 'flex';
        saveBtn.style.display = 'flex';
        
        // Focus sur le premier champ
        inputs[0].focus();
    } else {
        editBtn.innerHTML = '<i class="fas fa-pen"></i><span>Modifier</span>';
        cancelBtn.style.display = 'none';
        saveBtn.style.display = 'none';
    }
}

function cancelEdit() {
    toggleEdit();
    document.getElementById('profileForm').reset();
}

function saveProfile() {
    const form = document.getElementById('profileForm');
    const formData = new FormData(form);
    
    // Simulation de sauvegarde
    setTimeout(() => {
        alert('✅ Modifications enregistrées');
        toggleEdit();
    }, 800);
}

// Gestion avatar
function previewAvatar(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Vérifier la taille
    if (file.size > 5 * 1024 * 1024) {
        alert('Image trop volumineuse (max 5MB)');
        return;
    }
    
    // Vérifier le type
    if (!file.type.startsWith('image/')) {
        alert('Veuillez sélectionner une image');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        document.querySelector('.profile-avatar').src = e.target.result;
        
        // Ici, envoi au serveur
        setTimeout(() => {
            alert('✅ Photo mise à jour');
        }, 500);
    };
    
    reader.readAsDataURL(file);
}

// Ajout d'adresse
function addAddress() {
    const address = prompt('Entrez votre nouvelle adresse :\nFormat: Nom, Rue, Code postal Ville, Pays');
    if (address) {
        alert('✅ Adresse ajoutée');
        // Ici, AJAX pour sauvegarder
    }
}

// Déconnexion
function logout() {
    if (confirm('Se déconnecter ?')) {
        // Redirection vers login
        window.location.href = '?page=logout';
    }
}

// Initialisation responsive
function initResponsive() {
    // Détecter la taille d'écran
    const screenWidth = window.innerWidth;
    
    // Adapter le layout pour mobile
    if (screenWidth < 768) {
        // Simplifier les interactions sur mobile
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });
    }
    
    // Gérer l'orientation paysage
    const isLandscape = window.innerWidth > window.innerHeight;
    if (isLandscape && screenWidth < 768) {
        document.body.classList.add('landscape');
    } else {
        document.body.classList.remove('landscape');
    }
}

// Événements
document.addEventListener('DOMContentLoaded', initResponsive);
window.addEventListener('resize', initResponsive);
window.addEventListener('orientationchange', function() {
    setTimeout(initResponsive, 100);
});

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && isEditing) {
        cancelEdit();
    }
});
</script>

