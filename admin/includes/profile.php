<?php
// admin/includes/profile.php
?>

<!-- Page Header -->
<div class="page-header">
    <h1>Mon profil</h1>
    <div class="header-actions">
        <button class="btn btn-secondary" id="editProfileBtn">
            <i class="fas fa-edit"></i> Modifier le profil
        </button>
        <button class="btn btn-primary" id="saveProfileBtn" style="display: none;">
            <i class="fas fa-save"></i> Enregistrer
        </button>
        <button class="btn btn-secondary" id="cancelEditBtn" style="display: none;">
            <i class="fas fa-times"></i> Annuler
        </button>
    </div>
</div>

<div class="profile-container">
    <div class="profile-sidebar">
        <div class="profile-avatar-section">
            <div class="avatar-container">
                <img src="<?php echo htmlspecialchars($admin_avatar); ?>" alt="Admin" id="profileAvatar">
                <div class="avatar-overlay" id="avatarOverlay">
                    <i class="fas fa-camera"></i>
                    <span>Changer la photo</span>
                </div>
                <input type="file" id="avatarInput" accept="image/*" style="display: none;">
            </div>
            <h2><?php echo htmlspecialchars($admin_name); ?></h2>
            <p class="profile-role"><?php echo htmlspecialchars($admin_role); ?></p>
            <div class="profile-stats">
                <div class="stat-item">
                    <i class="fas fa-calendar-alt"></i>
                    <div>
                        <span class="stat-label">Membre depuis</span>
                        <span class="stat-value">15 Mars 2023</span>
                    </div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <span class="stat-label">Dernière connexion</span>
                        <span class="stat-value">Aujourd'hui, 14:30</span>
                    </div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <span class="stat-label">Niveau de sécurité</span>
                        <span class="stat-value">Élevé</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="profile-menu">
            <a href="#info" class="profile-menu-item active" data-section="info">
                <i class="fas fa-user"></i>
                <span>Informations personnelles</span>
            </a>
            <a href="#security" class="profile-menu-item" data-section="security">
                <i class="fas fa-lock"></i>
                <span>Sécurité</span>
            </a>
            <a href="#notifications" class="profile-menu-item" data-section="notifications">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
            </a>
            <a href="#preferences" class="profile-menu-item" data-section="preferences">
                <i class="fas fa-cog"></i>
                <span>Préférences</span>
            </a>
        </div>
    </div>
    
    <div class="profile-content">
        <!-- Section Informations -->
        <div class="profile-section active" id="infoSection">
            <h3><i class="fas fa-user"></i> Informations personnelles</h3>
            <form id="profileForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">Nom</label>
                        <input type="text" id="firstName" value="Benjamin" readonly>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Prénom</label>
                        <input type="text" id="lastName" value="Tech" readonly>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" value="benjamin@salacope.com" readonly>
                </div>
                
                <div class="form-group">
                    <label for="phone">Téléphone</label>
                    <input type="tel" id="phone" value="+243 81 234 5678" readonly>
                </div>
                
                <div class="form-group">
                    <label for="address">Adresse</label>
                    <textarea id="address" rows="3" readonly>123 Avenue du Commerce, Kinshasa, RDC</textarea>
                </div>
                
                <div class="form-group">
                    <label for="bio">Biographie</label>
                    <textarea id="bio" rows="4" readonly>Propriétaire et administrateur principal de Salacope. Passionné par la technologie et l'entrepreneuriat.</textarea>
                </div>
            </form>
        </div>
        
        <!-- Section Sécurité -->
        <div class="profile-section" id="securitySection">
            <h3><i class="fas fa-lock"></i> Sécurité du compte</h3>
            
            <div class="security-item">
                <div class="security-info">
                    <h4>Mot de passe</h4>
                    <p>Dernière modification il y a 30 jours</p>
                </div>
                <button class="btn btn-secondary" id="changePasswordBtn">
                    <i class="fas fa-key"></i> Changer le mot de passe
                </button>
            </div>
            
            <div class="security-item">
                <div class="security-info">
                    <h4>Authentification à deux facteurs</h4>
                    <p>Améliorez la sécurité de votre compte</p>
                </div>
                <label class="switch">
                    <input type="checkbox" id="twoFactorToggle">
                    <span class="slider"></span>
                </label>
            </div>
            
            <div class="security-item">
                <div class="security-info">
                    <h4>Sessions actives</h4>
                    <p>2 appareils connectés</p>
                </div>
                <button class="btn btn-secondary">
                    <i class="fas fa-desktop"></i> Gérer les sessions
                </button>
            </div>
        </div>
        
        <!-- Section Notifications -->
        <div class="profile-section" id="notificationsSection">
            <h3><i class="fas fa-bell"></i> Préférences de notification</h3>
            
            <div class="notification-preference">
                <div class="preference-info">
                    <h4>Notifications par email</h4>
                    <p>Recevoir des emails pour les activités importantes</p>
                </div>
                <label class="switch">
                    <input type="checkbox" checked>
                    <span class="slider"></span>
                </label>
            </div>
            
            <div class="notification-preference">
                <div class="preference-info">
                    <h4>Notifications push</h4>
                    <p>Recevoir des notifications sur votre appareil</p>
                </div>
                <label class="switch">
                    <input type="checkbox" checked>
                    <span class="slider"></span>
                </label>
            </div>
            
            <div class="notification-preference">
                <div class="preference-info">
                    <h4>Alertes de sécurité</h4>
                    <p>Être alerté des activités suspectes</p>
                </div>
                <label class="switch">
                    <input type="checkbox" checked>
                    <span class="slider"></span>
                </label>
            </div>
            
            <div class="notification-preference">
                <div class="preference-info">
                    <h4>Nouvelles fonctionnalités</h4>
                    <p>Être informé des nouvelles mises à jour</p>
                </div>
                <label class="switch">
                    <input type="checkbox">
                    <span class="slider"></span>
                </label>
            </div>
        </div>
        
        <!-- Section Préférences -->
        <div class="profile-section" id="preferencesSection">
            <h3><i class="fas fa-cog"></i> Préférences générales</h3>
            
            <div class="preference-item">
                <div class="preference-info">
                    <h4>Langue</h4>
                    <p>Langue de l'interface</p>
                </div>
                <select class="form-select">
                    <option value="fr" selected>Français</option>
                    <option value="en">English</option>
                    <option value="sw">Swahili</option>
                </select>
            </div>
            
            <div class="preference-item">
                <div class="preference-info">
                    <h4>Fuseau horaire</h4>
                    <p>Heure locale affichée</p>
                </div>
                <select class="form-select">
                    <option value="kinshasa" selected>Kinshasa (GMT+1)</option>
                    <option value="paris">Paris (GMT+2)</option>
                    <option value="newyork">New York (GMT-4)</option>
                </select>
            </div>
            
            <div class="preference-item">
                <div class="preference-info">
                    <h4>Thème</h4>
                    <p>Apparence de l'interface</p>
                </div>
                <div class="theme-options">
                    <label class="theme-option">
                        <input type="radio" name="theme" value="light" checked>
                        <div class="theme-preview light">
                            <i class="fas fa-sun"></i>
                            <span>Clair</span>
                        </div>
                    </label>
                    <label class="theme-option">
                        <input type="radio" name="theme" value="dark">
                        <div class="theme-preview dark">
                            <i class="fas fa-moon"></i>
                            <span>Sombre</span>
                        </div>
                    </label>
                    <label class="theme-option">
                        <input type="radio" name="theme" value="auto">
                        <div class="theme-preview auto">
                            <i class="fas fa-adjust"></i>
                            <span>Auto</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>