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
                const response = await fetch('http://localhost/votre_projet/backend/auth/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        username: username,
                        password: password
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message, 'success');
                    
                    // Stocker les informations de l'utilisateur
                    localStorage.setItem('user', JSON.stringify(result.data));
                    
                    // Rediriger selon le type d'utilisateur
                    setTimeout(() => {
                        if (result.data.user_type === 'admin') {
                            window.location.href = '../admin/dashboard.php';
                        } else {
                            window.location.href = '../client/dashboard.php';
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