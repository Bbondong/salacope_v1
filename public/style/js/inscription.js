// fichier: ../style/js/inscription.js
// VERSION ACHETEUR — saute l'étape abonnement

document.addEventListener('DOMContentLoaded', function () {

    /* ===================== ÉLÉMENTS DOM ===================== */
    const steps = document.querySelectorAll('.form-step');
    const progressFill = document.getElementById('progress-fill');
    const accountTypeOptions = document.querySelectorAll('.account-type-option');
    const nextStep1Btn = document.getElementById('next-step-1');
    const nextStep2Btn = document.getElementById('next-step-2');
    const prevBtns = document.querySelectorAll('.btn-prev');
    const acheteurForm = document.getElementById('acheteur-form');

    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordRequirements = document.querySelectorAll('.requirement');
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');

    const termsCheckbox = document.getElementById('terms');
    const submitBtn = document.getElementById('submit-form');

    /* ===================== VARIABLES ===================== */
    let currentStep = 1;
    let selectedAccountType = 'acheteur';

    let formData = {
        acheteur: {}
    };

    /* ===================== INITIALISATION ===================== */
    function init() {
        nextStep1Btn.disabled = true;
        submitBtn.disabled = true;

        selectAccountType('acheteur');

        // masquer visuellement l'étape abonnement
        const step3Indicator = document.querySelector('.step[data-step="3"]');
        if (step3Indicator) step3Indicator.style.display = 'none';

        setupFormListeners();
        validatePassword('');
    }

    /* ===================== TYPE DE COMPTE ===================== */
    accountTypeOptions.forEach(option => {
        option.addEventListener('click', function () {
            const type = this.dataset.type;
            if (type === 'acheteur') {
                selectAccountType(type);
            } else {
                showNotification('Inscription vendeur bientôt disponible', 'info');
            }
        });
    });

    function selectAccountType(type) {
        accountTypeOptions.forEach(opt => opt.classList.remove('selected'));
        document.querySelector(`.account-type-option[data-type="${type}"]`).classList.add('selected');

        selectedAccountType = type;
        nextStep1Btn.disabled = false;
    }

    /* ===================== NAVIGATION ===================== */
    nextStep1Btn.addEventListener('click', () => goToStep(2));
    nextStep2Btn.addEventListener('click', goFromStep2);

    prevBtns.forEach(btn => {
        btn.addEventListener('click', () => goToStep(currentStep - 1));
    });

    function goFromStep2() {
        if (!validateStep2()) {
            showNotification('Veuillez corriger les erreurs', 'error');
            return;
        }
        saveFormData();
        goToStep(4); // 🔥 SAUT DE L'ÉTAPE ABONNEMENT
    }

    function goToStep(step) {
        if (step < 1 || step > 4) return;

        document.getElementById(`step-${currentStep}`).classList.remove('active');
        document.getElementById(`step-${step}`).classList.add('active');

        currentStep = step;
        updateProgress(step);

        if (step === 4) updateSummary();

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function updateProgress(step) {
        document.querySelectorAll('.step').forEach(s => {
            const n = parseInt(s.dataset.step);
            s.classList.toggle('active', n <= step);
        });

        const percent = ((step - 1) / 3) * 100;
        progressFill.style.width = `${percent}%`;
    }

    /* ===================== VALIDATION ===================== */
    function validateStep2() {
        clearErrors();
        let valid = true;

        ['nom', 'postnom', 'prenom', 'telephone'].forEach(id => {
            const input = document.getElementById(id);
            if (!input.value.trim()) {
                showError(input, 'Champ obligatoire');
                valid = false;
            }
        });

        if (!validatePasswordFields()) valid = false;
        return valid;
    }

    function validatePasswordFields() {
        let valid = true;

        if (passwordInput.value.length < 6) {
            showError(passwordInput, '6 caractères minimum');
            valid = false;
        }

        if (passwordInput.value !== confirmPasswordInput.value) {
            showError(confirmPasswordInput, 'Les mots de passe ne correspondent pas');
            valid = false;
        }

        return valid;
    }

    /* ===================== MOT DE PASSE ===================== */
    passwordInput.addEventListener('input', () => validatePassword(passwordInput.value));

    function validatePassword(password) {
        const rules = {
            length: password.length >= 6,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password)
        };

        passwordRequirements.forEach(req => {
            req.classList.toggle('valid', rules[req.dataset.rule]);
        });
    }

    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const input = this.parentElement.querySelector('input');
            input.type = input.type === 'password' ? 'text' : 'password';
        });
    });

    /* ===================== DONNÉES ===================== */
    function saveFormData() {
        formData.acheteur = {
            nom: nom.value,
            postnom: postnom.value,
            prenom: prenom.value,
            telephone: telephone.value
        };
        formData.password = passwordInput.value;
    }

    /* ===================== RÉSUMÉ ===================== */
    function updateSummary() {
        document.getElementById('summary-account-type').textContent =
            'Compte Acheteur (Gratuit)';

        const container = document.querySelector('#summary-personal-info .summary-details');
        container.innerHTML = `
            <p><strong>Nom :</strong> ${formData.acheteur.nom}</p>
            <p><strong>Post-nom :</strong> ${formData.acheteur.postnom}</p>
            <p><strong>Prénom :</strong> ${formData.acheteur.prenom}</p>
            <p><strong>Téléphone :</strong> ${formData.acheteur.telephone}</p>
        `;
    }

    /* ===================== SOUMISSION ===================== */
    termsCheckbox.addEventListener('change', () => {
        submitBtn.disabled = !termsCheckbox.checked;
    });

    document.getElementById('inscription-form').addEventListener('submit', submitForm);

    async function submitForm(e) {
        e.preventDefault();

        if (!termsCheckbox.checked) return;

        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Création...';

        try {
            const response = await fetch('/backend/auth/inscription.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    accountType: 'acheteur',
                    password: formData.password,
                    user: formData.acheteur
                })
            });

            const result = await response.json();

            if (result.success) {
                showNotification('Compte créé avec succès', 'success');
                setTimeout(() => location.href = './double_authen.php', 1500);
            } else {
                throw new Error(result.message);
            }

        } catch (err) {
            showNotification(err.message || 'Erreur serveur', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Finaliser l\'inscription';
        }
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
        alert(message); // simple (tu peux remettre ta version stylée)
    }

    /* ===================== START ===================== */
    init();
});
