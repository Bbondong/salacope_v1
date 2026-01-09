<h2>Paramètres de l'administration</h2>
<p class="page-description">Configurez les paramètres de votre espace d'administration.</p>

<div class="settings-container">
    <div class="settings-nav">
        <div class="settings-nav-item active" data-target="general-settings">
            <i class="fas fa-cog"></i> Général
        </div>
        <div class="settings-nav-item" data-target="notification-settings">
            <i class="fas fa-bell"></i> Notifications
        </div>
        <div class="settings-nav-item" data-target="security-settings">
            <i class="fas fa-shield-alt"></i> Sécurité
        </div>
        <div class="settings-nav-item" data-target="appearance-settings">
            <i class="fas fa-palette"></i> Apparence
        </div>
    </div>
    
    <div class="settings-content">
        <!-- Général -->
        <div id="general-settings" class="settings-section active">
            <h3>Paramètres généraux</h3>
            <div class="form-group">
                <label for="site-name" class="form-label">Nom du site</label>
                <input type="text" id="site-name" class="form-control" value="Salacopp">
            </div>
            
            <div class="form-group">
                <label for="site-description" class="form-label">Description du site</label>
                <textarea id="site-description" class="form-control" rows="3">La meilleure plateforme de vente et d'achat en ligne</textarea>
            </div>
            
            <div class="form-group">
                <label for="admin-email" class="form-label">Email de l'administrateur</label>
                <input type="email" id="admin-email" class="form-control" value="admin@salacopp.com">
            </div>
            
            <div class="form-group">
                <label class="form-label">Mode maintenance</label>
                <div class="toggle-container">
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label">Activer le mode maintenance</span>
                </div>
            </div>
            
            <button class="btn btn-primary">Enregistrer les modifications</button>
        </div>
        
        <!-- Notifications -->
        <div id="notification-settings" class="settings-section">
            <h3>Paramètres de notifications</h3>
            
            <div class="form-group">
                <label class="form-label">Notifications par email</label>
                <div class="toggle-container">
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label">Activer les notifications email</span>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Notifications de vente</label>
                <div class="toggle-container">
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label">Recevoir des notifications pour chaque vente</span>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Notifications de message</label>
                <div class="toggle-container">
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label">Recevoir des notifications pour les nouveaux messages</span>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Notifications d'abonnement</label>
                <div class="toggle-container">
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label">Recevoir des notifications pour les nouveaux abonnements</span>
                </div>
            </div>
            
            <button class="btn btn-primary">Enregistrer les modifications</button>
        </div>
        
        <!-- Sécurité -->
        <div id="security-settings" class="settings-section">
            <h3>Paramètres de sécurité</h3>
            
            <div class="form-group">
                <label for="current-password" class="form-label">Mot de passe actuel</label>
                <input type="password" id="current-password" class="form-control" placeholder="Entrez votre mot de passe actuel">
            </div>
            
            <div class="form-group">
                <label for="new-password" class="form-label">Nouveau mot de passe</label>
                <input type="password" id="new-password" class="form-control" placeholder="Entrez votre nouveau mot de passe">
            </div>
            
            <div class="form-group">
                <label for="confirm-password" class="form-label">Confirmer le nouveau mot de passe</label>
                <input type="password" id="confirm-password" class="form-control" placeholder="Confirmez votre nouveau mot de passe">
            </div>
            
            <div class="form-group">
                <label class="form-label">Authentification à deux facteurs</label>
                <div class="toggle-container">
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label">Activer l'authentification à deux facteurs</span>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Limiter les tentatives de connexion</label>
                <div class="toggle-container">
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label">Bloquer après 5 tentatives échouées</span>
                </div>
            </div>
            
            <button class="btn btn-primary">Mettre à jour la sécurité</button>
        </div>
        
        <!-- Apparence -->
        <div id="appearance-settings" class="settings-section">
            <h3>Paramètres d'apparence</h3>
            
            <div class="form-group">
                <label for="theme-color" class="form-label">Couleur du thème</label>
                <input type="color" id="theme-color" class="form-control" value="#25D366" style="height: 50px; width: 100px; padding: 5px;">
            </div>
            
            <div class="form-group">
                <label for="admin-language" class="form-label">Langue de l'administration</label>
                <select id="admin-language" class="form-control">
                    <option value="fr" selected>Français</option>
                    <option value="en">English</option>
                    <option value="es">Español</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Mode sombre</label>
                <div class="toggle-container">
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label">Activer le mode sombre</span>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Animation des éléments</label>
                <div class="toggle-container">
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label">Activer les animations</span>
                </div>
            </div>
            
            <button class="btn btn-primary">Appliquer les changements</button>
        </div>
    </div>
</div>