// fichier: ../style/js/inscription.js
// VERSION MODULAIRE — fonctions séparées pour client et vendeur

document.addEventListener('DOMContentLoaded', function () {
    // DÉBOGAGE INITIAL
    console.log('✅ Script inscription.js chargé');
    console.log('📍 URL actuelle:', window.location.href);
    console.log('📍 Chemin actuel:', window.location.pathname);

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

        // Désactiver la validation HTML5
        document.getElementById('inscription-form').setAttribute('novalidate', 'novalidate');
        
        // Masquer les champs vendeur par défaut
        hideVendeurFields();
    }

    /* ===================== FONCTIONS UTILITAIRES ===================== */
    function hideVendeurFields() {
        // Désactiver tous les champs vendeur
        const vendeurFields = vendeurForm.querySelectorAll('input, select, textarea');
        vendeurFields.forEach(field => {
            field.disabled = true;
            field.removeAttribute('required');
            field.style.display = 'none';
        });
        
        // Masquer les labels aussi
        const vendeurLabels = vendeurForm.querySelectorAll('label');
        vendeurLabels.forEach(label => {
            label.style.display = 'none';
        });
    }

    function showVendeurFields() {
        // Activer tous les champs vendeur
        const vendeurFields = vendeurForm.querySelectorAll('input, select, textarea');
        vendeurFields.forEach(field => {
            field.disabled = false;
            if (field.hasAttribute('data-required')) {
                field.setAttribute('required', 'required');
            }
            field.style.display = '';
        });
        
        // Afficher les labels
        const vendeurLabels = vendeurForm.querySelectorAll('label');
        vendeurLabels.forEach(label => {
            label.style.display = '';
        });
    }

    function hideAcheteurFields() {
        // Désactiver tous les champs acheteur
        const acheteurFields = acheteurForm.querySelectorAll('input, select, textarea');
        acheteurFields.forEach(field => {
            field.disabled = true;
            field.removeAttribute('required');
            field.style.display = 'none';
        });
        
        // Masquer les labels
        const acheteurLabels = acheteurForm.querySelectorAll('label');
        acheteurLabels.forEach(label => {
            label.style.display = 'none';
        });
    }

    function showAcheteurFields() {
        // Activer tous les champs acheteur
        const acheteurFields = acheteurForm.querySelectorAll('input, select, textarea');
        acheteurFields.forEach(field => {
            field.disabled = false;
            if (field.hasAttribute('data-required')) {
                field.setAttribute('required', 'required');
            }
            field.style.display = '';
        });
        
        // Afficher les labels
        const acheteurLabels = acheteurForm.querySelectorAll('label');
        acheteurLabels.forEach(label => {
            label.style.display = '';
        });
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

        // Afficher/masquer les formulaires appropriés
        showAccountForm(type);

        // Gérer l'affichage de l'étape abonnement
        const step3Indicator = document.querySelector('.step[data-step="3"]');
        if (step3Indicator) {
            step3Indicator.style.display = type === 'vendeur' ? 'flex' : 'none';
        }

        // Gérer les champs selon le type
        if (type === 'acheteur') {
            showAcheteurFields();
            hideVendeurFields();
        } else {
            showVendeurFields();
            hideAcheteurFields();
        }
    }

    function showAccountForm(type) {
        if (type === 'acheteur') {
            acheteurForm.style.display = 'block';
            vendeurForm.style.display = 'none';
        } else {
            acheteurForm.style.display = 'none';
            vendeurForm.style.display = 'block';
        }
    }

    /* ===================== NAVIGATION ===================== */
    nextStep1Btn.addEventListener('click', () => {
        goToStep(2);
        if (selectedAccountType === 'vendeur') {
            resetSubscriptionSelection();
        }
    });

    nextStep2Btn.addEventListener('click', () => {
        if (selectedAccountType === 'acheteur') {
            if (!validateAcheteurStep2()) {
                showNotification('Veuillez corriger les erreurs dans le formulaire', 'error');
                return;
            }
            goToStep(4);
        } else {
            if (!validateVendeurStep2()) {
                showNotification('Veuillez corriger les erreurs dans le formulaire', 'error');
                return;
            }
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
            if (selectedAccountType === 'acheteur' && n === 3) return;
            s.classList.toggle('active', n <= step);
        });

        let totalSteps = selectedAccountType === 'acheteur' ? 3 : 4;
        let adjustedStep = selectedAccountType === 'acheteur' && step > 2 ? step - 1 : step;
        let percent = ((adjustedStep - 1) / (totalSteps - 1)) * 100;
        progressFill.style.width = `${percent}%`;
    }

    /* ===================== VALIDATION ACHETEUR ===================== */
    function validateAcheteurStep2() {
        clearErrors();
        let valid = true;

        // Validation des champs acheteur
        const acheteurFields = ['nom', 'postnom', 'prenom', 'telephone'];
        acheteurFields.forEach(id => {
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

        // Validation mot de passe
        if (!validatePasswordFields()) valid = false;

        return valid;
    }

    /* ===================== VALIDATION VENDEUR ===================== */
    function validateVendeurStep2() {
        clearErrors();
        let valid = true;

        // Validation entreprise
        const entrepriseFields = ['nom_entreprise', 'telephone_entreprise', 'email_entreprise', 'adresse_entreprise'];
        entrepriseFields.forEach(id => {
            const input = document.getElementById(id);
            if (!input.value.trim()) {
                showError(input, 'Ce champ est obligatoire');
                valid = false;
            }
        });

        // Validation propriétaire
        const proprietaireFields = ['nom_proprietaire', 'postnom_proprietaire', 'prenom_proprietaire', 'fonction_proprietaire'];
        proprietaireFields.forEach(id => {
            const input = document.getElementById(id);
            if (!input.value.trim()) {
                showError(input, 'Ce champ est obligatoire');
                valid = false;
            }
        });

        // Validation email
        const emailInput = document.getElementById('email_entreprise');
        if (emailInput.value.trim() && !validateEmail(emailInput.value)) {
            showError(emailInput, 'Format d\'email invalide');
            valid = false;
        }

        // Validation téléphone
        const telEntrepriseInput = document.getElementById('telephone_entreprise');
        if (telEntrepriseInput.value.trim() && !validatePhoneNumber(telEntrepriseInput.value)) {
            showError(telEntrepriseInput, 'Format invalide. Ex: +243 81 123 4567');
            valid = false;
        }

        // Validation mot de passe
        if (!validatePasswordFields()) valid = false;

        return valid;
    }

    /* ===================== VALIDATION GÉNÉRALE ===================== */
    function validatePhoneNumber(phone) {
        const regex = /^\+243\s?\d{2}\s?\d{3}\s?\d{4}$/;
        return regex.test(phone);
    }

    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    function validatePasswordFields() {
        let valid = true;
        const password = passwordInput.value;

        // 3 CONDITIONS : longueur, chiffre, et lettre
        const hasLength = password.length >= 8;
        const hasNumber = /[0-9]/.test(password);
        const hasLetter = /[a-zA-Z]/.test(password); // Au moins une lettre (majuscule ou minuscule)

        // Vérifier toutes les conditions
        if (!hasLength || !hasNumber || !hasLetter) {
            let errorMsg = 'Le mot de passe doit :';
            if (!hasLength) errorMsg += ' avoir au moins 8 caractères;';
            if (!hasNumber) errorMsg += ' contenir au moins un chiffre;';
            if (!hasLetter) errorMsg += ' contenir au moins une lettre;';
            showError(passwordInput, errorMsg);
            valid = false;
        }

        // Vérifier la confirmation
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
        // 3 règles : longueur, chiffre, lettre
        const rules = {
            length: password.length >= 8,
            number: /[0-9]/.test(password),
            letter: /[a-zA-Z]/.test(password) // Vérifie au moins une lettre
        };

        passwordRequirements.forEach(req => {
            req.classList.toggle('valid', rules[req.dataset.rule]);
        });
    }

    function updatePasswordStrength(password) {
        const strengthBar = document.querySelector('.strength-bar');
        const strengthText = document.querySelector('.strength-text');
        
        // Calcul basé sur 3 critères
        let strength = 0;
        if (password.length >= 8) strength += 33; // 33% pour la longueur
        if (/[0-9]/.test(password)) strength += 33; // 33% pour le chiffre
        if (/[a-zA-Z]/.test(password)) strength += 34; // 34% pour la lettre
        
        strengthBar.style.width = `${strength}%`;
        
        // Ajuster les seuils pour 3 critères
        if (strength <= 33) {
            strengthBar.style.background = '#e53935';
            strengthText.textContent = 'Faible';
        } else if (strength <= 66) {
            strengthBar.style.background = '#ff9800';
            strengthText.textContent = 'Moyen';
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
        selectPlanBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const plan = this.dataset.plan;
                selectPlan(plan);
            });
        });

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
        
        subscriptionOptions.forEach(option => {
            option.classList.remove('selected');
        });
        document.querySelector(`.subscription-option[data-plan="${plan}"]`).classList.add('selected');
        
        nextStep3Btn.disabled = false;
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
    function getFormData() {
        if (selectedAccountType === 'acheteur') {
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
        } else {
            return {
                accountType: 'vendeur',
                password: passwordInput.value,
                entreprise: {
                    nom_entreprise: document.getElementById('nom_entreprise').value,
                    telephone_entreprise: document.getElementById('telephone_entreprise').value,
                    email_entreprise: document.getElementById('email_entreprise').value,
                    adresse_entreprise: document.getElementById('adresse_entreprise').value
                },
                proprietaire: {
                    nom: document.getElementById('nom_proprietaire').value,
                    postnom: document.getElementById('postnom_proprietaire').value,
                    prenom: document.getElementById('prenom_proprietaire').value,
                    fonction: document.getElementById('fonction_proprietaire').value
                },
                subscription: selectedPlan ? {
                    plan: selectedPlan,
                    duration: selectedDuration,
                    price: getPlanPrice(selectedPlan, selectedDuration),
                    payment_method: selectedPaymentMethod
                } : null
            };
        }
    }

    /* ===================== RÉSUMÉ ===================== */
    function updateSummary() {
        const accountTypeElement = document.getElementById('summary-account-type');
        accountTypeElement.textContent = selectedAccountType === 'acheteur' 
            ? 'Compte Acheteur (Gratuit)' 
            : 'Compte Vendeur';
        
        if (selectedAccountType === 'acheteur') {
            updateAcheteurSummary();
        } else {
            updateVendeurSummary();
        }
    }

    function updateAcheteurSummary() {
        const formData = getFormData();
        const container = document.querySelector('#summary-personal-info .summary-details');
        container.innerHTML = `
            <p><strong>Nom :</strong> ${formData.user.nom}</p>
            <p><strong>Post-nom :</strong> ${formData.user.postnom}</p>
            <p><strong>Prénom :</strong> ${formData.user.prenom}</p>
            <p><strong>Téléphone :</strong> ${formData.user.telephone}</p>
        `;
        document.getElementById('summary-business-info').style.display = 'none';
        document.getElementById('summary-subscription-info').style.display = 'none';
    }

    function updateVendeurSummary() {
        const formData = getFormData();
        
        // Informations entreprise
        const businessContainer = document.querySelector('#summary-business-info .summary-details');
        businessContainer.innerHTML = `
            <p><strong>Nom entreprise :</strong> ${formData.entreprise.nom_entreprise}</p>
            <p><strong>Téléphone :</strong> ${formData.entreprise.telephone_entreprise}</p>
            <p><strong>Email :</strong> ${formData.entreprise.email_entreprise}</p>
            <p><strong>Adresse :</strong> ${formData.entreprise.adresse_entreprise}</p>
        `;
        document.getElementById('summary-business-info').style.display = 'block';
        
        // Informations propriétaire
        const personalContainer = document.querySelector('#summary-personal-info .summary-details');
        personalContainer.innerHTML = `
            <p><strong>Nom :</strong> ${formData.proprietaire.nom}</p>
            <p><strong>Post-nom :</strong> ${formData.proprietaire.postnom}</p>
            <p><strong>Prénom :</strong> ${formData.proprietaire.prenom}</p>
            <p><strong>Fonction :</strong> ${formData.proprietaire.fonction}</p>
        `;
        
        // Informations abonnement
        if (formData.subscription) {
            const subscriptionContainer = document.querySelector('#summary-subscription-info .summary-details');
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

        // Appeler la fonction appropriée selon le type de compte
        if (selectedAccountType === 'acheteur') {
            await handleAcheteurInscription();
        } else {
            await handleVendeurInscription();
        }
    }

    /* ===================== FONCTION INSCRIPTION ACHETEUR ===================== */
    async function handleAcheteurInscription() {
        try {
            // Validation finale
            if (!validateAcheteurStep2()) {
                throw new Error('Veuillez corriger les erreurs');
            }

            const formData = getFormData();
            
            // DÉBOGAGE
            console.log('📤 Envoi des données acheteur:', formData);
            
            // IMPORTANT: Chemin corrigé
            const response = await fetch('/backend/auth/inscription_client.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            // DÉBOGAGE
            console.log('📥 Statut HTTP:', response.status);
            console.log('📥 URL appelée:', '../backend/auth/inscription_client.php');

            const result = await response.json();
            console.log('📥 Réponse JSON:', result);

            if (!response.ok) {
                // GÉRER SPÉCIFIQUEMENT LES DIFFÉRENTES ERREURS
                if (response.status === 409) {
                    throw new Error(result.message || 'Ce numéro de téléphone est déjà utilisé');
                    
                } else if (response.status === 400) {
                    throw new Error(result.message || 'Données invalides. Veuillez vérifier les informations.');
                    
                } else if (response.status === 500) {
                    console.error('Erreur serveur détaillée:', result);
                    throw new Error(result.message || 'Erreur serveur. Veuillez réessayer plus tard.');
                    
                } else {
                    throw new Error(result.message || `Erreur (${response.status})`);
                }
            }

            // Vérifier que success est true
            if (result.success !== true) {
                throw new Error(result.message || 'Erreur lors de la création du compte');
            }

            handleAcheteurSuccess(result);

        } catch (err) {
            console.error('❌ Erreur inscription acheteur:', err);
            
            const errorMessage = err.message || 'Erreur de connexion au serveur';
            
            // Mettre en évidence le champ concerné si c'est un conflit
            if (errorMessage.includes('téléphone') || errorMessage.includes('phone')) {
                const telInput = document.getElementById('telephone');
                showError(telInput, 'Ce numéro est déjà utilisé');
                // Retourner à l'étape 2 pour corriger
                goToStep(2);
            }
            
            showNotification(errorMessage, 'error');
            
            // Réactiver le bouton
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Finaliser l\'inscription';
        }
    }

    function handleAcheteurSuccess(result) {
        showNotification('Compte créé avec succès ! Redirection en cours...', 'success');
        
        // DÉBOGAGE
        console.log('✅ Données reçues:', result);
        
        setTimeout(() => {
            // CORRECTION: Vérifier la structure correcte
            if (result.whatsapp && result.whatsapp.url) {
                console.log('📱 URL WhatsApp trouvée (nouvelle structure):', result.whatsapp.url);
                openWhatsApp(result.whatsapp.url, result);
                
            } else if (result.whatsapp_redirect && result.whatsapp_redirect.url) {
                // Compatibilité avec ancienne structure
                console.log('📱 URL WhatsApp trouvée (ancienne structure):', result.whatsapp_redirect.url);
                openWhatsApp(result.whatsapp_redirect.url, result);
                
            } else if (result.redirect) {
                console.log('🔄 Redirection vers:', result.redirect);
                window.location.href = result.redirect;
                
            } else {
                console.log('🔄 Redirection par défaut vers double_authen.php');
                window.location.href = './double_authen.php';
            }
        }, 1500);
    }

    /* ===================== FONCTION INSCRIPTION VENDEUR ===================== */
    async function handleVendeurInscription() {
        try {
            // Validation finale
            if (!validateVendeurStep2()) {
                throw new Error('Veuillez corriger les erreurs');
            }

            if (!selectedPlan) {
                throw new Error('Veuillez sélectionner un plan d\'abonnement');
            }

            const formData = getFormData();
            
            console.log('📤 Envoi des données vendeur:', formData);
            
            const response = await fetch('/backend/auth/inscription_vendeur.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            console.log('📥 Statut HTTP vendeur:', response.status);

            const result = await response.json();
            console.log('📥 Réponse JSON vendeur:', result);

            if (!response.ok) {
                if (response.status === 409) {
                    let message = 'Ces informations sont déjà utilisées. ';
                    
                    if (result.message) {
                        if (result.message.includes('téléphone') || result.message.includes('phone')) {
                            message = `Ce téléphone d'entreprise est déjà utilisé. `;
                        } else if (result.message.includes('email')) {
                            message = `Cet email d'entreprise est déjà utilisé. `;
                        } else if (result.message.includes('entreprise')) {
                            message = `Ce nom d'entreprise est déjà pris. `;
                        } else {
                            message = result.message;
                        }
                    }
                    
                    throw new Error(message);
                    
                } else if (response.status === 400) {
                    throw new Error(result.message || 'Données invalides.');
                } else if (response.status === 500) {
                    console.error('Erreur serveur vendeur:', result);
                    throw new Error('Erreur serveur. Veuillez réessayer plus tard.');
                } else {
                    throw new Error(result.message || `Erreur (${response.status})`);
                }
            }

            if (result.success !== true) {
                throw new Error(result.message || 'Erreur lors de la création du compte');
            }

            handleVendeurSuccess(result);

        } catch (err) {
            console.error('❌ Erreur inscription vendeur:', err);
            
            const errorMessage = err.message || 'Erreur de connexion au serveur';
            
            // Mettre en évidence le champ concerné
            if (errorMessage.includes('téléphone d\'entreprise') || errorMessage.includes('phone')) {
                const telInput = document.getElementById('telephone_entreprise');
                showError(telInput, 'Ce téléphone d\'entreprise est déjà utilisé');
            } else if (errorMessage.includes('email d\'entreprise')) {
                const emailInput = document.getElementById('email_entreprise');
                showError(emailInput, 'Cet email d\'entreprise est déjà utilisé');
            } else if (errorMessage.includes('nom d\'entreprise')) {
                const entrepriseInput = document.getElementById('nom_entreprise');
                showError(entrepriseInput, 'Ce nom d\'entreprise est déjà pris');
            }
            
            showNotification(errorMessage, 'error');
            
            // Réactiver le bouton
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Finaliser l\'inscription';
        }
    }

    function handleVendeurSuccess(result) {
        showNotification('Compte vendeur créé avec succès !', 'success');
        
        console.log('✅ Succès vendeur:', result);
        
        setTimeout(() => {
            if (result.redirect_url) {
                console.log('🔄 Redirection vendeur vers:', result.redirect_url);
                window.location.href = result.redirect_url;
            } else if (result.redirect) {
                console.log('🔄 Redirection vendeur vers:', result.redirect);
                window.location.href = result.redirect;
            } else {
                console.log('🔄 Redirection vendeur par défaut');
                window.location.href = '../frontend/dashboard_vendeur.php';
            }
        }, 2000);
    }

    /* ===================== WHATSAPP FUNCTIONS ===================== */
    function openWhatsApp(whatsappUrl, result) {
        console.log('📱 Ouverture WhatsApp:', whatsappUrl);
        showNotification('Ouverture de WhatsApp pour la vérification...', 'info');
        
        const whatsappWindow = window.open(whatsappUrl, '_blank', 'noopener,noreferrer');
        
        if (!whatsappWindow || whatsappWindow.closed || typeof whatsappWindow.closed === 'undefined') {
            console.log('⚠️ Popup WhatsApp bloqué');
            handlePopupBlocked(whatsappUrl);
            
            // Rediriger après un délai
            setTimeout(() => {
                const redirectUrl = result.redirect || result.redirect_url || './double_authen.php';
                console.log('🔄 Redirection après popup bloqué:', redirectUrl);
                window.location.href = redirectUrl;
            }, 5000);
        } else {
            console.log('✅ WhatsApp ouvert avec succès');
            showNotification('WhatsApp ouvert! Envoyez le message pré-rempli.', 'success');
            
            // Rediriger après un délai
            setTimeout(() => {
                const redirectUrl = result.redirect || result.redirect_url || './double_authen.php';
                console.log('🔄 Redirection après WhatsApp:', redirectUrl);
                window.location.href = redirectUrl;
            }, 3000);
        }
    }

    function handlePopupBlocked(whatsappUrl) {
        showNotification('WhatsApp n\'a pas pu s\'ouvrir automatiquement', 'warning');
        createManualWhatsAppButton(whatsappUrl);
        showNotification('Cliquez sur le bouton vert pour ouvrir WhatsApp manuellement', 'info');
    }

    function createManualWhatsAppButton(whatsappUrl) {
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
        
        document.getElementById('manual-whatsapp-btn').addEventListener('click', function() {
            window.open(whatsappUrl, '_blank');
            buttonContainer.remove();
            showNotification('WhatsApp ouvert! Envoyez le message.', 'success');
        });
        
        document.getElementById('close-manual-btn').addEventListener('click', function() {
            buttonContainer.remove();
            showNotification('Vous pouvez ouvrir WhatsApp plus tard', 'info');
        });
        
        buttonContainer.addEventListener('click', function(e) {
            if (e.target === buttonContainer) {
                buttonContainer.remove();
            }
        });
    }

    function startCodeCountdown(seconds) {
        let remaining = seconds;
        
        showNotification(`Vérifiez WhatsApp dans ${remaining} secondes pour votre code...`, 'info');
        
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

    function scheduleRedirection(seconds) {
        setTimeout(() => {
            showNotification('Redirection vers la vérification du code...', 'info');
            setTimeout(() => {
                window.location.href = './double_authen.php';
            }, 1500);
        }, seconds * 1000);
    }

    /* ===================== UTILITAIRES ===================== */
    function showError(input, msg) {
        const group = input.closest('.form-group');
        group.classList.add('error');
        const errorEl = group.querySelector('.error-message');
        if (errorEl) {
            errorEl.textContent = msg;
            errorEl.style.display = 'block';
        }
    }

    function clearErrors() {
        document.querySelectorAll('.form-group').forEach(g => {
            g.classList.remove('error');
            const e = g.querySelector('.error-message');
            if (e) {
                e.textContent = '';
                e.style.display = 'none';
            }
        });
    }

    function setupFormListeners() {
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', () => {
                const g = input.closest('.form-group');
                if (g) {
                    g.classList.remove('error');
                    const e = g.querySelector('.error-message');
                    if (e) e.style.display = 'none';
                }
            });
        });
    }

    function showNotification(message, type = 'info', duration = 5000) {
        document.querySelectorAll('.notification').forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        // Si le message contient du HTML, ne pas l'échapper
        if (typeof message === 'string' && message.includes('<')) {
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                    <span>${message}</span>
                </div>
                <button class="notification-close"><i class="fas fa-times"></i></button>
            `;
        } else {
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                    <span>${message}</span>
                </div>
                <button class="notification-close"><i class="fas fa-times"></i></button>
            `;
        }

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

        const contentStyle = `
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        `;
        notification.querySelector('.notification-content').style.cssText = contentStyle;

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

        closeBtn.addEventListener('mouseover', () => closeBtn.style.opacity = '1');
        closeBtn.addEventListener('mouseout', () => closeBtn.style.opacity = '0.8');
        closeBtn.addEventListener('click', () => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        });

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

        document.body.appendChild(notification);

        const autoRemoveTime = type === 'error' ? 8000 : (type === 'info' ? duration : 5000);
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }
        }, autoRemoveTime);
    }

    /* ===================== DÉMARRAGE ===================== */
    init();

});