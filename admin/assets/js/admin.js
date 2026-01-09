// Initialisation du dashboard
document.addEventListener('DOMContentLoaded', function() {
    
    // Menu mobile toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mobileNavItems = document.querySelectorAll('.nav-item');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
    
    // Fermer le sidebar en cliquant à l'extérieur (mobile)
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 1024) {
            if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        }
    });
    
    // Gestion des onglets des paramètres
    const settingsNavItems = document.querySelectorAll('.settings-nav-item');
    const settingsSections = document.querySelectorAll('.settings-section');
    
    if (settingsNavItems.length > 0) {
        settingsNavItems.forEach(item => {
            item.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                
                // Retirer la classe active de tous les éléments
                settingsNavItems.forEach(navItem => {
                    navItem.classList.remove('active');
                });
                
                settingsSections.forEach(section => {
                    section.classList.remove('active');
                });
                
                // Ajouter la classe active à l'élément cliqué
                this.classList.add('active');
                
                // Afficher la section correspondante
                document.getElementById(target).classList.add('active');
            });
        });
    }
    
    // Gestion du téléversement d'images
    const imageUpload = document.querySelector('.image-upload');
    const fileInput = document.getElementById('product-images');
    
    if (imageUpload && fileInput) {
        imageUpload.addEventListener('click', function() {
            fileInput.click();
        });
        
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const fileName = this.files.length === 1 ? 
                    '1 image sélectionnée' : 
                    `${this.files.length} images sélectionnées`;
                
                imageUpload.innerHTML = `
                    <i class="fas fa-check-circle"></i>
                    <p>${fileName}</p>
                    <small>Cliquez pour changer</small>
                `;
            }
        });
    }
    
    // Simulation des données du dashboard
    updateStats();
    
    // Gestion des cartes de produits
    setupProductCards();
    
    // Simulation du chat
    setupChat();
});

// Mettre à jour les statistiques
function updateStats() {
    // Simuler des données
    const stats = {
        sales: 1245,
        users: 562,
        products: 89,
        subscriptions: 342
    };
    
    // Mettre à jour les valeurs
    document.querySelectorAll('.stat-info h3').forEach((stat, index) => {
        const keys = Object.keys(stats);
        if (keys[index]) {
            stat.textContent = stats[keys[index]].toLocaleString();
        }
    });
}

// Configuration des cartes de produits
function setupProductCards() {
    const viewButtons = document.querySelectorAll('.btn-view');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-id');
            
            // Simuler l'ouverture d'une modal de détail
            alert(`Affichage des détails du produit #${productId}. Dans une vraie application, cela ouvrirait une modal.`);
        });
    });
    
    const chatButtons = document.querySelectorAll('.btn-chat');
    
    chatButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-id');
            
            // Simuler la redirection vers le chat
            window.location.href = `index.php?page=chat&product=${productId}`;
        });
    });
}

// Configuration du chat
function setupChat() {
    const chatItems = document.querySelectorAll('.chat-item');
    const sendButton = document.querySelector('.chat-send');
    const messageInput = document.querySelector('.chat-input input');
    
    if (chatItems.length > 0) {
        chatItems.forEach(item => {
            item.addEventListener('click', function() {
                // Retirer la classe active de tous les éléments
                chatItems.forEach(chatItem => {
                    chatItem.classList.remove('active');
                });
                
                // Ajouter la classe active à l'élément cliqué
                this.classList.add('active');
                
                // Simuler le chargement des messages
                loadChatMessages(this.getAttribute('data-user-id'));
            });
        });
    }
    
    if (sendButton && messageInput) {
        sendButton.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
}

// Charger les messages du chat
function loadChatMessages(userId) {
    const chatMessages = document.querySelector('.chat-messages');
    
    // Simuler des messages
    const messages = [
        {
            id: 1,
            sender: 'user',
            text: 'Bonjour, je suis intéressé par votre produit.',
            time: '10:30'
        },
        {
            id: 2,
            sender: 'admin',
            text: 'Bonjour ! Je suis ravi que vous soyez intéressé. Que souhaitez-vous savoir ?',
            time: '10:32'
        },
        {
            id: 3,
            sender: 'user',
            text: 'Est-ce que ce produit est disponible en plusieurs couleurs ?',
            time: '10:35'
        }
    ];
    
    // Afficher les messages
    chatMessages.innerHTML = '';
    
    messages.forEach(message => {
        const messageElement = document.createElement('div');
        messageElement.className = `message ${message.sender === 'admin' ? 'sent' : 'received'}`;
        
        messageElement.innerHTML = `
            <div class="message-content">
                <p>${message.text}</p>
                <div class="message-time">${message.time}</div>
            </div>
        `;
        
        chatMessages.appendChild(messageElement);
    });
    
    // Faire défiler vers le bas
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Envoyer un message
function sendMessage() {
    const messageInput = document.querySelector('.chat-input input');
    const chatMessages = document.querySelector('.chat-messages');
    
    if (messageInput.value.trim() === '') return;
    
    const messageElement = document.createElement('div');
    messageElement.className = 'message sent';
    
    const now = new Date();
    const timeString = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
    
    messageElement.innerHTML = `
        <div class="message-content">
            <p>${messageInput.value}</p>
            <div class="message-time">${timeString}</div>
        </div>
    `;
    
    chatMessages.appendChild(messageElement);
    messageInput.value = '';
    
    // Faire défiler vers le bas
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    // Simuler une réponse automatique après 1 seconde
    setTimeout(() => {
        const replyElement = document.createElement('div');
        replyElement.className = 'message received';
        
        replyElement.innerHTML = `
            <div class="message-content">
                <p>Merci pour votre message. Je vous répondrai dès que possible.</p>
                <div class="message-time">${timeString}</div>
            </div>
        `;
        
        chatMessages.appendChild(replyElement);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }, 1000);
}