<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/css/inscription.css">
    <title>Inscription - Salacope</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <section class="inscription-section">
        <!-- Formulaire d'inscription -->
        <div class="form-container">
            <!-- En-tête avec logo -->
            <div class="form-header">
                <img src="../assets/img_log/logo.jpg" alt="logo app" class="logo">
                <h1>Créer un compte</h1>
                <p>Rejoignez notre communauté d'acheteurs et vendeurs</p>
            </div>

            <!-- Indicateur de progression -->
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
                <div class="steps">
                    <div class="step active" data-step="1">
                        <div class="step-number">1</div>
                        <span class="step-label">Type de compte</span>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-number">2</div>
                        <span class="step-label">Informations</span>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-number">3</div>
                        <span class="step-label">Abonnement</span>
                    </div>
                    <div class="step" data-step="4">
                        <div class="step-number">4</div>
                        <span class="step-label">Confirmation</span>
                    </div>
                </div>
            </div>

            <!-- Formulaire multi-étapes -->
            <form id="inscription-form" action="#" method="post">
                <!-- Étape 1 : Choix du type de compte -->
                <div class="form-step active" id="step-1">
                    <h2>Quel type de compte souhaitez-vous créer ?</h2>
                    <p class="step-description">Choisissez le type de compte qui correspond à vos besoins</p>
                    
                    <div class="account-type-options">
                        <div class="account-type-option" data-type="acheteur">
                            <div class="option-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="option-content">
                                <h3>Acheteur</h3>
                                <p>Je veux acheter des produits et services</p>
                                <ul class="option-features">
                                    <li><i class="fas fa-check"></i> Parcourir les produits</li>
                                    <li><i class="fas fa-check"></i> Commander en ligne</li>
                                    <li><i class="fas fa-check"></i> Suivre mes commandes</li>
                                    <li><i class="fas fa-check"></i> Gratuit à vie</li>
                                </ul>
                            </div>
                            <div class="option-select">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                        
                        <div class="account-type-option" data-type="vendeur">
                            <div class="option-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <div class="option-content">
                                <h3>Vendeur</h3>
                                <p>Je veux vendre mes produits et services</p>
                                <ul class="option-features">
                                    <li><i class="fas fa-check"></i> Gérer mes produits</li>
                                    <li><i class="fas fa-check"></i> Vendre en ligne</li>
                                    <li><i class="fas fa-check"></i> Suivre mes ventes</li>
                                    <li><i class="fas fa-check"></i> Plans flexibles</li>
                                </ul>
                            </div>
                            <div class="option-select">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="button" class="btn btn-next" id="next-step-1" disabled>Suivant <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <!-- Étape 2 : Informations selon le type de compte -->
                <div class="form-step" id="step-2">
                    <!-- Formulaire pour Acheteur -->
                    <div class="account-form" id="acheteur-form">
                        <h2><i class="fas fa-user"></i> Informations personnelles</h2>
                        <p class="step-description">Remplissez vos informations personnelles pour créer votre compte acheteur</p>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="nom">Nom *</label>
                                <input type="text" id="nom" name="nom" required placeholder="Votre nom">
                                <div class="error-message"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="postnom">Post-nom *</label>
                                <input type="text" id="postnom" name="postnom" required placeholder="Votre post-nom">
                                <div class="error-message"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="prenom">Prénom *</label>
                                <input type="text" id="prenom" name="prenom" required placeholder="Votre prénom">
                                <div class="error-message"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="telephone">Numéro de téléphone *</label>
                                <input type="tel" id="telephone" name="telephone" required placeholder="Ex: +243 00 000 0000">
                                <div class="error-message"></div>
                            </div>
                            
                            <div class="form-group full-width">
                                <label for="email">Adresse email *</label>
                                <input type="email" id="email" name="email" required placeholder="votre@email.com">
                                <div class="error-message"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Formulaire pour Vendeur -->
                    <div class="account-form" id="vendeur-form">
                        <h2><i class="fas fa-building"></i> Informations de l'entreprise</h2>
                        <p class="step-description">Remplissez les informations de votre entreprise pour créer votre compte vendeur</p>
                        
                        <div class="form-section">
                            <h3><i class="fas fa-store"></i> Informations sur l'entreprise</h3>
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label for="nom_entreprise">Nom de l'entreprise *</label>
                                    <input type="text" id="nom_entreprise" name="nom_entreprise" required placeholder="Nom de votre entreprise">
                                    <div class="error-message"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="telephone_entreprise">Téléphone entreprise *</label>
                                    <input type="tel" id="telephone_entreprise" name="telephone_entreprise" required placeholder="Ex: +243 00 000 0000">
                                    <div class="error-message"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email_entreprise">Email entreprise *</label>
                                    <input type="email" id="email_entreprise" name="email_entreprise" required placeholder="contact@entreprise.com">
                                    <div class="error-message"></div>
                                </div>
                                
                                <div class="form-group full-width">
                                    <label for="adresse_entreprise">Adresse de l'entreprise *</label>
                                    <input type="text" id="adresse_entreprise" name="adresse_entreprise" required placeholder="Adresse complète de l'entreprise">
                                    <div class="error-message"></div>
                                </div>
                                
                                <div class="form-group full-width">
                                    <label for="type_entreprise">Type d'entreprise *</label>
                                    <select id="type_entreprise" name="type_entreprise" required>
                                        <option value="">Sélectionnez un type</option>
                                        <option value="SARL">SARL</option>
                                        <option value="SA">SA</option>
                                        <option value="SNC">SNC</option>
                                        <option value="EI">Entreprise Individuelle</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                    <div class="error-message"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h3><i class="fas fa-user-tie"></i> Informations du propriétaire</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="nom_proprietaire">Nom *</label>
                                    <input type="text" id="nom_proprietaire" name="nom_proprietaire" required placeholder="Nom du propriétaire">
                                    <div class="error-message"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="postnom_proprietaire">Post-nom *</label>
                                    <input type="text" id="postnom_proprietaire" name="postnom_proprietaire" required placeholder="Post-nom du propriétaire">
                                    <div class="error-message"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="prenom_proprietaire">Prénom *</label>
                                    <input type="text" id="prenom_proprietaire" name="prenom_proprietaire" required placeholder="Prénom du propriétaire">
                                    <div class="error-message"></div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="fonction_proprietaire">Fonction *</label>
                                    <input type="text" id="fonction_proprietaire" name="fonction_proprietaire" required placeholder="Ex: Gérant, Directeur">
                                    <div class="error-message"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3><i class="fas fa-lock"></i> Sécurité du compte</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="password">Mot de passe *</label>
                                <div class="password-container">
                                    <input type="password" id="password" name="password" required placeholder="Créez un mot de passe">
                                    <button type="button" class="toggle-password" id="toggle-password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength">
                                    <div class="strength-bar"></div>
                                    <span class="strength-text">Force du mot de passe</span>
                                </div>
                                <div class="error-message"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Confirmer le mot de passe *</label>
                                <div class="password-container">
                                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Répétez le mot de passe">
                                    <button type="button" class="toggle-password" id="toggle-confirm-password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="error-message"></div>
                            </div>
                        </div>
                        
                        <div class="password-requirements">
                            <p>Le mot de passe doit contenir :</p>
                            <ul>
                                <li class="requirement" data-rule="length">Au moins 8 caractères</li>
                                <li class="requirement" data-rule="uppercase">Une lettre majuscule</li>
                                <li class="requirement" data-rule="lowercase">Une lettre minuscule</li>
                                <li class="requirement" data-rule="number">Un chiffre</li>
                                <li class="requirement" data-rule="special">Un caractère spécial</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="button" class="btn btn-prev"><i class="fas fa-arrow-left"></i> Retour</button>
                        <button type="button" class="btn btn-next" id="next-step-2">Suivant <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <!-- Étape 3 : Choix de l'abonnement (uniquement pour vendeurs) -->
                <div class="form-step" id="step-3">
                    <h2><i class="fas fa-crown"></i> Choisissez votre formule</h2>
                    <p class="step-description">Sélectionnez la formule qui correspond le mieux à vos besoins commerciaux</p>
                    
                    <div class="subscription-options" id="subscription-options">
                        <!-- Essai gratuit -->
                        <div class="subscription-option" data-plan="essai">
                            <div class="subscription-header">
                                <div class="subscription-badge">Essai Gratuit</div>
                                <h3>Essai</h3>
                                <div class="subscription-price">
                                    <span class="price-amount">0 FC</span>
                                    <span class="price-period">/ 30 jours</span>
                                </div>
                            </div>
                            
                            <div class="subscription-features">
                                <ul>
                                    <li><i class="fas fa-check"></i> <strong>10 crédits</strong> pour tester</li>
                                    <li><i class="fas fa-check"></i> Publier jusqu'à 5 produits</li>
                                    <li><i class="fas fa-check"></i> Répondre à 10 clients maximum</li>
                                    <li><i class="fas fa-check"></i> Commencer 10 ventes maximum</li>
                                    <li><i class="fas fa-check"></i> Support de base</li>
                                    <li><i class="fas fa-times"></i> Pas de publicité sur la page d'accueil</li>
                                    <li><i class="fas fa-times"></i> Pas d'assistant IA</li>
                                </ul>
                            </div>
                            
                            <div class="subscription-action">
                                <button type="button" class="btn btn-select-plan" data-plan="essai">
                                    Choisir l'essai
                                </button>
                            </div>
                        </div>
                        
                        <!-- Standard -->
                        <div class="subscription-option recommended" data-plan="standard">
                            <div class="subscription-header">
                                <div class="subscription-badge">Populaire</div>
                                <h3>Standard</h3>
                                <div class="subscription-price">
                                    <span class="price-amount">7 000 FC</span>
                                    <span class="price-period">/ mois</span>
                                </div>
                            </div>
                            
                            <div class="subscription-features">
                                <ul>
                                    <li><i class="fas fa-check"></i> <strong>100 crédits</strong> par mois</li>
                                    <li><i class="fas fa-check"></i> Publier produits nombre des crédits</li>
                                    <li><i class="fas fa-check"></i> Répondre à 100 clients/mois</li>
                                    <li><i class="fas fa-check"></i> Multiplier crédits avec durée</li>
                                    <li><i class="fas fa-check"></i> Support prioritaire</li>
                                    <li><i class="fas fa-check"></i> Statistiques de vente</li>
                                    <li><i class="fas fa-times"></i> Pas d'assistant IA</li>
                                </ul>
                            </div>
                            
                            <div class="subscription-duration">
                                <h4>Durée d'abonnement</h4>
                                <div class="duration-options">
                                    <label class="duration-option">
                                        <input type="radio" name="duration_standard" value="1" checked>
                                        <span>1 mois - 7 000 FC</span>
                                    </label>
                                    <label class="duration-option">
                                        <input type="radio" name="duration_standard" value="3">
                                        <span>3 mois - 19 000 FC</span>
                                    </label>
                                    <label class="duration-option">
                                        <input type="radio" name="duration_standard" value="6">
                                        <span>6 mois - 35 000 FC</span>
                                    </label>
                                    <label class="duration-option">
                                        <input type="radio" name="duration_standard" value="12">
                                        <span>12 mois - 65 000 FC</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="subscription-action">
                                <button type="button" class="btn btn-select-plan" data-plan="standard">
                                    Choisir Standard
                                </button>
                            </div>
                        </div>
                        
                        <!-- Premium -->
                        <div class="subscription-option premium" data-plan="premium">
                            <div class="subscription-header">
                                <div class="subscription-badge">Premium</div>
                                <h3>Premium</h3>
                                <div class="subscription-price">
                                    <span class="price-amount">9 000 FC</span>
                                    <span class="price-period">/ mois</span>
                                </div>
                            </div>
                            
                            <div class="subscription-features">
                                <ul>
                                    <li><i class="fas fa-check"></i> <strong>130 crédits</strong> par mois</li>
                                    <li><i class="fas fa-check"></i> Publicité sur page d'accueil</li>
                                    <li><i class="fas fa-check"></i> Assistant IA intégré</li>
                                    <li><i class="fas fa-check"></i> Répond automatiquement</li>
                                    <li><i class="fas fa-check"></i> Support 24/7</li>
                                    <li><i class="fas fa-check"></i> Analyse avancée</li>
                                    <li><i class="fas fa-check"></i> Produits en vedette</li>
                                </ul>
                            </div>
                            
                            <div class="subscription-duration">
                                <h4>Durée d'abonnement</h4>
                                <div class="duration-options">
                                    <label class="duration-option">
                                        <input type="radio" name="duration_premium" value="1" checked>
                                        <span>1 mois - 9 000 FC</span>
                                    </label>
                                    <label class="duration-option">
                                        <input type="radio" name="duration_premium" value="3">
                                        <span>3 mois - 25 000 FC</span>
                                    </label>
                                    <label class="duration-option">
                                        <input type="radio" name="duration_premium" value="6">
                                        <span>6 mois - 45 000 FC</span>
                                    </label>
                                    <label class="duration-option">
                                        <input type="radio" name="duration_premium" value="12">
                                        <span>12 mois - 85 000 FC</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="subscription-action">
                                <button type="button" class="btn btn-select-plan" data-plan="premium">
                                    Choisir Premium
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="payment-section" id="payment-section" style="display: none;">
                        <h3><i class="fas fa-credit-card"></i> Paiement</h3>
                        <p>Pour finaliser votre inscription, veuillez choisir votre méthode de paiement :</p>
                        
                        <div class="payment-methods">
                            <div class="payment-method">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="mobile_money" checked>
                                    <div class="payment-content">
                                        <div class="payment-icon">
                                            <i class="fas fa-mobile-alt"></i>
                                        </div>
                                        <div class="payment-text">
                                            <h4>Mobile Money</h4>
                                            <p>Paiement via Airtel Money, Orange Money, M-Pesa</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="payment-method">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="carte_bancaire">
                                    <div class="payment-content">
                                        <div class="payment-icon">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                        <div class="payment-text">
                                            <h4>Carte Bancaire</h4>
                                            <p>Visa, Mastercard, UnionPay</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="payment-method">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="virement">
                                    <div class="payment-content">
                                        <div class="payment-icon">
                                            <i class="fas fa-university"></i>
                                        </div>
                                        <div class="payment-text">
                                            <h4>Virement Bancaire</h4>
                                            <p>Transfert bancaire direct</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <div class="payment-details" id="payment-details">
                            <!-- Les détails de paiement seront affichés ici -->
                        </div>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="button" class="btn btn-prev"><i class="fas fa-arrow-left"></i> Retour</button>
                        <button type="button" class="btn btn-next" id="next-step-3" disabled>Continuer vers la confirmation <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <!-- Étape 4 : Confirmation -->
                <div class="form-step" id="step-4">
                    <h2><i class="fas fa-check-circle"></i> Confirmation</h2>
                    <p class="step-description">Vérifiez vos informations avant de finaliser votre inscription</p>
                    
                    <div class="confirmation-summary">
                        <div class="summary-section">
                            <h3>Type de compte</h3>
                            <p id="summary-account-type"></p>
                        </div>
                        
                        <div class="summary-section" id="summary-personal-info">
                            <h3>Informations personnelles</h3>
                            <div class="summary-details"></div>
                        </div>
                        
                        <div class="summary-section" id="summary-business-info">
                            <h3>Informations de l'entreprise</h3>
                            <div class="summary-details"></div>
                        </div>
                        
                        <div class="summary-section" id="summary-subscription-info">
                            <h3>Formule choisie</h3>
                            <div class="summary-details"></div>
                        </div>
                        
                        <div class="summary-section">
                            <h3>Conditions d'utilisation</h3>
                            <div class="terms-container">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="terms" name="terms" required>
                                    <span class="checkmark"></span>
                                    J'accepte les <a href="#" class="terms-link">conditions d'utilisation</a> et la 
                                    <a href="../assets/politique.pdf" class="terms-link" download>politique de confidentialité</a>
                                </label>
                                <div class="error-message"></div>
                            </div>
                            
                            <div class="terms-container">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="newsletter" name="newsletter" checked>
                                    <span class="checkmark"></span>
                                    Je souhaite recevoir des offres promotionnelles et des actualités par email
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="button" class="btn btn-prev"><i class="fas fa-arrow-left"></i> Retour</button>
                        <button type="submit" class="btn btn-submit" id="submit-form">
                            <i class="fas fa-user-plus"></i> Finaliser l'inscription
                        </button>
                    </div>
                </div>
            </form>
            
            <div class="login-link">
                <p>Vous avez déjà un compte ? <a href="./login.php">Connectez-vous</a></p>
            </div>
            
            <div class="copyright">
                <p>© COPYRIGHT SALACOPE || créé par <a href="#">Ben tech</a></p>
            </div>
        </div>
        
        <!-- Section illustration / informations -->
        <div class="illustration-container">
            <div class="illustration-content">
                <div class="illustration-header">
                    <h2>Rejoignez notre plateforme</h2>
                    <p>Connectez acheteurs et vendeurs en toute simplicité</p>
                </div>
                
                <div class="illustration-features">
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Sécurisé et fiable</h3>
                            <p>Vos données sont protégées avec un chiffrement de pointe</p>
                        </div>
                    </div>
                    
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Transactions rapides</h3>
                            <p>Effectuez vos achats et ventes en quelques clics</p>
                        </div>
                    </div>
                    
                    <div class="feature">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="feature-text">
                            <h3>Croissance garantie</h3>
                            <p>Boostez vos ventes avec nos formules adaptées</p>
                        </div>
                    </div>
                </div>
                
                <div class="subscription-benefits">
                    <h3>Nos formules pour vendeurs</h3>
                    <div class="benefits-grid">
                        <div class="benefit">
                            <i class="fas fa-gem"></i>
                            <h4>Essai Gratuit</h4>
                            <p>Testez gratuitement avec 10 crédits</p>
                        </div>
                        <div class="benefit">
                            <i class="fas fa-star"></i>
                            <h4>Standard</h4>
                            <p>100 crédits/mois à 7 000 FC</p>
                        </div>
                        <div class="benefit">
                            <i class="fas fa-crown"></i>
                            <h4>Premium</h4>
                            <p>130 crédits + IA à 9 000 FC</p>
                        </div>
                    </div>
                </div>
                
                <div class="illustration-image">
                    <div class="image-placeholder">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <script src="../style/js/inscription.js"></script>
</body>
</html>