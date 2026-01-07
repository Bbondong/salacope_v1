document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Script login chargé');
    
    const loginForm = document.querySelector('form');
    
    if (loginForm) {
        console.log('✅ Formulaire trouvé');
        
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('📝 Form submitted');
            
            // Récupérer les valeurs du formulaire
            const username = document.querySelector('input[name="user"]').value;
            const password = document.querySelector('input[name="pass"]').value;
            
            console.log('🔑 Identifiants:', { 
                username: username, 
                password: password ? '***' : '(vide)' 
            });
            
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
                
                console.log('📤 Envoi vers:', apiUrl);
                console.log('📦 Données:', { username, password: '***' });
                
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
                
                console.log('📥 Réponse reçue:');
                console.log('- Status:', response.status, response.statusText);
                console.log('- OK:', response.ok);
                
                // Lire le texte de la réponse
                const responseText = await response.text();
                console.log('📄 Réponse brute (500 premiers caractères):', responseText.substring(0, 500));
                
                let result;
                try {
                    result = JSON.parse(responseText);
                    console.log('✅ JSON parsé:', result);
                } catch (jsonError) {
                    console.error('❌ Erreur parsing JSON:', jsonError);
                    console.error('Texte reçu:', responseText);
                    throw new Error('Le serveur a retourné une réponse invalide');
                }
                
                // VÉRIFICATION IMPORTANTE : La structure de la réponse
                if (result && typeof result.success !== 'undefined') {
                    console.log('🔍 Structure OK - success:', result.success);
                    
                    if (result.success === true || result.success === 'true') {
                        console.log('🎉 Connexion réussie!');
                        console.log('- Message:', result.message);
                        console.log('- Data:', result.data);
                        console.log('- Redirect:', result.redirect);
                        
                        showMessage(result.message || 'Connexion réussie', 'success');
                        
                        // Rediriger après succès
                        setTimeout(() => {
                            let redirectUrl = '/';
                            
                            // Priorité 1 : URL de redirection de l'API
                            if (result.redirect) {
                                redirectUrl = result.redirect;
                            }
                            // Priorité 2 : Basé sur user_type
                            else if (result.data && result.data.user_type) {
                                switch(result.data.user_type) {
                                    case 'admin':
                                        redirectUrl = '/admin/index.php';
                                        break;
                                    case 'vendeur':
                                        redirectUrl = '/vendeur/index.php';
                                        break;
                                    case 'client':
                                        redirectUrl = '/clients/index.php';
                                        break;
                                    default:
                                        redirectUrl = '/';
                                }
                            }
                            // Priorité 3 : Fallback
                            else {
                                redirectUrl = '/';
                            }
                            
                            console.log('🔄 Redirection vers:', redirectUrl);
                            window.location.href = redirectUrl;
                        }, 1500);
                        
                    } else {
                        console.log('❌ Connexion échouée:', result.message);
                        showMessage(result.message || 'Identifiants incorrects', 'error');
                        submitBtn.value = originalBtnText;
                        submitBtn.disabled = false;
                    }
                } else {
                    console.error('❌ Structure de réponse invalide');
                    console.error('Réponse complète:', result);
                    showMessage('Erreur serveur (structure invalide)', 'error');
                    submitBtn.value = originalBtnText;
                    submitBtn.disabled = false;
                }
                
            } catch (error) {
                console.error('💥 Erreur fatale:', error);
                console.error('Stack:', error.stack);
                
                // Message d'erreur plus précis
                let errorMessage = 'Erreur de connexion au serveur';
                if (error.message.includes('Failed to fetch')) {
                    errorMessage = 'Impossible de contacter le serveur. Vérifiez votre connexion.';
                } else if (error.message.includes('JSON')) {
                    errorMessage = 'Erreur dans la réponse du serveur';
                }
                
                showMessage(errorMessage, 'error');
                submitBtn.value = originalBtnText;
                submitBtn.disabled = false;
            }
        });
    } else {
        console.log('❌ Formulaire non trouvé');
    }
    
    // Fonction pour vérifier la session au chargement
    async function checkSession() {
        try {
            console.log('🔍 Vérification de session...');
            const baseUrl = window.location.origin;
            const response = await fetch(`${baseUrl}/backend/auth/check_session.php`, {
                credentials: 'include'
            });
            
            console.log('Session check status:', response.status);
            
            const result = await response.json();
            console.log('Session check result:', result);
            
            if (result.is_logged_in) {
                console.log('✅ Utilisateur déjà connecté:', result.user);
                
                // Rediriger si déjà connecté
                if (window.location.pathname.includes('login') || 
                    window.location.pathname.includes('index.php')) {
                    
                    let redirectUrl = '/';
                    
                    if (result.user && result.user.user_type) {
                        switch(result.user.user_type) {
                            case 'admin':
                                redirectUrl = '/admin/index.php';
                                break;
                            case 'vendeur':
                                redirectUrl = '/vendeur/index.php';
                                break;
                            case 'client':
                                redirectUrl = '/clients/index.php';
                                break;
                        }
                    }
                    
                    console.log('🔄 Redirection automatique vers:', redirectUrl);
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 1000);
                }
            } else {
                console.log('👤 Non connecté');
            }
        } catch (error) {
            console.log('⚠️ Session non vérifiée:', error.message);
        }
    }
    
    // Vérifier la session au chargement
    checkSession();
    
    // Fonction pour afficher les messages (améliorée)
    function showMessage(message, type) {
        console.log('💬 Message:', type, '-', message);
        
        // Supprimer les anciens messages
        const existingMessages = document.querySelectorAll('.flash-message');
        existingMessages.forEach(msg => {
            msg.style.animation = 'slideOut 0.2s ease-out';
            setTimeout(() => {
                if (msg.parentNode) msg.remove();
            }, 200);
        });
        
        // Créer le nouveau message
        const messageDiv = document.createElement('div');
        messageDiv.className = `flash-message ${type}`;
        messageDiv.textContent = message;
        
        // Styles améliorés
        const styles = {
            position: 'fixed',
            top: '20px',
            right: '20px',
            padding: '15px 25px',
            borderRadius: '8px',
            color: 'white',
            fontWeight: 'bold',
            zIndex: '10000',
            animation: 'slideIn 0.3s ease-out',
            boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
            fontFamily: 'Arial, sans-serif',
            fontSize: '14px',
            maxWidth: '400px',
            wordWrap: 'break-word',
            display: 'flex',
            alignItems: 'center',
            gap: '10px'
        };
        
        // Appliquer les styles
        Object.assign(messageDiv.style, styles);
        
        // Couleur selon le type
        if (type === 'success') {
            messageDiv.style.background = 'linear-gradient(135deg, #4CAF50, #2E7D32)';
            messageDiv.style.borderLeft = '5px solid #1B5E20';
            
            // Ajouter icône
            const icon = document.createElement('span');
            icon.textContent = '✓';
            icon.style.fontSize = '18px';
            messageDiv.prepend(icon);
        } else {
            messageDiv.style.background = 'linear-gradient(135deg, #f44336, #C62828)';
            messageDiv.style.borderLeft = '5px solid #B71C1C';
            
            // Ajouter icône
            const icon = document.createElement('span');
            icon.textContent = '✗';
            icon.style.fontSize = '18px';
            messageDiv.prepend(icon);
        }
        
        // Ajouter à la page
        document.body.appendChild(messageDiv);
        
        // Auto-destruction après 5 secondes
        setTimeout(() => {
            messageDiv.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.remove();
                }
            }, 300);
        }, 5000);
    }
    
    // Ajouter les styles d'animation s'ils n'existent pas
    if (!document.querySelector('#login-animations')) {
        const style = document.createElement('style');
        style.id = 'login-animations';
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%) translateY(-20px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0) translateY(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOut {
                from {
                    transform: translateX(0) translateY(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%) translateY(-20px);
                    opacity: 0;
                }
            }
            
            /* Style pour le bouton en cours de chargement */
            .loading-btn {
                position: relative;
                color: transparent !important;
            }
            
            .loading-btn::after {
                content: '';
                position: absolute;
                left: 50%;
                top: 50%;
                width: 20px;
                height: 20px;
                margin: -10px 0 0 -10px;
                border: 2px solid rgba(255,255,255,0.3);
                border-top-color: white;
                border-radius: 50%;
                animation: spin 0.8s linear infinite;
            }
            
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Ajouter un bouton de test en bas à gauche (pour debug)
    if (window.location.href.includes('localhost') || window.location.href.includes('127.0.0.1')) {
        const debugBtn = document.createElement('button');
        debugBtn.textContent = '🔍 Test API';
        debugBtn.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 20px;
            padding: 10px 15px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            z-index: 9999;
            font-size: 12px;
        `;
        debugBtn.onclick = testAPI;
        document.body.appendChild(debugBtn);
        
        async function testAPI() {
            console.log('🧪 Test API manuel...');
            
            // Test 1: Vérifier que le fichier existe
            try {
                const res1 = await fetch('/backend/auth/login.php');
                console.log('📁 Fichier existe:', res1.status);
            } catch(e) {
                console.error('❌ Fichier inaccessible:', e.message);
            }
            
            // Test 2: Envoyer des données de test
            try {
                const res2 = await fetch('/backend/auth/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username: 'test', password: 'test' })
                });
                const text = await res2.text();
                console.log('📤 Test POST:', res2.status);
                console.log('📄 Réponse:', text.substring(0, 300));
            } catch(e) {
                console.error('❌ POST échoué:', e.message);
            }
        }
    }
});