// fichier: ../js/inscription.js

document.addEventListener('DOMContentLoaded', function() {
    // ========== ÉLÉMENTS DOM ==========
    const steps = document.querySelectorAll('.form-step');
    const progressFill = document.getElementById('progress-fill');
    const accountTypeOptions = document.querySelectorAll('.account-type-option');
    const nextStep1Btn = document.getElementById('next-step-1');
    const nextStep2Btn = document.getElementById('next-step-2');
    const nextStep3Btn = document.getElementById('next-step-3');
    const prevBtns = document.querySelectorAll('.btn-prev');
    const acheteurForm = document.getElementById('acheteur-form');
    const vendeurForm = document.getElementById('vendeur-form');
    const subscriptionOptions = document.querySelectorAll('.subscription-option');
    const selectPlanBtns = document.querySelectorAll('.btn-select-plan');
    const paymentSection = document.getElementById('payment-section');
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordRequirements = document.querySelectorAll('.requirement');
    const termsCheckbox = document.getElementById('terms');
    const submitBtn = document.getElementById('submit-form');
    const durationInputs = document.querySelectorAll('input[name^="duration_"]');
    const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
    const paymentDetails = document.getElementById('payment-details');
    
    // ========== VARIABLES GLOBALES ==========
    let currentStep = 1;
    let selectedAccountType = null;
    let selectedPlan = null;
    let selectedDuration = '1';
    let selectedPaymentMethod = 'mobile_money';
    let paymentReference = generatePaymentReference();
    let formData = {
        acheteur: {},
        vendeur: {},
        subscription: {},
        payment: {}
    };

    // ========== CONSTANTES DE CONFIGURATION ==========
    const CONFIG = {
        API_BASE_URL: 'https://api.salacope.com', // À modifier avec votre URL
        PAYMENT_API: {
            MOBILE_MONEY: '/api/payment/mobile-money',
            CARD: '/api/payment/card',
            BANK_TRANSFER: '/api/payment/bank-transfer',
            VERIFY: '/api/payment/verify'
        },
        PRICING: {
            STANDARD: {
                1: 7000,
                3: 19000,
                6: 35000,
                12: 65000
            },
            PREMIUM: {
                1: 9000,
                3: 25000,
                6: 45000,
                12: 85000
            }
        },
        CURRENCY: 'CDF'
    };

    // ========== INITIALISATION ==========
    function init() {
        // Désactiver le bouton suivant pour l'étape 1
        nextStep1Btn.disabled = true;
        nextStep3Btn.disabled = true;
        submitBtn.disabled = true;
        
        // Initialiser la validation du mot de passe
        validatePassword(passwordInput.value);
        
        // Écouter les changements sur les champs de formulaire
        setupFormListeners();
        
        // Ajouter les styles pour les notifications
        addNotificationStyles();
        
        // Initialiser les options de paiement
        initPaymentOptions();
    }

    // ========== ÉCOUTEURS D'ÉVÉNEMENTS ==========
    
    // Choix du type de compte
    accountTypeOptions.forEach(option => {
        option.addEventListener('click', function() {
            selectAccountType(this.getAttribute('data-type'));
        });
    });

    // Navigation
    nextStep1Btn.addEventListener('click', goToStep2);
    nextStep2Btn.addEventListener('click', goToStep2Next);
    nextStep3Btn.addEventListener('click', goToStep3Next);
    
    prevBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            goToStep(currentStep - 1);
        });
    });

    // Sélection des plans
    selectPlanBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            selectPlan(this.getAttribute('data-plan'));
        });
    });

    // Durée d'abonnement
    durationInputs.forEach(input => {
        input.addEventListener('change', function() {
            selectedDuration = this.value;
            updatePaymentDetails();
        });
    });

    // Méthode de paiement
    paymentMethodInputs.forEach(input => {
        input.addEventListener('change', function() {
            selectedPaymentMethod = this.value;
            updatePaymentDetails();
        });
    });

    // Mot de passe
    passwordInput.addEventListener('input', function() {
        validatePassword(this.value);
    });

    // Conditions d'utilisation
    termsCheckbox.addEventListener('click', function() {
        submitBtn.disabled = !this.checked;
    });

    // Soumission du formulaire
    document.getElementById('inscription-form').addEventListener('submit', submitForm);

    // Afficher/masquer le mot de passe
    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', togglePasswordVisibility);
    });

    // ========== FONCTIONS PRINCIPALES ==========

    // Sélection du type de compte
    function selectAccountType(type) {
        // Retirer la sélection précédente
        accountTypeOptions.forEach(opt => {
            opt.classList.remove('selected');
        });
        
        // Ajouter la sélection à l'option cliquée
        document.querySelector(`.account-type-option[data-type="${type}"]`).classList.add('selected');
        selectedAccountType = type;
        
        // Activer le bouton suivant
        nextStep1Btn.disabled = false;
        
        // Sauvegarder le type de compte
        formData.accountType = type;
    }

    // Navigation vers l'étape 2
    function goToStep2() {
        if (!selectedAccountType) {
            showNotification('Veuillez sélectionner un type de compte', 'error');
            return;
        }
        
        // Afficher le formulaire approprié
        if (selectedAccountType === 'acheteur') {
            acheteurForm.classList.add('active');
            vendeurForm.classList.remove('active');
        } else {
            acheteurForm.classList.remove('active');
            vendeurForm.classList.add('active');
        }
        
        goToStep(2);
    }

    // Navigation depuis l'étape 2
    function goToStep2Next() {
        if (!validateStep2()) {
            showNotification('Veuillez corriger les erreurs dans le formulaire', 'error');
            return;
        }
        
        // Sauvegarder les données du formulaire
        saveFormData();
        
        if (selectedAccountType === 'acheteur') {
            // Acheteur va directement à la confirmation
            goToStep(4);
        } else {
            // Vendeur va à la sélection d'abonnement
            goToStep(3);
        }
    }

    // Navigation depuis l'étape 3
    function goToStep3Next() {
        if (selectedPlan === 'essai') {
            goToStep(4);
        } else {
            if (!validatePaymentSelection()) {
                showNotification('Veuillez sélectionner une méthode de paiement', 'error');
                return;
            }
            goToStep(4);
        }
    }

    // Changer d'étape
    function goToStep(step) {
        // Validation des étapes
        if (step < 1 || step > 4) return;
        
        // Masquer l'étape actuelle
        document.getElementById(`step-${currentStep}`).classList.remove('active');
        
        // Afficher la nouvelle étape
        document.getElementById(`step-${step}`).classList.add('active');
        
        // Mettre à jour l'indicateur de progression
        updateProgressIndicator(step);
        
        // Mettre à jour l'étape actuelle
        currentStep = step;
        
        // Gérer l'affichage des boutons
        updateNavigationButtons(step);
        
        // Mettre à jour les sections spécifiques
        updateStepSections(step);
        
        // Faire défiler vers le haut
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Mettre à jour l'indicateur de progression
    function updateProgressIndicator(step) {
        document.querySelectorAll('.step').forEach(stepEl => {
            stepEl.classList.remove('active');
        });
        
        document.querySelectorAll(`.step`).forEach(stepEl => {
            if (parseInt(stepEl.getAttribute('data-step')) <= step) {
                stepEl.classList.add('active');
            }
        });
        
        // Mettre à jour la barre de progression
        const progressPercent = (step - 1) * (100 / 3);
        progressFill.style.width = `${progressPercent}%`;
    }

    // Mettre à jour les boutons de navigation
    function updateNavigationButtons(step) {
        const prevBtn = document.querySelector('.btn-prev');
        
        if (step === 1) {
            prevBtn.style.visibility = 'hidden';
        } else {
            prevBtn.style.visibility = 'visible';
        }
        
        // Mettre à jour le texte du bouton suivant étape 3
        if (step === 3) {
            if (selectedPlan === 'essai') {
                nextStep3Btn.textContent = 'Continuer vers la confirmation';
            } else {
                nextStep3Btn.textContent = 'Continuer';
            }
        }
    }

    // Mettre à jour les sections spécifiques à chaque étape
    function updateStepSections(step) {
        if (step === 3 && selectedAccountType === 'vendeur') {
            updatePaymentSection();
        }
        
        if (step === 4) {
            updateSummary();
        }
    }

    // ========== VALIDATION DU FORMULAIRE ==========

    // Validation de l'étape 2
    function validateStep2() {
        let isValid = true;
        
        // Réinitialiser les erreurs
        clearErrors();
        
        // Validation selon le type de compte
        if (selectedAccountType === 'acheteur') {
            isValid = validateAcheteurForm() && isValid;
        } else {
            isValid = validateVendeurForm() && isValid;
        }
        
        // Validation du mot de passe
        isValid = validatePasswordFields() && isValid;
        
        return isValid;
    }

    // Validation du formulaire acheteur
    function validateAcheteurForm() {
        let isValid = true;
        const fields = ['nom', 'postnom', 'prenom', 'telephone', 'email'];
        
        fields.forEach(fieldId => {
            const input = document.getElementById(fieldId);
            if (!input.value.trim()) {
                showError(input, 'Ce champ est obligatoire');
                isValid = false;
            } else if (fieldId === 'email' && !isValidEmail(input.value)) {
                showError(input, 'Veuillez entrer une adresse email valide');
                isValid = false;
            } else if (fieldId === 'telephone' && !isValidPhone(input.value)) {
                showError(input, 'Veuillez entrer un numéro de téléphone valide');
                isValid = false;
            }
        });
        
        return isValid;
    }

    // Validation du formulaire vendeur
    function validateVendeurForm() {
        let isValid = true;
        
        // Informations de l'entreprise
        const entrepriseFields = ['nom_entreprise', 'telephone_entreprise', 'email_entreprise', 'adresse_entreprise', 'type_entreprise'];
        entrepriseFields.forEach(fieldId => {
            const input = document.getElementById(fieldId);
            if (!input.value.trim()) {
                showError(input, 'Ce champ est obligatoire');
                isValid = false;
            } else if (fieldId === 'email_entreprise' && !isValidEmail(input.value)) {
                showError(input, 'Veuillez entrer une adresse email valide');
                isValid = false;
            } else if (fieldId === 'telephone_entreprise' && !isValidPhone(input.value)) {
                showError(input, 'Veuillez entrer un numéro de téléphone valide');
                isValid = false;
            }
        });
        
        // Informations du propriétaire
        const proprietaireFields = ['nom_proprietaire', 'postnom_proprietaire', 'prenom_proprietaire', 'fonction_proprietaire'];
        proprietaireFields.forEach(fieldId => {
            const input = document.getElementById(fieldId);
            if (!input.value.trim()) {
                showError(input, 'Ce champ est obligatoire');
                isValid = false;
            }
        });
        
        return isValid;
    }

    // Validation des champs de mot de passe
    function validatePasswordFields() {
        let isValid = true;
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (!password) {
            showError(passwordInput, 'Le mot de passe est obligatoire');
            isValid = false;
        } else if (password.length < 8) {
            showError(passwordInput, 'Le mot de passe doit contenir au moins 8 caractères');
            isValid = false;
        }
        
        if (!confirmPassword) {
            showError(confirmPasswordInput, 'Veuillez confirmer votre mot de passe');
            isValid = false;
        } else if (password !== confirmPassword) {
            showError(confirmPasswordInput, 'Les mots de passe ne correspondent pas');
            isValid = false;
        }
        
        return isValid;
    }

    // Validation de la sélection du paiement
    function validatePaymentSelection() {
        if (!selectedPaymentMethod) {
            return false;
        }
        return true;
    }

    // Validation de l'email
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Validation du téléphone
    function isValidPhone(phone) {
        const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
        return phoneRegex.test(phone);
    }

    // ========== GESTION DES PLANS D'ABONNEMENT ==========

    // Sélection d'un plan
    function selectPlan(plan) {
        selectedPlan = plan;
        formData.subscription.plan = plan;
        
        // Retirer la sélection précédente
        subscriptionOptions.forEach(option => {
            option.classList.remove('selected');
        });
        
        // Ajouter la sélection
        document.querySelector(`.subscription-option[data-plan="${plan}"]`).classList.add('selected');
        
        // Mettre à jour la section paiement
        updatePaymentSection();
        
        // Activer le bouton suivant
        nextStep3Btn.disabled = false;
        
        // Sauvegarder les données d'abonnement
        saveSubscriptionData();
    }

    // Mettre à jour la section paiement
    function updatePaymentSection() {
        if (selectedPlan === 'essai') {
            paymentSection.style.display = 'none';
            paymentSection.classList.remove('active');
        } else {
            paymentSection.style.display = 'block';
            paymentSection.classList.add('active');
            updatePaymentDetails();
        }
    }

    // Mettre à jour les détails de paiement
    function updatePaymentDetails() {
        if (selectedPlan === 'essai') return;
        
        const { planName, price, credits } = calculateSubscriptionDetails();
        paymentReference = generatePaymentReference();
        
        const paymentInfo = generatePaymentInfo(planName, price, credits);
        paymentDetails.innerHTML = paymentInfo;
        
        // Configurer le bouton "Payer maintenant"
        setupPayNowButton();
        
        // Sauvegarder les données de paiement
        formData.payment = {
            plan: planName,
            duration: selectedDuration,
            method: selectedPaymentMethod,
            amount: price,
            credits: credits,
            reference: paymentReference,
            currency: CONFIG.CURRENCY
        };
    }

    // Calculer les détails de l'abonnement
    function calculateSubscriptionDetails() {
        let planName = '';
        let price = 0;
        let credits = 0;
        
        switch(selectedPlan) {
            case 'standard':
                planName = 'Standard';
                credits = 100 * parseInt(selectedDuration);
                price = CONFIG.PRICING.STANDARD[selectedDuration] || CONFIG.PRICING.STANDARD[1];
                break;
            case 'premium':
                planName = 'Premium';
                credits = 130 * parseInt(selectedDuration);
                price = CONFIG.PRICING.PREMIUM[selectedDuration] || CONFIG.PRICING.PREMIUM[1];
                break;
            case 'essai':
                planName = 'Essai Gratuit';
                price = 0;
                credits = 10;
                break;
        }
        
        return { planName, price, credits };
    }

    // Générer les informations de paiement
    function generatePaymentInfo(planName, price, credits) {
        return `
            <div class="payment-summary">
                <h4><i class="fas fa-receipt"></i> Récapitulatif de paiement</h4>
                <div class="order-details">
                    <div class="detail-row">
                        <span>Formule :</span>
                        <strong>${planName} (${selectedDuration} mois)</strong>
                    </div>
                    <div class="detail-row">
                        <span>Crédits :</span>
                        <strong>${credits} crédits</strong>
                    </div>
                    <div class="detail-row total">
                        <span>Montant total :</span>
                        <strong>${price.toLocaleString()} ${CONFIG.CURRENCY}</strong>
                    </div>
                </div>
            </div>
            
            <div class="payment-instructions">
                <h4><i class="fas fa-info-circle"></i> Instructions de paiement</h4>
                <p>Veuillez cliquer sur "Payer maintenant" pour procéder au paiement sécurisé.</p>
                <p class="payment-note">
                    <i class="fas fa-shield-alt"></i> 
                    <strong>Sécurité garantie :</strong> Votre paiement est protégé par notre système de paiement sécurisé.
                </p>
            </div>
            
            <div class="payment-action">
                <button type="button" class="btn btn-pay-now" id="pay-now">
                    <i class="fas fa-lock"></i> Payer maintenant ${price.toLocaleString()} ${CONFIG.CURRENCY}
                </button>
                <p class="payment-security">
                    <i class="fas fa-lock"></i> Paiement 100% sécurisé via notre partenaire de paiement
                </p>
            </div>
            
            <div class="payment-methods-info">
                <h5><i class="fas fa-credit-card"></i> Méthodes acceptées :</h5>
                <div class="accepted-methods">
                    <span class="method-badge"><i class="fas fa-mobile-alt"></i> Mobile Money</span>
                    <span class="method-badge"><i class="fas fa-credit-card"></i> Carte Visa/Mastercard</span>
                    <span class="method-badge"><i class="fas fa-university"></i> Virement bancaire</span>
                </div>
            </div>
            
            <div class="payment-reference">
                <p><strong>Référence de paiement :</strong> ${paymentReference}</p>
                <small>Cette référence sera utilisée pour identifier votre paiement.</small>
            </div>
        `;
    }

    // Configurer le bouton "Payer maintenant"
    function setupPayNowButton() {
        const payNowBtn = document.getElementById('pay-now');
        if (payNowBtn) {
            payNowBtn.addEventListener('click', initiatePayment);
        }
    }

    // Initialiser les options de paiement
    function initPaymentOptions() {
        // Sélectionner Mobile Money par défaut
        document.querySelector('input[name="payment_method"][value="mobile_money"]').checked = true;
    }

    // ========== GESTION DU PAIEMENT ==========

    // Générer une référence de paiement
    function generatePaymentReference() {
        const timestamp = Date.now().toString().slice(-6);
        const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        return `SALACOPE-${timestamp}${random}`;
    }

    // Initialiser le paiement
    function initiatePayment() {
        if (!selectedPaymentMethod) {
            showNotification('Veuillez sélectionner une méthode de paiement', 'error');
            return;
        }
        
        // Valider les données de paiement
        if (!validatePaymentData()) {
            showNotification('Données de paiement incomplètes', 'error');
            return;
        }
        
        // Afficher le modal de paiement
        showPaymentModal();
    }

    // Valider les données de paiement
    function validatePaymentData() {
        return selectedPlan && 
               selectedPaymentMethod && 
               formData.payment.amount > 0 &&
               paymentReference;
    }

    // Afficher le modal de paiement
    function showPaymentModal() {
        // Créer le modal
        const modal = document.createElement('div');
        modal.className = 'payment-modal';
        modal.id = 'paymentModal';
        
        const { planName, price, credits } = calculateSubscriptionDetails();
        
        modal.innerHTML = `
            <div class="modal-overlay"></div>
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-lock"></i> Paiement sécurisé</h3>
                    <button class="modal-close" id="closeModal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <div class="payment-summary-modal">
                        <h4>Récapitulatif de votre commande</h4>
                        <div class="summary-details">
                            <p><strong>Formule :</strong> ${planName}</p>
                            <p><strong>Durée :</strong> ${selectedDuration} mois</p>
                            <p><strong>Crédits :</strong> ${credits}</p>
                            <p><strong>Montant :</strong> ${price.toLocaleString()} ${CONFIG.CURRENCY}</p>
                            <p><strong>Méthode :</strong> ${getPaymentMethodLabel(selectedPaymentMethod)}</p>
                            <p><strong>Référence :</strong> ${paymentReference}</p>
                        </div>
                    </div>
                    
                    <div class="payment-form" id="paymentForm">
                        ${generatePaymentForm()}
                    </div>
                    
                    <div class="payment-processing" id="paymentProcessing" style="display: none;">
                        <div class="processing-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <h4>Traitement du paiement en cours...</h4>
                        <p>Veuillez ne pas fermer cette fenêtre.</p>
                        <div class="progress-bar">
                            <div class="progress"></div>
                        </div>
                    </div>
                    
                    <div class="payment-success" id="paymentSuccess" style="display: none;">
                        <div class="success-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4>Paiement réussi !</h4>
                        <p>Votre paiement a été traité avec succès.</p>
                        <p><strong>Référence :</strong> ${paymentReference}</p>
                        <button class="btn btn-continue" id="continueAfterPayment">Continuer</button>
                    </div>
                    
                    <div class="payment-error" id="paymentError" style="display: none;">
                        <div class="error-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <h4>Échec du paiement</h4>
                        <p id="errorMessage">Une erreur est survenue lors du traitement.</p>
                        <button class="btn btn-retry" id="retryPayment">Réessayer</button>
                        <button class="btn btn-cancel" id="cancelPayment">Annuler</button>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <p class="security-notice">
                        <i class="fas fa-shield-alt"></i>
                        Transactions sécurisées avec cryptage SSL 256-bit
                    </p>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Ajouter les styles du modal
        addPaymentModalStyles();
        
        // Afficher le modal avec animation
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
        
        // Gestion des événements du modal
        setupPaymentModalEvents();
    }

    // Générer le formulaire de paiement selon la méthode
    function generatePaymentForm() {
        switch(selectedPaymentMethod) {
            case 'mobile_money':
                return `
                    <h5><i class="fas fa-mobile-alt"></i> Paiement Mobile Money</h5>
                    <div class="form-group">
                        <label for="mobileProvider">Opérateur *</label>
                        <select id="mobileProvider" required>
                            <option value="">Sélectionnez votre opérateur</option>
                            <option value="airtel">Airtel Money</option>
                            <option value="orange">Orange Money</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="africell">Africell Money</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="mobileNumber">Numéro de téléphone *</label>
                        <input type="tel" id="mobileNumber" required 
                               placeholder="Ex: +243 81 234 5678">
                    </div>
                    <div class="form-group">
                        <label for="confirmationCode">Code de confirmation</label>
                        <input type="text" id="confirmationCode" 
                               placeholder="Entrez le code reçu par SMS (si requis)">
                    </div>
                    <button type="button" class="btn btn-confirm-payment" id="confirmPayment">
                        Confirmer le paiement
                    </button>
                `;
                
            case 'carte_bancaire':
                return `
                    <h5><i class="fas fa-credit-card"></i> Paiement par carte</h5>
                    <div class="form-group">
                        <label for="cardNumber">Numéro de carte *</label>
                        <input type="text" id="cardNumber" required 
                               placeholder="1234 5678 9012 3456" 
                               maxlength="19">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiryDate">Date d'expiration *</label>
                            <input type="text" id="expiryDate" required 
                                   placeholder="MM/AA" maxlength="5">
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV *</label>
                            <input type="text" id="cvv" required 
                                   placeholder="123" maxlength="4">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cardHolder">Nom sur la carte *</label>
                        <input type="text" id="cardHolder" required 
                               placeholder="Ex: JOHN DOE">
                    </div>
                    <button type="button" class="btn btn-confirm-payment" id="confirmPayment">
                        Payer maintenant
                    </button>
                `;
                
            case 'virement':
                return `
                    <h5><i class="fas fa-university"></i> Virement bancaire</h5>
                    <div class="bank-instructions">
                        <p>Veuillez effectuer un virement avec les informations suivantes :</p>
                        <div class="bank-details">
                            <p><strong>Montant :</strong> ${formData.payment.amount.toLocaleString()} ${CONFIG.CURRENCY}</p>
                            <p><strong>Référence :</strong> ${paymentReference}</p>
                            <p><strong>Bénéficiaire :</strong> SALACOPE SARL</p>
                            <p><strong>Communication :</strong> Abonnement ${selectedPlan} - ${paymentReference}</p>
                        </div>
                        <p class="instruction-note">
                            Après avoir effectué le virement, cliquez sur "Confirmer le virement".
                            Votre compte sera activé après vérification du paiement.
                        </p>
                    </div>
                    <button type="button" class="btn btn-confirm-payment" id="confirmPayment">
                        J'ai effectué le virement
                    </button>
                `;
                
            default:
                return '';
        }
    }

    // Configurer les événements du modal de paiement
    function setupPaymentModalEvents() {
        // Fermer le modal
        document.getElementById('closeModal').addEventListener('click', closePaymentModal);
        document.querySelector('.modal-overlay').addEventListener('click', closePaymentModal);
        
        // Confirmer le paiement
        document.getElementById('confirmPayment').addEventListener('click', processPayment);
        
        // Continuer après paiement réussi
        document.getElementById('continueAfterPayment')?.addEventListener('click', function() {
            closePaymentModal();
            goToStep(4);
        });
        
        // Réessayer après échec
        document.getElementById('retryPayment')?.addEventListener('click', function() {
            document.getElementById('paymentError').style.display = 'none';
            document.getElementById('paymentForm').style.display = 'block';
        });
        
        // Annuler après échec
        document.getElementById('cancelPayment')?.addEventListener('click', closePaymentModal);
        
        // Formater le numéro de carte
        const cardNumberInput = document.getElementById('cardNumber');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', formatCardNumber);
        }
        
        // Formater la date d'expiration
        const expiryInput = document.getElementById('expiryDate');
        if (expiryInput) {
            expiryInput.addEventListener('input', formatExpiryDate);
        }
        
        // Empêcher la fermeture avec Échap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                e.preventDefault();
            }
        });
    }

    // Traiter le paiement
    async function processPayment() {
        // Valider le formulaire de paiement
        if (!validatePaymentForm()) {
            showNotification('Veuillez remplir tous les champs obligatoires', 'error');
            return;
        }
        
        // Récupérer les données de paiement
        const paymentData = collectPaymentData();
        
        // Afficher l'écran de traitement
        document.getElementById('paymentForm').style.display = 'none';
        document.getElementById('paymentProcessing').style.display = 'block';
        
        // Simuler un traitement (À REMPLACER par l'appel API réel)
        try {
            // Ici, vous appelleriez votre API de paiement
            // const response = await callPaymentAPI(paymentData);
            
            // Pour l'exemple, nous simulons un délai
            await simulatePaymentProcessing();
            
            // Paiement réussi
            showPaymentSuccess();
            
        } catch (error) {
            // Échec du paiement
            showPaymentError(error.message);
        }
    }

    // Valider le formulaire de paiement
    function validatePaymentForm() {
        switch(selectedPaymentMethod) {
            case 'mobile_money':
                const provider = document.getElementById('mobileProvider').value;
                const mobileNumber = document.getElementById('mobileNumber').value;
                return provider && mobileNumber && isValidPhone(mobileNumber);
                
            case 'carte_bancaire':
                const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
                const expiryDate = document.getElementById('expiryDate').value;
                const cvv = document.getElementById('cvv').value;
                const cardHolder = document.getElementById('cardHolder').value;
                
                return cardNumber.length >= 16 && 
                       expiryDate.length === 5 &&
                       cvv.length >= 3 &&
                       cardHolder.trim().length > 0;
                
            case 'virement':
                return true; // Toujours valide car confirmation manuelle
                
            default:
                return false;
        }
    }

    // Collecter les données de paiement
    function collectPaymentData() {
        const baseData = {
            reference: paymentReference,
            amount: formData.payment.amount,
            currency: CONFIG.CURRENCY,
            plan: selectedPlan,
            duration: selectedDuration,
            method: selectedPaymentMethod,
            customer: selectedAccountType === 'acheteur' ? formData.acheteur : formData.vendeur
        };
        
        switch(selectedPaymentMethod) {
            case 'mobile_money':
                return {
                    ...baseData,
                    provider: document.getElementById('mobileProvider').value,
                    phoneNumber: document.getElementById('mobileNumber').value,
                    confirmationCode: document.getElementById('confirmationCode').value || null
                };
                
            case 'carte_bancaire':
                return {
                    ...baseData,
                    cardNumber: document.getElementById('cardNumber').value.replace(/\s/g, ''),
                    expiryDate: document.getElementById('expiryDate').value,
                    cvv: document.getElementById('cvv').value,
                    cardHolder: document.getElementById('cardHolder').value
                };
                
            case 'virement':
                return {
                    ...baseData,
                    confirmation: 'pending'
                };
                
            default:
                return baseData;
        }
    }

    // Simuler le traitement du paiement (À REMPLACER)
    async function simulatePaymentProcessing() {
        return new Promise((resolve, reject) => {
            // Simuler un délai de traitement
            const processingTime = Math.random() * 3000 + 2000; // 2-5 secondes
            
            setTimeout(() => {
                // Simuler un succès 90% du temps
                if (Math.random() < 0.9) {
                    resolve({ success: true, transactionId: `TXN-${Date.now()}` });
                } else {
                    reject(new Error('Échec du traitement du paiement'));
                }
            }, processingTime);
        });
    }

    // Appeler l'API de paiement (EXEMPLE - À ADAPTER)
    async function callPaymentAPI(paymentData) {
        let endpoint = '';
        
        switch(paymentData.method) {
            case 'mobile_money':
                endpoint = CONFIG.PAYMENT_API.MOBILE_MONEY;
                break;
            case 'carte_bancaire':
                endpoint = CONFIG.PAYMENT_API.CARD;
                break;
            case 'virement':
                endpoint = CONFIG.PAYMENT_API.BANK_TRANSFER;
                break;
        }
        
        const response = await fetch(`${CONFIG.API_BASE_URL}${endpoint}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer YOUR_API_KEY' // À configurer
            },
            body: JSON.stringify(paymentData)
        });
        
        if (!response.ok) {
            throw new Error('Erreur lors du traitement du paiement');
        }
        
        return await response.json();
    }

    // Afficher le succès du paiement
    function showPaymentSuccess() {
        document.getElementById('paymentProcessing').style.display = 'none';
        document.getElementById('paymentSuccess').style.display = 'block';
        
        // Mettre à jour les données de paiement
        formData.payment.status = 'success';
        formData.payment.paidAt = new Date().toISOString();
        
        // Sauvegarder dans le localStorage (temporaire)
        localStorage.setItem('salacope_payment_reference', paymentReference);
    }

    // Afficher l'erreur de paiement
    function showPaymentError(errorMessage) {
        document.getElementById('paymentProcessing').style.display = 'none';
        document.getElementById('paymentError').style.display = 'block';
        document.getElementById('errorMessage').textContent = errorMessage;
        
        // Mettre à jour les données de paiement
        formData.payment.status = 'failed';
        formData.payment.error = errorMessage;
    }

    // Fermer le modal de paiement
    function closePaymentModal() {
        const modal = document.getElementById('paymentModal');
        if (modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.remove();
            }, 300);
        }
    }

    // Obtenir le libellé de la méthode de paiement
    function getPaymentMethodLabel(method) {
        const labels = {
            'mobile_money': 'Mobile Money',
            'carte_bancaire': 'Carte Bancaire',
            'virement': 'Virement Bancaire'
        };
        return labels[method] || method;
    }

    // Formater le numéro de carte
    function formatCardNumber(e) {
        let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/g, '');
        
        if (value.length > 16) {
            value = value.substring(0, 16);
        }
        
        // Ajouter des espaces tous les 4 chiffres
        value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
        
        e.target.value = value;
    }

    // Formater la date d'expiration
    function formatExpiryDate(e) {
        let value = e.target.value.replace(/[^0-9]/g, '');
        
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        
        e.target.value = value.substring(0, 5);
    }

    // ========== GESTION DES MOTS DE PASSE ==========

    // Valider la force du mot de passe
    function validatePassword(password) {
        const rules = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[^A-Za-z0-9]/.test(password)
        };
        
        // Mettre à jour les exigences
        passwordRequirements.forEach(req => {
            const rule = req.getAttribute('data-rule');
            if (rules[rule]) {
                req.classList.add('valid');
            } else {
                req.classList.remove('valid');
            }
        });
        
        // Mettre à jour la barre de force
        updatePasswordStrengthBar(rules);
    }

    // Mettre à jour la barre de force du mot de passe
    function updatePasswordStrengthBar(rules) {
        const validCount = Object.values(rules).filter(Boolean).length;
        const strengthBar = document.querySelector('.strength-bar');
        const strengthText = document.querySelector('.strength-text');
        
        let strength = 'faible';
        let color = '#e53935';
        let width = '20%';
        
        if (validCount >= 4) {
            strength = 'fort';
            color = '#4CAF50';
            width = '100%';
        } else if (validCount >= 2) {
            strength = 'moyen';
            color = '#ff9800';
            width = '60%';
        }
        
        if (strengthBar) {
            strengthBar.style.width = width;
            strengthBar.style.background = color;
        }
        
        if (strengthText) {
            strengthText.textContent = `Force du mot de passe : ${strength}`;
            strengthText.style.color = color;
        }
    }

    // Afficher/masquer le mot de passe
    function togglePasswordVisibility(e) {
        const button = e.currentTarget;
        const input = button.parentElement.querySelector('input');
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // ========== SAUVEGARDE DES DONNÉES ==========

    // Sauvegarder les données du formulaire
    function saveFormData() {
        if (selectedAccountType === 'acheteur') {
            formData.acheteur = {
                nom: document.getElementById('nom').value,
                postnom: document.getElementById('postnom').value,
                prenom: document.getElementById('prenom').value,
                telephone: document.getElementById('telephone').value,
                email: document.getElementById('email').value
            };
        } else {
            formData.vendeur = {
                entreprise: {
                    nom: document.getElementById('nom_entreprise').value,
                    telephone: document.getElementById('telephone_entreprise').value,
                    email: document.getElementById('email_entreprise').value,
                    adresse: document.getElementById('adresse_entreprise').value,
                    type: document.getElementById('type_entreprise').value
                },
                proprietaire: {
                    nom: document.getElementById('nom_proprietaire').value,
                    postnom: document.getElementById('postnom_proprietaire').value,
                    prenom: document.getElementById('prenom_proprietaire').value,
                    fonction: document.getElementById('fonction_proprietaire').value
                }
            };
        }
        
        // Sauvegarder le mot de passe
        formData.password = passwordInput.value;
    }

    // Sauvegarder les données d'abonnement
    function saveSubscriptionData() {
        formData.subscription = {
            plan: selectedPlan,
            duration: selectedDuration,
            paymentMethod: selectedPaymentMethod
        };
    }

    // ========== RÉSUMÉ ET CONFIRMATION ==========

    // Mettre à jour le résumé
    function updateSummary() {
        updateAccountTypeSummary();
        
        if (selectedAccountType === 'acheteur') {
            updateAcheteurSummary();
        } else {
            updateVendeurSummary();
            updateSubscriptionSummary();
        }
    }

    // Mettre à jour le type de compte dans le résumé
    function updateAccountTypeSummary() {
        const accountTypeElement = document.getElementById('summary-account-type');
        accountTypeElement.textContent = selectedAccountType === 'acheteur' 
            ? 'Compte Acheteur (Gratuit)' 
            : 'Compte Vendeur';
    }

    // Mettre à jour le résumé pour acheteur
    function updateAcheteurSummary() {
        document.getElementById('summary-personal-info').style.display = 'block';
        document.getElementById('summary-business-info').style.display = 'none';
        document.getElementById('summary-subscription-info').style.display = 'none';
        
        const personalDetails = document.querySelector('#summary-personal-info .summary-details');
        personalDetails.innerHTML = `
            <p><strong>Nom :</strong> ${formData.acheteur.nom}</p>
            <p><strong>Post-nom :</strong> ${formData.acheteur.postnom}</p>
            <p><strong>Prénom :</strong> ${formData.acheteur.prenom}</p>
            <p><strong>Téléphone :</strong> ${formData.acheteur.telephone}</p>
            <p><strong>Email :</strong> ${formData.acheteur.email}</p>
        `;
    }

    // Mettre à jour le résumé pour vendeur
    function updateVendeurSummary() {
        document.getElementById('summary-personal-info').style.display = 'none';
        document.getElementById('summary-business-info').style.display = 'block';
        document.getElementById('summary-subscription-info').style.display = 'block';
        
        const businessDetails = document.querySelector('#summary-business-info .summary-details');
        businessDetails.innerHTML = `
            <p><strong>Entreprise :</strong> ${formData.vendeur.entreprise.nom}</p>
            <p><strong>Type :</strong> ${formData.vendeur.entreprise.type}</p>
            <p><strong>Adresse :</strong> ${formData.vendeur.entreprise.adresse}</p>
            <p><strong>Téléphone :</strong> ${formData.vendeur.entreprise.telephone}</p>
            <p><strong>Email :</strong> ${formData.vendeur.entreprise.email}</p>
            <p><strong>Propriétaire :</strong> ${formData.vendeur.proprietaire.prenom} ${formData.vendeur.proprietaire.nom}</p>
            <p><strong>Fonction :</strong> ${formData.vendeur.proprietaire.fonction}</p>
        `;
    }

    // Mettre à jour le résumé de l'abonnement
    function updateSubscriptionSummary() {
        const subscriptionDetails = document.querySelector('#summary-subscription-info .summary-details');
        const { planName, price, credits } = calculateSubscriptionDetails();
        
        let subscriptionHTML = `
            <p><strong>Formule :</strong> ${planName}</p>
            <p><strong>Durée :</strong> ${selectedDuration} mois</p>
            <p><strong>Crédits :</strong> ${credits} crédits</p>
            <p><strong>Prix :</strong> ${price.toLocaleString()} ${CONFIG.CURRENCY}</p>
        `;
        
        if (selectedPlan !== 'essai') {
            subscriptionHTML += `
                <p><strong>Méthode de paiement :</strong> ${getPaymentMethodLabel(selectedPaymentMethod)}</p>
                ${formData.payment.status === 'success' ? 
                    `<p><strong>Statut :</strong> <span class="status-success">Payé</span></p>
                     <p><strong>Référence :</strong> ${paymentReference}</p>` : 
                    `<p><strong>Statut :</strong> <span class="status-pending">Paiement requis</span></p>`}
            `;
        }
        
        subscriptionDetails.innerHTML = subscriptionHTML;
    }

    // ========== SOUMISSION DU FORMULAIRE ==========

    // Soumettre le formulaire
    async function submitForm(e) {
        e.preventDefault();
        
        if (!termsCheckbox.checked) {
            showNotification('Veuillez accepter les conditions d\'utilisation', 'error');
            return;
        }
        
        // Pour les vendeurs avec abonnement payant, vérifier le paiement
        if (selectedAccountType === 'vendeur' && selectedPlan !== 'essai' && formData.payment.status !== 'success') {
            showNotification('Veuillez compléter le paiement avant de finaliser l\'inscription', 'error');
            return;
        }
        
        // Afficher l'indicateur de chargement
        showLoading(true);
        
        try {
            // Préparer les données à envoyer
            const registrationData = prepareRegistrationData();
            
            // Envoyer les données au serveur
            const response = await sendRegistrationData(registrationData);
            
            if (response.success) {
                showNotification('Compte créé avec succès !', 'success');
                
                // Rediriger vers la page de connexion après 2 secondes
                setTimeout(() => {
                    window.location.href = './login.php';
                }, 2000);
            } else {
                throw new Error(response.message || 'Erreur lors de la création du compte');
            }
            
        } catch (error) {
            showNotification(error.message, 'error');
            showLoading(false);
        }
    }

    // Préparer les données d'inscription
    function prepareRegistrationData() {
        const data = {
            accountType: selectedAccountType,
            subscription: formData.subscription,
            payment: formData.payment,
            timestamp: new Date().toISOString()
        };
        
        if (selectedAccountType === 'acheteur') {
            data.user = formData.acheteur;
        } else {
            data.user = formData.vendeur;
        }
        
        return data;
    }

    // Envoyer les données d'inscription
    async function sendRegistrationData(data) {
        // ICI : Remplacer par votre appel API réel
        console.log('Données d\'inscription à envoyer:', data);
        
        // Simulation d'appel API
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve({
                    success: true,
                    message: 'Compte créé avec succès',
                    userId: `USER-${Date.now()}`,
                    reference: paymentReference
                });
            }, 1500);
        });
    }

    // ========== UTILITAIRES ==========

    // Afficher une erreur
    function showError(input, message) {
        const formGroup = input.closest('.form-group');
        formGroup.classList.add('error');
        const errorMsg = formGroup.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.textContent = message;
            errorMsg.style.display = 'block';
        }
    }

    // Effacer toutes les erreurs
    function clearErrors() {
        document.querySelectorAll('.form-group').forEach(group => {
            group.classList.remove('error');
            const errorMsg = group.querySelector('.error-message');
            if (errorMsg) {
                errorMsg.textContent = '';
                errorMsg.style.display = 'none';
            }
        });
    }

    // Afficher une notification
    function showNotification(message, type = 'info') {
        // Créer l'élément de notification
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Ajouter au body
        document.body.appendChild(notification);
        
        // Animer l'entrée
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        // Fermer la notification
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        });
        
        // Fermer automatiquement après 5 secondes
        setTimeout(() => {
            if (notification.parentNode) {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }
        }, 5000);
    }

    // Ajouter les styles pour les notifications
    function addNotificationStyles() {
        if (document.getElementById('notification-styles')) return;
        
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                background: white;
                border-radius: 8px;
                padding: 15px 20px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                display: flex;
                align-items: center;
                justify-content: space-between;
                min-width: 300px;
                max-width: 400px;
                transform: translateX(400px);
                transition: transform 0.3s ease;
                z-index: 9999;
                border-left: 4px solid #4CAF50;
            }
            
            .notification.show {
                transform: translateX(0);
            }
            
            .notification-error {
                border-left-color: #e53935;
            }
            
            .notification-success {
                border-left-color: #4CAF50;
            }
            
            .notification-info {
                border-left-color: #2196F3;
            }
            
            .notification-content {
                display: flex;
                align-items: center;
                gap: 10px;
                flex: 1;
            }
            
            .notification-content i {
                font-size: 18px;
            }
            
            .notification-error .notification-content i {
                color: #e53935;
            }
            
            .notification-success .notification-content i {
                color: #4CAF50;
            }
            
            .notification-info .notification-content i {
                color: #2196F3;
            }
            
            .notification-close {
                background: none;
                border: none;
                color: #999;
                cursor: pointer;
                font-size: 16px;
                padding: 5px;
                margin-left: 10px;
            }
            
            .notification-close:hover {
                color: #333;
            }
            
            @media (max-width: 768px) {
                .notification {
                    left: 20px;
                    right: 20px;
                    max-width: none;
                    min-width: auto;
                }
            }
        `;
        
        document.head.appendChild(style);
    }

    // Ajouter les styles pour le modal de paiement
    function addPaymentModalStyles() {
        if (document.getElementById('payment-modal-styles')) return;
        
        const style = document.createElement('style');
        style.id = 'payment-modal-styles';
        style.textContent = `
            .payment-modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease, visibility 0.3s ease;
            }
            
            .payment-modal.show {
                opacity: 1;
                visibility: visible;
            }
            
            .modal-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(3px);
            }
            
            .modal-content {
                position: relative;
                background: white;
                border-radius: 12px;
                width: 90%;
                max-width: 500px;
                max-height: 90vh;
                overflow-y: auto;
                transform: translateY(20px);
                transition: transform 0.3s ease;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            }
            
            .payment-modal.show .modal-content {
                transform: translateY(0);
            }
            
            .modal-header {
                padding: 20px;
                border-bottom: 1px solid #e0e0e0;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            
            .modal-header h3 {
                margin: 0;
                color: #333;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .modal-header h3 i {
                color: #4CAF50;
            }
            
            .modal-close {
                background: none;
                border: none;
                font-size: 24px;
                color: #999;
                cursor: pointer;
                line-height: 1;
                padding: 0;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                transition: all 0.2s ease;
            }
            
            .modal-close:hover {
                background: #f5f5f5;
                color: #333;
            }
            
            .modal-body {
                padding: 20px;
            }
            
            .payment-summary-modal {
                background: #f9f9f9;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 20px;
            }
            
            .payment-summary-modal h4 {
                margin-top: 0;
                margin-bottom: 15px;
                color: #333;
            }
            
            .summary-details p {
                margin: 8px 0;
                font-size: 14px;
                display: flex;
                justify-content: space-between;
            }
            
            .summary-details strong {
                color: #333;
            }
            
            .payment-form {
                margin: 20px 0;
            }
            
            .payment-form h5 {
                margin-top: 0;
                margin-bottom: 20px;
                color: #333;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .payment-form .form-group {
                margin-bottom: 15px;
            }
            
            .payment-form label {
                display: block;
                margin-bottom: 5px;
                font-weight: 500;
                color: #555;
                font-size: 14px;
            }
            
            .payment-form input,
            .payment-form select {
                width: 100%;
                padding: 10px 12px;
                border: 2px solid #e0e0e0;
                border-radius: 6px;
                font-size: 14px;
                transition: all 0.3s ease;
            }
            
            .payment-form input:focus,
            .payment-form select:focus {
                outline: none;
                border-color: #4CAF50;
            }
            
            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
            }
            
            .bank-instructions {
                background: #f0f9f0;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 20px;
                border-left: 4px solid #4CAF50;
            }
            
            .bank-details {
                background: white;
                padding: 10px;
                border-radius: 6px;
                margin: 10px 0;
            }
            
            .instruction-note {
                font-size: 13px;
                color: #666;
                font-style: italic;
                margin-top: 10px;
            }
            
            .btn-confirm-payment {
                width: 100%;
                padding: 12px;
                background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .btn-confirm-payment:hover {
                background: linear-gradient(135deg, #45a049 0%, #3d8b40 100%);
                transform: translateY(-2px);
            }
            
            .payment-processing,
            .payment-success,
            .payment-error {
                text-align: center;
                padding: 30px 20px;
            }
            
            .processing-spinner {
                font-size: 40px;
                color: #4CAF50;
                margin-bottom: 20px;
            }
            
            .success-icon {
                font-size: 50px;
                color: #4CAF50;
                margin-bottom: 20px;
            }
            
            .error-icon {
                font-size: 50px;
                color: #e53935;
                margin-bottom: 20px;
            }
            
            .progress-bar {
                width: 100%;
                height: 4px;
                background: #e0e0e0;
                border-radius: 2px;
                margin-top: 20px;
                overflow: hidden;
            }
            
            .progress {
                height: 100%;
                background: #4CAF50;
                width: 0;
                animation: progress 2s infinite linear;
            }
            
            @keyframes progress {
                0% { width: 0; transform: translateX(0); }
                50% { width: 70%; transform: translateX(0); }
                100% { width: 70%; transform: translateX(100%); }
            }
            
            .btn-continue,
            .btn-retry,
            .btn-cancel {
                padding: 10px 20px;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                border: none;
                margin: 5px;
            }
            
            .btn-continue {
                background: #4CAF50;
                color: white;
            }
            
            .btn-retry {
                background: #2196F3;
                color: white;
            }
            
            .btn-cancel {
                background: #f5f5f5;
                color: #666;
            }
            
            .modal-footer {
                padding: 15px 20px;
                border-top: 1px solid #e0e0e0;
                text-align: center;
            }
            
            .security-notice {
                font-size: 12px;
                color: #666;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            
            .security-notice i {
                color: #4CAF50;
            }
            
            .status-success {
                color: #4CAF50;
                font-weight: 600;
            }
            
            .status-pending {
                color: #ff9800;
                font-weight: 600;
            }
            
            @media (max-width: 576px) {
                .modal-content {
                    width: 95%;
                }
                
                .form-row {
                    grid-template-columns: 1fr;
                }
            }
        `;
        
        document.head.appendChild(style);
    }

    // Afficher/masquer l'indicateur de chargement
    function showLoading(show) {
        if (show) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création en cours...';
            submitBtn.disabled = true;
        } else {
            submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Finaliser l\'inscription';
            submitBtn.disabled = false;
        }
    }

    // Configurer les écouteurs pour les champs de formulaire
    function setupFormListeners() {
        // Validation en temps réel des champs
        const formInputs = document.querySelectorAll('#inscription-form input[required], #inscription-form select[required]');
        formInputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim()) {
                    clearFieldError(this);
                }
            });
            
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    clearFieldError(this);
                }
            });
        });
    }

    // Effacer l'erreur d'un champ
    function clearFieldError(input) {
        const formGroup = input.closest('.form-group');
        if (formGroup) {
            formGroup.classList.remove('error');
            const errorMsg = formGroup.querySelector('.error-message');
            if (errorMsg) {
                errorMsg.textContent = '';
                errorMsg.style.display = 'none';
            }
        }
    }

    // ========== INITIALISATION ==========
    init();
});