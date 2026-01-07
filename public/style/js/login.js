document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Récupérer les valeurs du formulaire
            const username = document.querySelector('input[name="user"]').value;
            const password = document.querySelector('input[name="pass"]').value;
            
            // Validation basique
            if (!username || !password) {
                showMessage('Veuillez remplir tous les champs', 'error');
                return;
            }
            
            // Afficher un indicateur de chargement
            const submitBtn = loginForm.querySelector('input[type="submit"]');
            const originalBtnText = submitBtn.value;
            submitBtn.value = 'Connexion en cours...';
            submitBtn.disabled = true;
            
            try {
                // URL dynamique selon l'environnement
                const baseUrl = window.location.origin;
                const apiUrl = `${baseUrl}/backend/auth/login.php`;
                
                console.log('Envoi vers:', apiUrl);
                
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        username: username,
                        password: password
                    }),
                    credentials: 'include' // Important pour les sessions
                });
                
                const result = await response.json();
                console.log('Réponse API:', result);
                
                if (result.success) {
                    showMessage(result.message, 'success');
                    
                    // Rediriger après succès
                    setTimeout(() => {
                        if (result.data.user_type === 'admin') {
                            window.location.href = result.redirect || '/admin/index.php';
                        } else {
                            window.location.href = result.redirect || '/client/dashboard.php';
                        }
                    }, 1500);
                } else {
                    showMessage(result.message, 'error');
                    submitBtn.value = originalBtnText;
                    submitBtn.disabled = false;
                }
                
            } catch (error) {
                console.error('Erreur:', error);
                showMessage('Erreur de connexion au serveur', 'error');
                submitBtn.value = originalBtnText;
                submitBtn.disabled = false;
            }
        });
    }
    
    // Fonction pour vérifier la session au chargement
    async function checkSession() {
        try {
            const baseUrl = window.location.origin;
            const response = await fetch(`${baseUrl}/backend/auth/check_session.php`, {
                credentials: 'include'
            });
            const result = await response.json();
            
            if (result.is_logged_in) {
                console.log('Utilisateur déjà connecté:', result.user);
                // Rediriger si déjà connecté
                if (window.location.pathname.includes('login')) {
                    if (result.user.user_type === 'admin') {
                        window.location.href = '/admin/dashboard.php';
                    } else {
                        window.location.href = '/client/dashboard.php';
                    }
                }
            }
        } catch (error) {
            console.log('Session non vérifiée:', error);
        }
    }
    
    // Vérifier la session au chargement
    checkSession();
    
    // Fonction pour afficher les messages
    function showMessage(message, type) {
        // Supprimer les anciens messages
        const existingMessage = document.querySelector('.message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Créer le nouveau message
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.textContent = message;
        messageDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            animation: slideIn 0.3s ease-out;
            ${type === 'success' ? 'background-color: #4CAF50;' : 'background-color: #f44336;'}
        `;
        
        document.body.appendChild(messageDiv);
        
        // Supprimer le message après 5 secondes
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => messageDiv.remove(), 300);
            }
        }, 5000);
    }
    
    // Ajouter les styles d'animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
});