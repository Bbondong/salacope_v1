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
    </div>

    <div class="settings-content">

        <!-- Général -->
        <div id="general-settings" class="settings-section active">
            <h3>Paramètres généraux</h3>

            <div class="form-group">
                <label class="form-label">Nom du site</label>
                <input type="text" class="form-control" value="Salacopp">
            </div>

            <div class="form-group">
                <label class="form-label">Description du site</label>
                <textarea class="form-control" rows="3">
La meilleure plateforme de vente et d'achat en ligne
                </textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Email de l'administrateur</label>
                <input type="email" class="form-control" value="admin@salacopp.com">
            </div>

            <button class="btn btn-primary">Enregistrer</button>
        </div>

        <!-- Notifications -->
        <div id="notification-settings" class="settings-section">
            <h3>Paramètres de notifications</h3>

            <div class="form-group">
                <label class="form-label">
                    <input type="checkbox" checked>
                    Activer les notifications par email
                </label>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <input type="checkbox" checked>
                    Notifications de vente
                </label>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <input type="checkbox" checked>
                    Notifications de messages
                </label>
            </div>

            <button class="btn btn-primary">Enregistrer</button>
        </div>

        <!-- 🔐 Sécurité -->
        <div id="security-settings" class="settings-section">
            <h3>Paramètres de sécurité</h3>

            <form method="POST" action="../backend/traitement/security_update.php">

                <div class="form-group">
                    <label class="form-label">Mot de passe actuel</label>
                    <input type="password"
                           name="current_password"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password"
                           name="new_password"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirmer le nouveau mot de passe</label>
                    <input type="password"
                           name="confirm_password"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <small style="color:#888;">
                        ⚠️ Seul le compte <strong>fondateur</strong> peut modifier le mot de passe.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">
                    Mettre à jour la sécurité
                </button>

            </form>
        </div>

    </div>
</div>
