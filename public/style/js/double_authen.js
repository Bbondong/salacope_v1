document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Page double authentification chargée');
    
    // Éléments DOM
    const codeInputs = document.querySelectorAll('.code-input');
    const verifyBtn = document.getElementById('verify-btn');
    const resendBtn = document.getElementById('resend-btn');
    const whatsappBtn = document.getElementById('whatsapp-btn');
    const helpBtn = document.getElementById('help-btn');
    const userPhone = document.getElementById('user-phone');
    const codeExpiry = document.getElementById('code-expiry');
    const countdownEl = document.getElementById('countdown');
    const resendTimerEl = document.getElementById('resend-timer');
    const timerEl = document.getElementById('timer');
    const whatsappStatus = document.getElementById('whatsapp-status');
    const helpModal = document.getElementById('help-modal');
    const closeHelpModal = document.getElementById('close-help-modal');
    
    // Variables
    let codeExpiryTime = null;
    let resendTimer = 60; // 60 secondes pour renvoyer
    let verificationAttempts = 0;
    const MAX_ATTEMPTS = 3;
    
    // Initialisation
    init();
    
    async function init() {
        console.log('🔧 Initialisation de la double authentification');
        
        // Récupérer les données de session depuis le serveur
        await loadUserData();
        
        // Configurer les écouteurs d'événements
        setupEventListeners();
        
        // Configurer la saisie du code
        setupCodeInputs();
        
        // Démarrer les timers
        startCodeExpiryTimer();
        startResendTimer();
    }
    
    async function loadUserData() {
        try {
            // Récupérer les données de session depuis le backend
            const response = await fetch('/backend/auth/get_session_data.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'include' // Important pour les cookies de session
            });
            
            if (response.ok) {
                const data = await response.json();
                console.log('👤 Données session chargées:', data);
                
                if (data.success && data.session) {
                    const session = data.session;
                    userPhone.textContent = session.client_telephone || 'Non disponible';
                    
                    // Convertir la date d'expiration
                    if (session.verification_expires) {
                        codeExpiryTime = new Date(session.verification_expires);
                    }
                    
                    // Afficher/masquer le statut WhatsApp
                    if (session.whatsapp_sent) {
                        whatsappStatus.style.display = 'flex';
                    } else {
                        whatsappStatus.style.display = 'none';
                    }
                } else {
                    // Rediriger si pas de session
                    showNotification('Session expirée. Veuillez vous réinscrire.', 'error');
                    setTimeout(() => {
                        window.location.href = './login.php';
                    }, 3000);
                }
            } else {
                throw new Error('Erreur de chargement des données');
            }
        } catch (error) {
            console.error('❌ Erreur chargement session:', error);
            showNotification('Impossible de charger les données. Veuillez rafraîchir.', 'error');
        }
    }
    
    function setupEventListeners() {
        // Bouton vérifier
        verifyBtn.addEventListener('click', verifyCode);
        
        // Bouton renvoyer
        resendBtn.addEventListener('click', resendCode);
        
        // Bouton WhatsApp
        whatsappBtn.addEventListener('click', openWhatsApp);
        
        // Bouton aide
        helpBtn.addEventListener('click', () => {
            helpModal.style.display = 'flex';
        });
        
        // Fermer modal
        closeHelpModal.addEventListener('click', () => {
            helpModal.style.display = 'none';
        });
        
        // Fermer modal en cliquant à l'extérieur
        helpModal.addEventListener('click', (e) => {
            if (e.target === helpModal) {
                helpModal.style.display = 'none';
            }
        });
        
        // Touche Entrée pour vérifier
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && isCodeComplete()) {
                verifyCode();
            }
        });
    }
    
    function setupCodeInputs() {
        codeInputs.forEach((input, index) => {
            // Navigation avec les flèches
            input.addEventListener('keydown', (e) => {
                handleCodeInputNavigation(e, index);
            });
            
            // Saisie du caractère
            input.addEventListener('input', (e) => {
                handleCodeInput(e, index);
            });
            
            // Coller un code complet
            input.addEventListener('paste', (e) => {
                handlePasteCode(e);
            });
        });
        
        // Focus sur le premier champ
        codeInputs[0].focus();
    }
    
    function handleCodeInputNavigation(e, index) {
        const key = e.key;
        
        if (key === 'ArrowRight' || key === 'ArrowDown') {
            // Aller au champ suivant
            e.preventDefault();
            if (index < codeInputs.length - 1) {
                codeInputs[index + 1].focus();
            }
        } else if (key === 'ArrowLeft' || key === 'ArrowUp') {
            // Aller au champ précédent
            e.preventDefault();
            if (index > 0) {
                codeInputs[index - 1].focus();
            }
        } else if (key === 'Backspace') {
            // Effacer et aller en arrière si vide
            if (!codeInputs[index].value && index > 0) {
                e.preventDefault();
                codeInputs[index - 1].focus();
                codeInputs[index - 1].value = '';
                updateCodeInputState(index - 1);
            } else if (codeInputs[index].value) {
                // Effacer le champ courant
                setTimeout(() => {
                    updateCodeInputState(index);
                }, 0);
            }
        }
    }
    
    function handleCodeInput(e, index) {
        const value = e.target.value;
        
        // N'autoriser que les chiffres
        if (!/^\d*$/.test(value)) {
            e.target.value = '';
            updateCodeInputState(index);
            return;
        }
        
        // Limiter à 1 chiffre
        if (value.length > 1) {
            e.target.value = value.charAt(0);
        }
        
        updateCodeInputState(index);
        
        // Aller au champ suivant si un chiffre est saisi
        if (value && index < codeInputs.length - 1) {
            setTimeout(() => {
                codeInputs[index + 1].focus();
            }, 10);
        }
        
        // Vérifier si le code est complet
        checkCodeComplete();
    }
    
    function handlePasteCode(e) {
        e.preventDefault();
        const pasteData = e.clipboardData.getData('text').trim();
        
        // Vérifier si c'est un code à 6 chiffres
        if (/^\d{6}$/.test(pasteData)) {
            const digits = pasteData.split('');
            
            codeInputs.forEach((input, index) => {
                if (index < digits.length) {
                    input.value = digits[index];
                    updateCodeInputState(index);
                }
            });
            
            // Focus sur le dernier champ
            if (codeInputs[5]) {
                codeInputs[5].focus();
            }
            
            checkCodeComplete();
        } else {
            showNotification('Veuillez coller un code à 6 chiffres', 'error');
        }
    }
    
    function updateCodeInputState(index) {
        const input = codeInputs[index];
        
        // Retirer toutes les classes
        input.classList.remove('filled', 'error');
        
        // Ajouter la classe appropriée
        if (input.value) {
            input.classList.add('filled');
        }
    }
    
    function checkCodeComplete() {
        const code = getCodeFromInputs();
        const isComplete = code.length === 6;
        
        verifyBtn.disabled = !isComplete;
        return isComplete;
    }
    
    function getCodeFromInputs() {
        return Array.from(codeInputs)
            .map(input => input.value)
            .join('');
    }
    
    function clearCodeInputs() {
        codeInputs.forEach((input, index) => {
            input.value = '';
            updateCodeInputState(index);
        });
        
        verifyBtn.disabled = true;
        codeInputs[0].focus();
    }
    
    function markCodeInputsAsError() {
        codeInputs.forEach(input => {
            input.classList.add('error');
        });
        
        // Retirer l'erreur après 1 seconde
        setTimeout(() => {
            codeInputs.forEach(input => {
                input.classList.remove('error');
            });
        }, 1000);
    }
    
    // TIMERS
    function startCodeExpiryTimer() {
        if (!codeExpiryTime) return;
        
        function updateTimer() {
            const now = new Date();
            const diff = codeExpiryTime - now;
            
            if (diff <= 0) {
                // Code expiré
                countdownEl.textContent = '00:00';
                codeExpiry.textContent = 'Expiré';
                timerEl.classList.add('expired');
                verifyBtn.disabled = true;
                showNotification('Le code a expiré. Veuillez en demander un nouveau.', 'error');
                return;
            }
            
            const minutes = Math.floor(diff / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            
            const timeStr = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            countdownEl.textContent = timeStr;
            codeExpiry.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            requestAnimationFrame(updateTimer);
        }
        
        updateTimer();
    }
    
    function startResendTimer() {
        resendBtn.disabled = true;
        resendTimer = 60;
        
        const timerInterval = setInterval(() => {
            resendTimer--;
            resendTimerEl.textContent = `(${resendTimer}s)`;
            
            if (resendTimer <= 0) {
                clearInterval(timerInterval);
                resendBtn.disabled = false;
                resendTimerEl.textContent = '';
            }
        }, 1000);
    }
    
    // ACTIONS
    async function verifyCode() {
        if (!checkCodeComplete()) {
            showNotification('Veuillez compléter le code à 6 chiffres', 'error');
            return;
        }
        
        verificationAttempts++;
        const enteredCode = getCodeFromInputs();
        
        console.log('🔐 Tentative de vérification:', enteredCode);
        
        // Désactiver le bouton pendant la vérification
        verifyBtn.disabled = true;
        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Vérification...';
        
        try {
            // Appeler l'API de vérification
            const response = await fetch('/backend/auth/verify_code.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'include',
                body: JSON.stringify({
                    verification_code: enteredCode
                })
            });
            
            const result = await response.json();
            console.log('📥 Réponse vérification:', result);
            
            if (response.ok && result.success) {
                // Vérification réussie
                showNotification('Code vérifié avec succès ! Redirection...', 'success');
                
                // Rediriger selon le type d'utilisateur
                setTimeout(() => {
                    if (result.user_type === 'admin') {
                        window.location.href = '/admin/index.php';
                    } else if (result.user_type === 'vendeur') {
                        window.location.href = '/vendeur/index.php';
                    } else {
                        window.location.href = '/clients/index.php';
                    }
                }, 1500);
                
            } else {
                // Vérification échouée
                throw new Error(result.message || 'Code incorrect');
            }
            
        } catch (error) {
            // Vérification échouée
            console.error('❌ Échec vérification:', error);
            
            markCodeInputsAsError();
            clearCodeInputs();
            
            const remainingAttempts = MAX_ATTEMPTS - verificationAttempts;
            
            if (remainingAttempts > 0) {
                showNotification(`${error.message}. ${remainingAttempts} tentative(s) restante(s).`, 'error');
            } else {
                showNotification('Trop de tentatives. Veuillez demander un nouveau code.', 'error');
                verifyBtn.disabled = true;
                setTimeout(() => {
                    window.location.href = './index.php';
                }, 3000);
            }
        } finally {
            // Réactiver le bouton
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Vérifier le code';
        }
    }
    
    async function resendCode() {
        console.log('🔄 Renvoi du code demandé');
        
        resendBtn.disabled = true;
        resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
        
        try {
            // Appeler l'API pour renvoyer le code
            const response = await fetch('/backend/auth/resend_code.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'include'
            });
            
            const result = await response.json();
            console.log('📥 Réponse renvoi:', result);
            
            if (response.ok && result.success) {
                showNotification('Nouveau code envoyé sur WhatsApp', 'success');
                
                // Réinitialiser le timer d'expiration
                if (result.new_expiry) {
                    codeExpiryTime = new Date(result.new_expiry);
                }
                
                // Redémarrer les timers
                startCodeExpiryTimer();
                startResendTimer();
                
                // Effacer les champs
                clearCodeInputs();
                
                // Réinitialiser les tentatives
                verificationAttempts = 0;
                
            } else {
                throw new Error(result.message || 'Échec d\'envoi du code');
            }
            
        } catch (error) {
            console.error('❌ Échec renvoi:', error);
            showNotification('Échec d\'envoi du code. Veuillez réessayer.', 'error');
        } finally {
            resendBtn.disabled = false;
            resendBtn.innerHTML = '<i class="fas fa-redo"></i> Renvoyer le code';
        }
    }
    
    function openWhatsApp() {
        console.log('📱 Ouverture WhatsApp');
        
        const phoneNumber = "243962763130";
        const userPhoneNumber = userPhone.textContent.replace(/\D/g, '');
        const message = `Bonjour,\n\nJe n'ai pas reçu mon code de vérification pour mon compte Salacope.\n\nNuméro: ${userPhoneNumber}\n\nVeuillez m'envoyer mon code de double authentification.\n\nMerci.`;
        
        const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
        
        showNotification('Ouverture de WhatsApp...', 'info');
        
        const whatsappWindow = window.open(whatsappUrl, '_blank');
        
        if (!whatsappWindow) {
            showNotification('Popup bloqué. Veuillez ouvrir WhatsApp manuellement.', 'warning');
        }
    }
    
    // UTILITAIRES
    function showNotification(message, type = 'info', duration = 5000) {
        // Supprimer les notifications existantes
        document.querySelectorAll('.notification').forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        let icon = 'info-circle';
        let bgColor = '#2196F3';
        
        switch(type) {
            case 'success':
                icon = 'check-circle';
                bgColor = '#4CAF50';
                break;
            case 'error':
                icon = 'exclamation-circle';
                bgColor = '#f44336';
                break;
            case 'warning':
                icon = 'exclamation-triangle';
                bgColor = '#ff9800';
                break;
        }
        
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${icon}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close"><i class="fas fa-times"></i></button>
        `;
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${bgColor};
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
            font-family: 'Poppins', sans-serif;
        `;
        
        notification.querySelector('.notification-content').style.cssText = `
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        `;
        
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
        
        // Ajouter les styles d'animation si nécessaire
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
        
        // Auto-suppression
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }
        }, duration);
    }
    
    // Vérifier si le code est complet
    function isCodeComplete() {
        return getCodeFromInputs().length === 6;
    }
});