// fichier: ../style/js/inscription.js
// VERSION COMPLÈTE — avec acheteur, vendeur et ouverture WhatsApp automatique

document.addEventListener('DOMContentLoaded', function () {

    /* ===================== ÉLÉMENTS DOM ===================== */
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
    const durationOptions = document.querySelectorAll('.duration-option input');
    const paymentSection = document.getElementById('payment-section');
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');

    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordRequirements = document.querySelectorAll('.requirement');
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');

    const termsCheckbox = document.getElementById('terms');
    const submitBtn = document.getElementById('submit-form');

    /* ===================== VARIABLES ===================== */
    let currentStep = 1;
    let selectedAccountType = 'acheteur';
    let selectedPlan = null;
    let selectedDuration = '1';
    let selectedPaymentMethod = 'mobile_money';

    let formData = {
        acheteur: {},
        vendeur: {
            entreprise: {},
            proprietaire: {}
        }
    };

    /* ===================== INITIALISATION ===================== */
    function init() {
        nextStep1Btn.disabled = true;
        nextStep3Btn.disabled = true;
        submitBtn.disabled = true;

        // Afficher le formulaire acheteur par défaut
        showAccountForm(selectedAccountType);

        // Écouteurs pour les options d'abonnement
        setupSubscriptionListeners();
        setupPaymentListeners();
        setupFormListeners();
        validatePassword('');
    }

    /* ===================== TYPE DE COMPTE ===================== */
    accountTypeOptions.forEach(option => {
        option.addEventListener('click', function () {
            const type = this.dataset.type;
            selectAccountType(type);
        });
    });

    function selectAccountType(type) {
        accountTypeOptions.forEach(opt => opt.classList.remove('selected'));
        document.querySelector(`.account-type-option[data-type="${type}"]`).classList.add('selected');

        selectedAccountType = type;
        nextStep1Btn.disabled = false;

        // Afficher le formulaire correspondant
        showAccountForm(type);

        // Gérer l'affichage de l'étape abonnement
        const step3Indicator = document.querySelector('.step[data-step="3"]');
        if (step3Indicator) {
            step3Indicator.style.display = type === 'vendeur' ? 'flex' : 'none';
        }
    }

    function showAccountForm(type) {
        if (type === 'acheteur') {
            acheteurForm.classList.add('active');
            vendeurForm.classList.remove('active');
        } else {
            acheteurForm.classList.remove('active');
            vendeurForm.classList.add('active');
        }
    }

    /* ===================== NAVIGATION ===================== */
    nextStep1Btn.addEventListener('click', () => {
        goToStep(2);
        // Si vendeur, on réinitialise la sélection d'abonnement
        if (selectedAccountType === 'vendeur') {
            resetSubscriptionSelection();
        }
    });

    nextStep2Btn.addEventListener('click', () => {
        if (!validateStep2()) {
            showNotification('Veuillez corriger les erreurs dans le formulaire', 'error');
            return;
        }
        saveFormData();
        
        // Si acheteur, on saute à l'étape de confirmation
        if (selectedAccountType === 'acheteur') {
            goToStep(4);
        } else {
            goToStep(3);
        }
    });

    nextStep3Btn.addEventListener('click', () => {
        if (selectedAccountType === 'vendeur' && !selectedPlan) {
            showNotification('Veuillez sélectionner un plan d\'abonnement', 'error');
            return;
        }
        goToStep(4);
    });

    prevBtns.forEach(btn => {
        btn.addEventListener('click', () => goToStep(currentStep - 1));
    });

    function goToStep(step) {
        if (step < 1 || step > 4) return;

        // Si vendeur va à l'étape 4 sans plan, on le redirige à l'étape 3
        if (step === 4 && selectedAccountType === 'vendeur' && !selectedPlan) {
            showNotification('Veuillez d\'abord sélectionner un plan d\'abonnement', 'error');
            goToStep(3);
            return;
        }

        document.getElementById(`step-${currentStep}`).classList.remove('active');
        document.getElementById(`step-${step}`).classList.add('active');

        currentStep = step;
        updateProgress(step);

        if (step === 4) {
            updateSummary();
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function updateProgress(step) {
        document.querySelectorAll('.step').forEach(s => {
            const n = parseInt(s.dataset.step);
            // Pour les acheteurs, on ignore l'étape 3 dans la progression
            if (selectedAccountType === 'acheteur' && n === 3) return;
            s.classList.toggle('active', n <= step);
        });

        // Calcul du pourcentage de progression
        let totalSteps = selectedAccountType === 'acheteur' ? 3 : 4;
        let adjustedStep = selectedAccountType === 'acheteur' && step > 2 ? step - 1 : step;
        let percent = ((adjustedStep - 1) / (totalSteps - 1)) * 100;
        progressFill.style.width = `${percent}%`;
    }

    /* ===================== VALIDATION ÉTAPE 2 ===================== */
    function validateStep2() {
        clearErrors();
        let valid = true;

        if (selectedAccountType === 'acheteur') {
            // Validation acheteur
            ['nom', 'postnom', 'prenom', 'telephone'].forEach(id => {
                const input = document.getElementById(id);
                if (!input.value.trim()) {
                    showError(input, 'Ce champ est obligatoire');
                    valid = false;
                }
            });

            // Validation téléphone
            const telInput = document.getElementById('telephone');
            if (telInput.value.trim() && !validatePhoneNumber(telInput.value)) {
                showError(telInput, 'Format invalide. Ex: +243 81 123 4567');
                valid = false;
            }
        } else {
            // Validation vendeur - informations entreprise
            ['nom_entreprise', 'telephone_entreprise', 'email_entreprise', 'adresse_entreprise'].forEach(id => {
                const input = document.getElementById(id);
                if (!input.value.trim()) {
                    showError(input, 'Ce champ est obligatoire');
                    valid = false;
                }
            });

            // Validation vendeur - informations propriétaire
            ['nom_proprietaire', 'postnom_proprietaire', 'prenom_proprietaire', 'fonction_proprietaire'].forEach(id => {
                const input = document.getElementById(id);
                if (!input.value.trim()) {
                    showError(input, 'Ce champ est obligatoire');
                    valid = false;
                }
            });

            // Validation email entreprise
            const emailInput = document.getElementById('email_entreprise');
            if (emailInput.value.trim() && !validateEmail(emailInput.value)) {
                showError(emailInput, 'Format d\'email invalide');
                valid = false;
            }

            // Validation téléphone entreprise
            const telEntrepriseInput = document.getElementById('telephone_entreprise');
            if (telEntrepriseInput.value.trim() && !validatePhoneNumber(telEntrepriseInput.value)) {
                showError(telEntrepriseInput, 'Format invalide. Ex: +243 81 123 4567');
                valid = false;
            }
        }

        // Validation mot de passe (commun aux deux types)
        if (!validatePasswordFields()) valid = false;
        return valid;
    }

    function validatePhoneNumber(phone) {
        // Format congolais: +243 suivi de 9 chiffres
        const regex = /^\+243\s?\d{2}\s?\d{3}\s?\d{4}$/;
        return regex.test(phone);
    }

    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    function validatePasswordFields() {
        let valid = true;

        // Règles de mot de passe
        const password = passwordInput.value;
        const rules = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };

        // Vérifier toutes les règles
        const allRulesValid = Object.values(rules).every(rule => rule);
        if (!allRulesValid) {
            showError(passwordInput, 'Le mot de passe ne respecte pas toutes les exigences');
            valid = false;
        }

        // Confirmation du mot de passe
        if (password !== confirmPasswordInput.value) {
            showError(confirmPasswordInput, 'Les mots de passe ne correspondent pas');
            valid = false;
        }

        return valid;
    }

    /* ===================== MOT DE PASSE ===================== */
    passwordInput.addEventListener('input', () => {
        validatePassword(passwordInput.value);
        updatePasswordStrength(passwordInput.value);
    });

    function validatePassword(password) {
        const rules = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };

        passwordRequirements.forEach(req => {
            req.classList.toggle('valid', rules[req.dataset.rule]);
        });
    }

    function updatePasswordStrength(password) {
        const strengthBar = document.querySelector('.strength-bar');
        const strengthText = document.querySelector('.strength-text');
        
        let strength = 0;
        if (password.length >= 8) strength += 20;
        if (/[A-Z]/.test(password)) strength += 20;
        if (/[a-z]/.test(password)) strength += 20;
        if (/[0-9]/.test(password)) strength += 20;
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 20;
        
        strengthBar.style.width = `${strength}%`;
        
        // Couleur selon la force
        if (strength <= 40) {
            strengthBar.style.background = '#e53935';
            strengthText.textContent = 'Faible';
        } else if (strength <= 60) {
            strengthBar.style.background = '#ff9800';
            strengthText.textContent = 'Moyen';
        } else if (strength <= 80) {
            strengthBar.style.background = '#ffb300';
            strengthText.textContent = 'Bon';
        } else {
            strengthBar.style.background = '#4CAF50';
            strengthText.textContent = 'Fort';
        }
    }

    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    });

    /* ===================== ABONNEMENT (Vendeur uniquement) ===================== */
    function setupSubscriptionListeners() {
        // Boutons de sélection de plan
        selectPlanBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const plan = this.dataset.plan;
                selectPlan(plan);
            });
        });

        // Options de durée
        durationOptions.forEach(option => {
            option.addEventListener('change', function () {
                if (this.checked) {
                    selectedDuration = this.value;
                    updatePaymentSection();
                }
            });
        });
    }

    function selectPlan(plan) {
        selectedPlan = plan;
        
        // Mettre à jour l'affichage des options
        subscriptionOptions.forEach(option => {
            option.classList.remove('selected');
        });
        document.querySelector(`.subscription-option[data-plan="${plan}"]`).classList.add('selected');
        
        // Activer le bouton suivant
        nextStep3Btn.disabled = false;
        
        // Afficher la section paiement
        paymentSection.style.display = 'block';
        updatePaymentSection();
        
        showNotification(`Plan ${plan} sélectionné`, 'success');
    }

    function resetSubscriptionSelection() {
        selectedPlan = null;
        subscriptionOptions.forEach(option => option.classList.remove('selected'));
        paymentSection.style.display = 'none';
        nextStep3Btn.disabled = true;
    }

    function getPlanPrice(plan, duration) {
        const prices = {
            essai: {1: 0},
            standard: {1: 7000, 3: 19000, 6: 35000, 12: 65000},
            premium: {1: 9000, 3: 25000, 6: 45000, 12: 85000}
        };
        return prices[plan]?.[duration] || 0;
    }

    /* ===================== PAIEMENT ===================== */
    function setupPaymentListeners() {
        paymentMethods.forEach(method => {
            method.addEventListener('change', function () {
                if (this.checked) {
                    selectedPaymentMethod = this.value;
                    updatePaymentDetails();
                }
            });
        });
    }

    function updatePaymentSection() {
        if (!selectedPlan) return;
        
        // Mettre à jour l'affichage du prix
        const price = getPlanPrice(selectedPlan, selectedDuration);
        const planName = {
            essai: 'Essai Gratuit',
            standard: 'Standard',
            premium: 'Premium'
        }[selectedPlan];
        
        const durationText = {
            '1': '1 mois',
            '3': '3 mois',
            '6': '6 mois',
            '12': '12 mois'
        }[selectedDuration];
        
        updatePaymentDetails();
    }

    function updatePaymentDetails() {
        const detailsContainer = document.getElementById('payment-details');
        const price = getPlanPrice(selectedPlan, selectedDuration);
        
        let details = '';
        if (selectedPaymentMethod === 'mobile_money') {
            details = `
                <h4>Instructions pour Mobile Money</h4>
                <p>Veuillez effectuer le paiement de <strong>${price} FC</strong> vers :</p>
                <ul>
                    <li><strong>Airtel Money:</strong> +243 81 234 5678</li>
                    <li><strong>Orange Money:</strong> +243 85 876 5432</li>
                    <li><strong>M-Pesa:</strong> +243 89 123 4567</li>
                </ul>
                <p>Indiquez dans le message : <code>${selectedPlan.toUpperCase()}${selectedDuration}</code></p>
            `;
        } else if (selectedPaymentMethod === 'carte_bancaire') {
            details = `
                <h4>Paiement par carte bancaire</h4>
                <p>Montant à payer : <strong>${price} FC</strong></p>
                <p>Vous serez redirigé vers notre plateforme de paiement sécurisée.</p>
            `;
        } else if (selectedPaymentMethod === 'virement') {
            details = `
                <h4>Virement bancaire</h4>
                <p>Veuillez effectuer un virement de <strong>${price} FC</strong> vers :</p>
                <p><strong>Banque:</strong> Rawbank</p>
                <p><strong>IBAN:</strong> CD78 1234 5678 9012 3456 7890</p>
                <p><strong>Bénéficiaire:</strong> SALACOPE SARL</p>
                <p><strong>Référence:</strong> ${selectedPlan.toUpperCase()}${selectedDuration}</p>
            `;
        }
        
        detailsContainer.innerHTML = details;
    }

    /* ===================== DONNÉES ===================== */
    function saveFormData() {
        if (selectedAccountType === 'acheteur') {
            formData.acheteur = {
                nom: document.getElementById('nom').value,
                postnom: document.getElementById('postnom').value,
                prenom: document.getElementById('prenom').value,
                telephone: document.getElementById('telephone').value
            };
        } else {
            formData.vendeur.entreprise = {
                nom_entreprise: document.getElementById('nom_entreprise').value,
                telephone_entreprise: document.getElementById('telephone_entreprise').value,
                email_entreprise: document.getElementById('email_entreprise').value,
                adresse_entreprise: document.getElementById('adresse_entreprise').value
            };
            
            formData.vendeur.proprietaire = {
                nom: document.getElementById('nom_proprietaire').value,
                postnom: document.getElementById('postnom_proprietaire').value,
                prenom: document.getElementById('prenom_proprietaire').value,
                fonction: document.getElementById('fonction_proprietaire').value
            };
        }
        
        formData.password = passwordInput.value;
        formData.accountType = selectedAccountType;
        
        if (selectedAccountType === 'vendeur' && selectedPlan) {
            formData.subscription = {
                plan: selectedPlan,
                duration: selectedDuration,
                price: getPlanPrice(selectedPlan, selectedDuration),
                payment_method: selectedPaymentMethod
            };
        }
    }

    /* ===================== RÉSUMÉ ===================== */
    function updateSummary() {
        // Type de compte
        const accountTypeElement = document.getElementById('summary-account-type');
        accountTypeElement.textContent = selectedAccountType === 'acheteur' 
            ? 'Compte Acheteur (Gratuit)' 
            : 'Compte Vendeur';
        
        // Informations personnelles/entreprise
        if (selectedAccountType === 'acheteur') {
            const container = document.querySelector('#summary-personal-info .summary-details');
            container.innerHTML = `
                <p><strong>Nom :</strong> ${formData.acheteur.nom}</p>
                <p><strong>Post-nom :</strong> ${formData.acheteur.postnom}</p>
                <p><strong>Prénom :</strong> ${formData.acheteur.prenom}</p>
                <p><strong>Téléphone :</strong> ${formData.acheteur.telephone}</p>
            `;
            document.getElementById('summary-business-info').style.display = 'none';
        } else {
            // Informations entreprise
            const businessContainer = document.querySelector('#summary-business-info .summary-details');
            businessContainer.innerHTML = `
                <p><strong>Nom entreprise :</strong> ${formData.vendeur.entreprise.nom_entreprise}</p>
                <p><strong>Téléphone :</strong> ${formData.vendeur.entreprise.telephone_entreprise}</p>
                <p><strong>Email :</strong> ${formData.vendeur.entreprise.email_entreprise}</p>
                <p><strong>Adresse :</strong> ${formData.vendeur.entreprise.adresse_entreprise}</p>
            `;
            document.getElementById('summary-business-info').style.display = 'block';
            
            // Informations propriétaire
            const personalContainer = document.querySelector('#summary-personal-info .summary-details');
            personalContainer.innerHTML = `
                <p><strong>Nom :</strong> ${formData.vendeur.proprietaire.nom}</p>
                <p><strong>Post-nom :</strong> ${formData.vendeur.proprietaire.postnom}</p>
                <p><strong>Prénom :</strong> ${formData.vendeur.proprietaire.prenom}</p>
                <p><strong>Fonction :</strong> ${formData.vendeur.proprietaire.fonction}</p>
            `;
        }
        
        // Informations d'abonnement (vendeur uniquement)
        const subscriptionContainer = document.querySelector('#summary-subscription-info .summary-details');
        if (selectedAccountType === 'vendeur' && formData.subscription) {
            const planNames = {
                essai: 'Essai Gratuit',
                standard: 'Standard',
                premium: 'Premium'
            };
            
            const durationNames = {
                '1': '1 mois',
                '3': '3 mois',
                '6': '6 mois',
                '12': '12 mois'
            };
            
            subscriptionContainer.innerHTML = `
                <p><strong>Formule :</strong> ${planNames[formData.subscription.plan]}</p>
                <p><strong>Durée :</strong> ${durationNames[formData.subscription.duration]}</p>
                <p><strong>Prix :</strong> ${formData.subscription.price} FC</p>
                <p><strong>Méthode de paiement :</strong> ${formData.subscription.payment_method}</p>
            `;
            document.getElementById('summary-subscription-info').style.display = 'block';
        } else {
            document.getElementById('summary-subscription-info').style.display = 'none';
        }
    }

    /* ===================== SOUMISSION ===================== */
    termsCheckbox.addEventListener('change', () => {
        submitBtn.disabled = !termsCheckbox.checked;
    });

    document.getElementById('inscription-form').addEventListener('submit', submitForm);

    async function submitForm(e) {
        e.preventDefault();

        if (!termsCheckbox.checked) {
            showNotification('Veuillez accepter les conditions d\'utilisation', 'error');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création du compte...';

        // DÉTERMINER L'ENDPOINT EN FONCTION DU TYPE DE COMPTE
        let endpoint = '';
        if (selectedAccountType === 'acheteur') {
            endpoint = '../backend/auth/inscription_client.php';
        } else {
            endpoint = '../backend/auth/inscription_vendeur.php';
        }

        // PRÉPARER LES DONNÉES SPÉCIFIQUES POUR CHAQUE ENDPOINT
        let requestData = {};
        
        if (selectedAccountType === 'acheteur') {
            // Format pour inscription_client.php
            requestData = {
                accountType: 'acheteur',
                password: formData.password,
                user: formData.acheteur
            };
        } else {
            // Format pour inscription_vendeur.php
            requestData = {
                accountType: 'vendeur',
                password: formData.password,
                entreprise: formData.vendeur.entreprise,
                proprietaire: formData.vendeur.proprietaire,
                subscription: formData.subscription
            };
        }

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            });

            // Vérifier si la réponse est OK
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                // GESTION DIFFÉRENTE SELON LE TYPE DE COMPTE
                if (selectedAccountType === 'acheteur') {
                    handleAcheteurSuccess(result);
                } else {
                    handleVendeurSuccess(result);
                }
            } else {
                throw new Error(result.message || 'Erreur lors de la création du compte');
            }

        } catch (err) {
            console.error('Erreur détaillée:', err);
            
            let errorMessage = 'Erreur de connexion au serveur';
            if (err.message.includes('HTTP')) {
                errorMessage = 'Erreur de communication avec le serveur';
            } else if (err.message.includes('JSON')) {
                errorMessage = 'Réponse du serveur invalide';
            } else {
                errorMessage = err.message;
            }
            
            showNotification(errorMessage, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Finaliser l\'inscription';
        }
    }

    /* ===================== GESTION SUCCÈS ACHETEUR ===================== */
    function handleAcheteurSuccess(result) {
        showNotification('Compte créé avec succès !', 'success');
        
        // Attendre 1.5 secondes pour que l'utilisateur voie le message
        setTimeout(() => {
            // Vérifier si WhatsApp doit être ouvert
            if (result.whatsapp_redirect && result.whatsapp_redirect.immediate && result.whatsapp_redirect.url) {
                openWhatsApp(result.whatsapp_redirect.url, result);
            } else if (result.verification && result.verification.whatsapp_url) {
                // Compatibilité avec l'ancien format
                openWhatsApp(result.verification.whatsapp_url, result);
            } else {
                // Pas d'URL WhatsApp, redirection normale
                showNotification('Redirection vers la vérification...', 'info');
                setTimeout(() => {
                    window.location.href = '../frontend/double_authen.php';
                }, 2000);
            }
        }, 1500);
    }

    /* ===================== OUVERTURE WHATSAPP ===================== */
    function openWhatsApp(whatsappUrl, result) {
        showNotification('Ouverture de WhatsApp...', 'info');
        
        // Ouvrir WhatsApp dans un nouvel onglet
        const whatsappWindow = window.open(
            whatsappUrl, 
            '_blank',
            'noopener,noreferrer'
        );
        
        // Vérifier si la fenêtre s'est ouverte
        if (!whatsappWindow || whatsappWindow.closed || typeof whatsappWindow.closed === 'undefined') {
            // Popup bloquée par le navigateur
            handlePopupBlocked(whatsappUrl);
        } else {
            // WhatsApp ouvert avec succès
            showNotification('WhatsApp ouvert! Envoyez le message pré-rempli.', 'success');
        }
        
        // Afficher le compte à rebours pour le code
        const waitTime = result.verification?.wait_time || 30;
        startCodeCountdown(waitTime);
        
        // Rediriger vers la page de vérification après délai
        scheduleRedirection(waitTime + 5); // +5 secondes de marge
    }

    /* ===================== GESTION POPUP BLOQUÉE ===================== */
    function handlePopupBlocked(whatsappUrl) {
        showNotification('WhatsApp n\'a pas pu s\'ouvrir automatiquement', 'warning');
        
        // Créer un bouton manuel pour ouvrir WhatsApp
        createManualWhatsAppButton(whatsappUrl);
        
        // Afficher des instructions
        showNotification('Cliquez sur le bouton vert pour ouvrir WhatsApp manuellement', 'info');
    }

    /* ===================== BOUTON MANUEL WHATSAPP ===================== */
    function createManualWhatsAppButton(whatsappUrl) {
        // Créer un conteneur pour le bouton
        const buttonContainer = document.createElement('div');
        buttonContainer.id = 'whatsapp-manual-button-container';
        buttonContainer.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            backdrop-filter: blur(5px);
        `;
        
        // Contenu du bouton
        buttonContainer.innerHTML = `
            <div style="
                background: white;
                padding: 40px;
                border-radius: 20px;
                text-align: center;
                max-width: 500px;
                width: 90%;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            ">
                <div style="margin-bottom: 25px;">
                    <i class="fab fa-whatsapp" style="font-size: 70px; color: #25D366;"></i>
                </div>
                <h2 style="margin-bottom: 15px; color: #333;">Ouvrir WhatsApp</h2>
                <p style="margin-bottom: 25px; color: #666; line-height: 1.6;">
                    Le navigateur a bloqué l'ouverture automatique de WhatsApp.<br>
                    Cliquez sur le bouton ci-dessous pour l'ouvrir manuellement.
                </p>
                <button id="manual-whatsapp-btn" style="
                    background: linear-gradient(135deg, #25D366 0%, #1da851 100%);
                    color: white;
                    border: none;
                    padding: 18px 50px;
                    border-radius: 50px;
                    font-size: 18px;
                    font-weight: bold;
                    cursor: pointer;
                    display: inline-flex;
                    align-items: center;
                    gap: 12px;
                    transition: all 0.3s ease;
                    margin-bottom: 20px;
                ">
                    <i class="fab fa-whatsapp"></i> Ouvrir WhatsApp maintenant
                </button>
                <p style="color: #888; font-size: 14px; margin-top: 15px;">
                    <i class="fas fa-info-circle"></i> Vous recevrez votre code de vérification dans 30 secondes
                </p>
                <button id="close-manual-btn" style="
                    background: none;
                    border: 2px solid #ddd;
                    color: #666;
                    padding: 10px 25px;
                    border-radius: 8px;
                    margin-top: 20px;
                    cursor: pointer;
                    font-size: 14px;
                ">
                    Fermer
                </button>
            </div>
        `;
        
        document.body.appendChild(buttonContainer);
        
        // Ajouter l'événement au bouton WhatsApp
        document.getElementById('manual-whatsapp-btn').addEventListener('click', function() {
            window.open(whatsappUrl, '_blank');
            buttonContainer.remove();
            showNotification('WhatsApp ouvert! Envoyez le message.', 'success');
        });
        
        // Ajouter l'événement au bouton Fermer
        document.getElementById('close-manual-btn').addEventListener('click', function() {
            buttonContainer.remove();
            showNotification('Vous pouvez ouvrir WhatsApp plus tard', 'info');
        });
        
        // Fermer en cliquant sur l'arrière-plan
        buttonContainer.addEventListener('click', function(e) {
            if (e.target === buttonContainer) {
                buttonContainer.remove();
            }
        });
    }

    /* ===================== COMPTE À REBOURS CODE ===================== */
    function startCodeCountdown(seconds) {
        let remaining = seconds;
        
        // Afficher le premier message
        showNotification(`Vérifiez WhatsApp dans ${remaining} secondes pour votre code...`, 'info');
        
        // Mettre à jour toutes les 10 secondes
        const countdownInterval = setInterval(() => {
            remaining -= 10;
            if (remaining > 0) {
                showNotification(`Code dans ${remaining} secondes...`, 'info');
            } else {
                clearInterval(countdownInterval);
                showNotification('Code envoyé! Vérifiez WhatsApp.', 'success');
            }
        }, 10000);
    }

    /* ===================== PLANIFICATION REDIRECTION ===================== */
    function scheduleRedirection(seconds) {
        setTimeout(() => {
            showNotification('Redirection vers la vérification du code...', 'info');
            setTimeout(() => {
                window.location.href = '../frontend/double_authen.php';
            }, 1500);
        }, seconds * 1000);
    }

    /* ===================== GESTION SUCCÈS VENDEUR ===================== */
    function handleVendeurSuccess(result) {
        showNotification('Compte vendeur créé avec succès !', 'success');
        
        // Redirection après 2 secondes
        setTimeout(() => {
            if (result.redirect_url) {
                window.location.href = result.redirect_url;
            } else {
                window.location.href = '../frontend/dashboard_vendeur.php';
            }
        }, 2000);
    }

    /* ===================== FONCTION DE SOUMISSION POUR ACHETEUR ===================== */
    async function submitAcheteur(formData) {
        const response = await fetch('../backend/auth/inscription_client.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                accountType: 'acheteur',
                password: formData.password,
                user: formData.acheteur
            })
        });
        return response;
    }

    /* ===================== FONCTION DE SOUMISSION POUR VENDEUR ===================== */
    async function submitVendeur(formData) {
        const response = await fetch('../backend/auth/inscription_vendeur.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                accountType: 'vendeur',
                password: formData.password,
                entreprise: formData.vendeur.entreprise,
                proprietaire: formData.vendeur.proprietaire,
                subscription: formData.subscription
            })
        });
        return response;
    }

    /* ===================== UTILITAIRES ===================== */
    function showError(input, msg) {
        const group = input.closest('.form-group');
        group.classList.add('error');
        group.querySelector('.error-message').textContent = msg;
    }

    function clearErrors() {
        document.querySelectorAll('.form-group').forEach(g => {
            g.classList.remove('error');
            const e = g.querySelector('.error-message');
            if (e) e.textContent = '';
        });
    }

    function setupFormListeners() {
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', () => {
                const g = input.closest('.form-group');
                if (g) g.classList.remove('error');
            });
        });
    }

    function showNotification(message, type = 'info') {
        // Supprimer les notifications précédentes
        document.querySelectorAll('.notification').forEach(n => n.remove());
        
        // Créer la notification
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close"><i class="fas fa-times"></i></button>
        `;

        // Styles de la notification
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 300px;
            max-width: 400px;
            z-index: 9999;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        `;

        // Styles pour le contenu
        const contentStyle = `
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        `;
        notification.querySelector('.notification-content').style.cssText = contentStyle;

        // Bouton de fermeture
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.style.cssText = `
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 5px;
            margin-left: 15px;
            font-size: 14px;
            opacity: 0.8;
            transition: opacity 0.2s;
        `;

        closeBtn.addEventListener('mouseover', () => {
            closeBtn.style.opacity = '1';
        });
        
        closeBtn.addEventListener('mouseout', () => {
            closeBtn.style.opacity = '0.8';
        });

        closeBtn.addEventListener('click', () => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        });

        // Animation
        if (!document.querySelector('#notification-styles')) {
            const style = document.createElement('style');
            style.id = 'notification-styles';
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }

        // Ajouter la notification
        document.body.appendChild(notification);

        // Auto-suppression après 5 secondes (sauf erreurs)
        const autoRemoveTime = type === 'error' ? 8000 : 5000;
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }
        }, autoRemoveTime);
    }

    /* ===================== DÉMARRAGE ===================== */
    init();

    /* ===================== FONCTION DE TEST ===================== */
    // Pour tester l'ouverture de WhatsApp, décommentez la ligne suivante :
    // testWhatsAppOpening();
});
