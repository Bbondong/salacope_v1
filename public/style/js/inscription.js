// fichier: ../js/inscription.js - VERSION SIMPLIFIÉE (acheteur seulement)

document.addEventListener('DOMContentLoaded', function() {
    // ========== ÉLÉMENTS DOM ==========
    const steps = document.querySelectorAll('.form-step');
    const progressFill = document.getElementById('progress-fill');
    const accountTypeOptions = document.querySelectorAll('.account-type-option');
    const nextStep1Btn = document.getElementById('next-step-1');
    const nextStep2Btn = document.getElementById('next-step-2');
    const prevBtns = document.querySelectorAll('.btn-prev');
    const acheteurForm = document.getElementById('acheteur-form');
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordRequirements = document.querySelectorAll('.requirement');
    const termsCheckbox = document.getElementById('terms');
    const submitBtn = document.getElementById('submit-form');
    
    // ========== VARIABLES GLOBALES ==========
    let currentStep = 1;
    let selectedAccountType = 'acheteur'; // Seulement acheteur pour l'instant
    let formData = {
        acheteur: {}
    };

    // ========== INITIALISATION ==========
    function init() {
        // Désactiver le bouton suivant pour l'étape 1
        nextStep1Btn.disabled = true;
        submitBtn.disabled = true;
        
        // Sélectionner automatiquement "acheteur"
        selectAccountType('acheteur');
        
        // Initialiser la validation du mot de passe
        validatePassword(passwordInput.value);
        
        // Écouter les changements sur les champs de formulaire
        setupFormListeners();
        
        // Ajouter les styles pour les notifications
        addNotificationStyles();
    }

    // ========== ÉCOUTEURS D'ÉVÉNEMENTS ==========
    
    // Choix du type de compte (désactivé pour vendeur temporairement)
    accountTypeOptions.forEach(option => {
        option.addEventListener('click', function() {
            const type = this.getAttribute('data-type');
            if (type === 'acheteur') {
                selectAccountType(type);
            } else {
                showNotification('Inscription vendeur temporairement désactivée', 'info');
            }
        });
    });

    // Navigation
    nextStep1Btn.addEventListener('click', goToStep2);
    nextStep2Btn.addEventListener('click', goToStep2Next);
    
    prevBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            goToStep(currentStep - 1);
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
        
        // Aller directement à la confirmation
        goToStep(3);
    }

    // Changer d'étape
    function goToStep(step) {
        // Validation des étapes
        if (step < 1 || step > 3) return;
        
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
        const progressPercent = (step - 1) * (100 / 2);
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
    }

    // Mettre à jour les sections spécifiques à chaque étape
    function updateStepSections(step) {
        if (step === 3) {
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
        }
        
        // Validation du mot de passe
        isValid = validatePasswordFields() && isValid;
        
        return isValid;
    }

    // Validation du formulaire acheteur
    function validateAcheteurForm() {
        let isValid = true;
        const fields = ['nom', 'postnom', 'prenom', 'telephone'];
        
        fields.forEach(fieldId => {
            const input = document.getElementById(fieldId);
            if (!input.value.trim()) {
                showError(input, 'Ce champ est obligatoire');
                isValid = false;
            } else if (fieldId === 'telephone' && !isValidPhone(input.value)) {
                showError(input, 'Veuillez entrer un numéro de téléphone valide');
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
        } else if (password.length < 6) {
            showError(passwordInput, 'Le mot de passe doit contenir au moins 6 caractères');
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

    // Validation du téléphone
    function isValidPhone(phone) {
        const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
        return phoneRegex.test(phone);
    }

    // ========== GESTION DES MOTS DE PASSE ==========

    // Valider la force du mot de passe
    function validatePassword(password) {
        const rules = {
            length: password.length >= 6,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password)
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
        
        if (validCount >= 3) {
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
        formData.acheteur = {
            nom: document.getElementById('nom').value,
            postnom: document.getElementById('postnom').value,
            prenom: document.getElementById('prenom').value,
            telephone: document.getElementById('telephone').value
        };
        
        // Sauvegarder le mot de passe
        formData.password = passwordInput.value;
    }

    // ========== RÉSUMÉ ET CONFIRMATION ==========

    // Mettre à jour le résumé
    function updateSummary() {
        updateAccountTypeSummary();
        updateAcheteurSummary();
    }

    // Mettre à jour le type de compte dans le résumé
    function updateAccountTypeSummary() {
        const accountTypeElement = document.getElementById('summary-account-type');
        accountTypeElement.textContent = 'Compte Acheteur (Gratuit)';
    }

    // Mettre à jour le résumé pour acheteur
    function updateAcheteurSummary() {
        document.getElementById('summary-personal-info').style.display = 'block';
        
        const personalDetails = document.querySelector('#summary-personal-info .summary-details');
        personalDetails.innerHTML = `
            <p><strong>Nom :</strong> ${formData.acheteur.nom}</p>
            <p><strong>Post-nom :</strong> ${formData.acheteur.postnom}</p>
            <p><strong>Prénom :</strong> ${formData.acheteur.prenom}</p>
            <p><strong>Téléphone :</strong> ${formData.acheteur.telephone}</p>
        `;
    }

    // ========== SOUMISSION DU FORMULAIRE ==========

    // Soumettre le formulaire
    async function submitForm(e) {
        e.preventDefault();
        
        if (!termsCheckbox.checked) {
            showNotification('Veuillez accepter les conditions d\'utilisation', 'error');
            return;
        }
        
        // Afficher l'indicateur de chargement
        showLoading(true);
        
        try {
            // Préparer les données à envoyer
            const registrationData = prepareRegistrationData();
            
            // ENVOI RÉEL AU SERVEUR
            const response = await sendRegistrationData(registrationData);
            
            if (response.success) {
                showNotification('Compte créé avec succès !', 'success');
                
                // Rediriger vers la page de connexion après 2 secondes
                setTimeout(() => {
                    window.location.href = response.redirect || '../clients/index.php';
                }, 2000);
            } else {
                throw new Error(response.message || 'Erreur lors de la création du compte');
            }
            
        } catch (error) {
            console.error('Erreur détaillée:', error);
            showNotification(error.message, 'error');
            showLoading(false);
        }
    }

    // Préparer les données d'inscription
    function prepareRegistrationData() {
        return {
            accountType: 'acheteur',
            password: passwordInput.value,
            user: {
                nom: document.getElementById('nom').value,
                postnom: document.getElementById('postnom').value,
                prenom: document.getElementById('prenom').value,
                telephone: document.getElementById('telephone').value
            }
        };
    }

    // Envoyer les données d'inscription
    async function sendRegistrationData(data) {
        try {
            const baseUrl = window.location.origin;
            const API_URL = `${baseUrl}/backend/auth/inscription.php`;
            
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Erreur HTTP ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                return result;
            } else {
                throw new Error(result.message || 'Erreur lors de l\'inscription');
            }
            
        } catch (error) {
            console.error('Erreur lors de l\'inscription:', error);
            
            // Messages d'erreur personnalisés
            if (error.message.includes('network') || error.message.includes('Network')) {
                throw new Error('Problème de connexion. Vérifiez votre connexion internet.');
            } else if (error.message.includes('téléphone est déjà utilisé')) {
                throw new Error('Ce numéro de téléphone est déjà utilisé.');
            } else {
                throw new Error('Impossible de créer le compte. Veuillez réessayer.');
            }
        }
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