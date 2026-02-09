<?php
session_start();

// Récupérer les 4 paramètres importants
$seller_id = isset($_GET['seller']) ? intval($_GET['seller']) : 0;
$product_id = isset($_GET['product']) ? intval($_GET['product']) : 0;
$client_id = isset($_GET['client']) ? intval($_GET['client']) : 0;
$seller_type = isset($_GET['seller_type']) ? $_GET['seller_type'] : 'client'; // 'admin' ou 'client'

// Vérifier qu'on a bien les IDs
if (!$seller_id || !$product_id || !$client_id || !$seller_type) {
    die('<div style="text-align:center; padding:50px;">
        <h2>Paramètres manquants</h2>
        <p>Impossible d\'accéder au chat sans les informations nécessaires.</p>
        <a href="products.php">Retour aux produits</a>
    </div>');
}

// Vérifier que le client correspond à l'utilisateur connecté
if ($client_id != $_SESSION['user_id']) {
    die('<div style="text-align:center; padding:50px;">
        <h2>Accès non autorisé</h2>
        <p>Vous ne pouvez pas accéder à ce chat.</p>
        <a href="login.php">Se connecter</a>
    </div>');
}

// Vérifier qu'on ne se contacte pas soi-même (uniquement si c'est un client)
if ($seller_type == 'client' && $seller_id == $client_id) {
    die('<div style="text-align:center; padding:50px;">
        <h2>Action impossible</h2>
        <p>Vous ne pouvez pas vous contacter vous-même.</p>
        <a href="products.php">Retour aux produits</a>
    </div>');
}
?>

<!-- Bannière avec les infos du produit -->
<div id="productBanner" class="product-banner">
    <div class="banner-content">
        <img id="productBannerImage" src="" alt="Produit" class="banner-product-image">
        <div class="banner-product-info">
            <h4 id="productBannerTitle">Chargement du produit...</h4>
            <p id="productBannerPrice"></p>
        </div>
        <button class="close-banner" onclick="hideProductBanner()">×</button>
    </div>
</div>

<!-- Interface de chat existante -->
<div class="chat-app">
    <div class="chat-container">
        <!-- SIDEBAR -->
        <aside class="chat-sidebar">
            <div class="sidebar-header">
                <h1><i class="fas fa-comments"></i> Messages</h1>
                <div class="header-stats">
                    <span id="conversationCount">0 conversations</span>
                    <span id="totalUnread" class="unread-count"></span>
                </div>
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Rechercher une conversation...">
                </div>
            </div>
            <div class="conversations-list" id="conversationsList">
                <!-- Conversations chargées par JS -->
            </div>
        </aside>

        <!-- ZONE PRINCIPALE -->
        <main class="chat-main">
            <header class="chat-header">
                <div class="chat-info">
                    <button class="chat-action-btn mobile-only" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="chat-avatar">
                        <img src="" alt="avatar" class="chat-avatar-img" id="chatAvatar">
                        <div class="online-dot" id="chatOnlineDot"></div>
                    </div>
                    <div class="chat-details">
                        <h2 id="chatName">
                            <?php 
                            if ($seller_type == 'admin') {
                                echo "Admin #$seller_id";
                            } else {
                                echo "Vendeur #$seller_id";
                            }
                            ?>
                        </h2>
                        <div class="chat-status" id="chatStatus">
                            <div class="chat-status-dot" id="chatStatusDot"></div>
                            <span id="chatStatusText">Chargement...</span>
                        </div>
                    </div>
                </div>
                <div class="chat-actions desktop-only">
                    <button class="chat-action-btn" onclick="makeCall()"><i class="fas fa-phone"></i></button>
                    <button class="chat-action-btn" onclick="showInfo()"><i class="fas fa-info-circle"></i></button>
                </div>
            </header>

            <!-- Messages -->
            <div class="messages-container" id="messagesContainer">
                <div class="empty-state">
                    <i class="far fa-comments"></i>
                    <h3>Pas encore de messages</h3>
                    <p>Envoyez votre premier message !</p>
                </div>
            </div>

            <!-- CHAMP DE SAISIE -->
            <div class="message-input-area">
                <div class="input-wrapper">
                    <button class="attachment-btn" onclick="attachFile()"><i class="fas fa-paperclip"></i></button>
                    <textarea 
                        class="message-input" 
                        id="messageInput" 
                        placeholder="Écrivez votre message..." 
                        rows="1"
                    ></textarea>
                    <div class="input-actions">
                        <button class="emoji-btn" onclick="toggleEmojiPicker()"><i class="far fa-smile"></i></button>
                        <button class="send-btn" id="sendBtn" onclick="sendMessage()" disabled>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Overlay mobile -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    <button class="sidebar-toggle mobile-only" onclick="toggleSidebar()">
        <i class="fas fa-comments"></i>
    </button>
</div>

<style>
    /* Styles pour la bannière produit */
    .product-banner {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        color: white;
        padding: 10px 15px;
        margin: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: none;
    }
    
    .product-banner.visible {
        display: block;
    }
    
    .banner-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .banner-product-image {
        width: 40px;
        height: 40px;
        border-radius: 5px;
        object-fit: cover;
        border: 2px solid white;
        margin-right: 10px;
    }
    
    .banner-product-info {
        flex: 1;
    }
    
    .banner-product-info h4 {
        margin: 0 0 5px 0;
        font-size: 14px;
        font-weight: 600;
    }
    
    .banner-product-info p {
        margin: 0;
        font-size: 12px;
        opacity: 0.9;
    }
    
    .close-banner {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
    }
    
    .close-banner:hover {
        background: rgba(255,255,255,0.3);
    }
    
    /* Style pour les messages liés à un produit */
    .message-with-product {
        border-left: 3px solid #4361ee;
        padding-left: 10px;
    }
    
    .product-reference {
        background: #f0f5ff;
        border-radius: 5px;
        padding: 5px 10px;
        margin-top: 5px;
        font-size: 12px;
        display: inline-block;
    }
    
    .product-reference a {
        color: #4361ee;
        text-decoration: none;
    }
    
    /* Badge pour les messages admin */
    .admin-badge {
        background: #ff6b6b;
        color: white;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        margin-right: 8px;
        font-weight: bold;
    }
    
    /* Messages des admins */
    .message.admin-message .message-content {
        border-left: 3px solid #ff6b6b;
    }
    
    /* Indicateur de lecture */
    .read-indicator {
        margin-left: 8px;
        font-size: 12px;
        color: #999;
    }
    
    .read-indicator.read {
        color: #4CAF50;
    }
    
    /* Pour les messages */
    .message {
        margin: 10px 0;
        padding: 10px 15px;
        border-radius: 15px;
        max-width: 70%;
        word-wrap: break-word;
    }
    
    .own-message {
        background: #4361ee;
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 5px;
    }
    
    .other-message {
        background: #f1f3f4;
        color: #333;
        margin-right: auto;
        border-bottom-left-radius: 5px;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .message {
            max-width: 85%;
        }
        
        .product-banner {
            margin: 5px;
            padding: 10px;
        }
    }
</style>

<script>
// Variables globales
const SELLER_ID = <?php echo $seller_id; ?>;
const PRODUCT_ID = <?php echo $product_id; ?>;
const CLIENT_ID = <?php echo $client_id; ?>;
const SELLER_TYPE = <?php echo json_encode($seller_type); ?>;
let currentConversationId = null;
let currentUserIsSeller = false;
let pollingInterval;

// Quand la page charge
document.addEventListener('DOMContentLoaded', function() {
    // 1. Charger les infos du produit
    if (PRODUCT_ID > 0) {
        loadProductInfo();
    }
    
    // 2. Charger les infos du vendeur
    loadSellerInfo();
    
    // 3. Créer ou charger la conversation
    createOrLoadConversation();
    
    // 4. Initialiser l'interface du chat
    initializeChat();
});

// 1. Charger les infos du produit
async function loadProductInfo() {
    try {
        const response = await fetch(`/backend/clients/product_detail.php?id=${PRODUCT_ID}`);
        const product = await response.json();
        
        // Mettre à jour la bannière produit
        if (product.image_path) {
            document.getElementById('productBannerImage').src = product.image_path;
        }
        document.getElementById('productBannerTitle').textContent = product.title;
        document.getElementById('productBannerPrice').textContent = product.price + ' €';
        
        // Afficher la bannière
        document.getElementById('productBanner').classList.add('visible');
        
        // Mettre à jour le placeholder
        const messageInput = document.getElementById('messageInput');
        messageInput.placeholder = `Posez votre question sur "${product.title.substring(0, 30)}..."`;
        
    } catch (error) {
        console.error('Erreur chargement produit:', error);
        // Cacher la bannière si erreur
        document.getElementById('productBanner').classList.add('hidden');
    }
}

// 2. Charger les infos du vendeur
async function loadSellerInfo() {
    try {
        const response = await fetch(`/backend/clients/get_seller_info.php?id=${SELLER_ID}&type=${SELLER_TYPE}`);
        const seller = await response.json();
        
        if (seller.success) {
            document.getElementById('chatName').textContent = seller.username || (SELLER_TYPE === 'admin' ? 'Administrateur' : 'Vendeur');
            
            if (seller.profile_picture) {
                document.getElementById('chatAvatar').src = seller.profile_picture;
            }
            
            // Afficher le statut selon le type
            const statusText = document.getElementById('chatStatusText');
            const statusDot = document.getElementById('chatStatusDot') || document.getElementById('chatOnlineDot');
            
            if (SELLER_TYPE === 'admin') {
                statusText.textContent = 'Administrateur • ' + (seller.role || 'Admin');
                if (statusDot) statusDot.style.backgroundColor = '#ff6b6b';
            } else {
                statusText.textContent = seller.is_online ? 'En ligne' : 'Hors ligne';
                if (statusDot) {
                    statusDot.style.backgroundColor = seller.is_online ? '#4CAF50' : '#ccc';
                }
            }
        }
    } catch (error) {
        console.error('Erreur chargement vendeur:', error);
    }
}

// 3. Créer ou charger la conversation
async function createOrLoadConversation() {
    const data = {
        seller_id: SELLER_ID,
        client_id: CLIENT_ID,
        product_id: PRODUCT_ID,
        seller_type: SELLER_TYPE
    };
    
    try {
        const response = await fetch('/backend/clients/create_chat.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            currentConversationId = result.conversation_id;
            currentUserIsSeller = (CLIENT_ID === SELLER_ID && SELLER_TYPE === 'client');
            
            // Charger les messages
            loadMessages();
            
            // Démarrer le polling des messages
            startMessagePolling();
            
            // Charger les conversations pour la sidebar
            loadConversations();
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur création chat:', error);
        alert('Erreur de connexion au serveur');
    }
}

// 4. Charger les messages
async function loadMessages() {
    if (!currentConversationId) return;
    
    try {
        const response = await fetch(`/backend/clients/get_messages.php?conversation_id=${currentConversationId}`);
        const result = await response.json();
        
        if (result.success) {
            displayMessages(result.messages, result.current_user_id);
            updateUnreadCount(result.messages);
        }
    } catch (error) {
        console.error('Erreur chargement messages:', error);
    }
}

// 5. Afficher les messages
function displayMessages(messages, currentUserId) {
    const container = document.getElementById('messagesContainer');
    
    if (!messages || messages.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="far fa-comments"></i>
                <h3>Pas encore de messages</h3>
                <p>Envoyez votre premier message !</p>
            </div>
        `;
        return;
    }
    
    // Vider le conteneur
    container.innerHTML = '';
    
    messages.forEach(message => {
        const isOwn = message.sender_id == currentUserId;
        const isAdmin = message.sender_type === 'admin';
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isOwn ? 'own-message' : 'other-message'}`;
        
        // Classes supplémentaires
        if (isAdmin && !isOwn) {
            messageDiv.classList.add('admin-message');
        }
        
        // Badge pour les admins
        const adminBadge = isAdmin && !isOwn ? 
            `<span class="admin-badge">Admin</span>` : '';
        
        // Indicateur de lecture
        const readIndicator = isOwn ? 
            `<span class="read-indicator ${message.is_read ? 'read' : 'unread'}">
                <i class="fas fa-check${message.is_read ? '-double' : ''}"></i>
            </span>` : '';
        
        messageDiv.innerHTML = `
            <div class="message-content">
                ${adminBadge}
                <div class="message-text">${escapeHtml(message.message)}</div>
                <div class="message-meta">
                    <span class="message-time">${message.time_only || formatTime(message.created_at)}</span>
                    ${readIndicator}
                </div>
            </div>
        `;
        
        container.appendChild(messageDiv);
    });
    
    // Faire défiler vers le bas
    scrollToBottom();
}

// 6. Envoyer un message
async function sendMessage() {
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (!message || !currentConversationId) return;
    
    // Désactiver le bouton pendant l'envoi
    const sendBtn = document.getElementById('sendBtn');
    sendBtn.disabled = true;
    
    const data = {
        conversation_id: currentConversationId,
        sender_id: CLIENT_ID,
        sender_type: currentUserIsSeller ? 'seller' : 'client',
        message: message,
        product_id: PRODUCT_ID
    };
    
    try {
        const response = await fetch('/backend/clients/send_message.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            messageInput.value = '';
            messageInput.style.height = 'auto';
            
            // Recharger les messages
            await loadMessages();
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur envoi message:', error);
        alert('Erreur d\'envoi du message');
    } finally {
        sendBtn.disabled = false;
    }
}

// 7. Polling pour les nouveaux messages
function startMessagePolling() {
    // Vérifier les nouveaux messages toutes les 5 secondes
    pollingInterval = setInterval(() => {
        if (currentConversationId) {
            loadMessages();
        }
    }, 5000);
}

function stopMessagePolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
}

// 8. Charger les conversations pour la sidebar
async function loadConversations() {
    try {
        const response = await fetch('/backend/clients/get_conversations.php');
        const result = await response.json();
        
        if (result.success) {
            displayConversations(result.conversations);
            document.getElementById('conversationCount').textContent = 
                `${result.count} conversation${result.count !== 1 ? 's' : ''}`;
        }
    } catch (error) {
        console.error('Erreur chargement conversations:', error);
    }
}

// 9. Afficher les conversations dans la sidebar
function displayConversations(conversations) {
    const container = document.getElementById('conversationsList');
    
    if (!conversations || conversations.length === 0) {
        container.innerHTML = '<div class="no-conversations">Aucune conversation</div>';
        return;
    }
    
    container.innerHTML = '';
    
    conversations.forEach(conv => {
        const isActive = conv.conversation_id == currentConversationId;
        const unreadBadge = conv.unread_count > 0 ? 
            `<span class="unread-badge">${conv.unread_count}</span>` : '';
        
        const convElement = document.createElement('div');
        convElement.className = `conversation-item ${isActive ? 'active' : ''}`;
        convElement.innerHTML = `
            <div class="conversation-avatar">
                <img src="${conv.product_image || 'images/default-product.jpg'}" alt="Produit">
            </div>
            <div class="conversation-info">
                <div class="conversation-title">
                    <strong>${conv.other_user_name || 'Utilisateur'}</strong>
                    ${unreadBadge}
                </div>
                <div class="conversation-preview">
                    ${conv.last_message ? conv.last_message.substring(0, 50) + '...' : 'Aucun message'}
                </div>
                <div class="conversation-product">
                    <small>${conv.product_title || 'Produit'}</small>
                </div>
            </div>
        `;
        
        convElement.addEventListener('click', () => {
            // Rediriger vers cette conversation
            window.location.href = `chat.php?seller=${conv.seller_id}&product=${conv.product_id}&client=${CLIENT_ID}&seller_type=${conv.seller_type}`;
        });
        
        container.appendChild(convElement);
    });
}

// 10. Fonctions utilitaires
function hideProductBanner() {
    document.getElementById('productBanner').classList.remove('visible');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatTime(dateString) {
    try {
        const date = new Date(dateString);
        return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    } catch (e) {
        return '--:--';
    }
}

function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
}

function updateUnreadCount(messages) {
    // Compter les messages non lus de l'autre personne
    const unread = messages.filter(m => 
        m.sender_id != CLIENT_ID && !m.is_read
    ).length;
    
    const unreadElement = document.getElementById('totalUnread');
    if (unreadElement) {
        if (unread > 0) {
            unreadElement.textContent = `${unread} non lu${unread > 1 ? 's' : ''}`;
            unreadElement.classList.add('has-unread');
        } else {
            unreadElement.textContent = '';
            unreadElement.classList.remove('has-unread');
        }
    }
}

// 11. Initialisation du chat
function initializeChat() {
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    
    // Activer/désactiver le bouton d'envoi
    messageInput.addEventListener('input', function() {
        sendBtn.disabled = !this.value.trim();
    });
    
    // Support de la touche Entrée (Ctrl+Enter pour nouvelle ligne)
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!sendBtn.disabled) {
                sendMessage();
            }
        }
    });
    
    // Auto-resize du textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
    
    // Focus automatique sur le champ de message
    setTimeout(() => {
        messageInput.focus();
    }, 500);
    
    // Nettoyer le polling quand on quitte la page
    window.addEventListener('beforeunload', stopMessagePolling);
}

// 12. Fonctions existantes de votre chat
function toggleSidebar() {
    const sidebar = document.querySelector('.chat-sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    if (sidebar && overlay) {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }
}

function makeCall() {
    alert('Fonctionnalité d\'appel à implémenter');
}

async function showInfo() {
    try {
        const response = await fetch(`/backend/clients/get_seller_info.php?id=${SELLER_ID}&type=${SELLER_TYPE}`);
        const seller = await response.json();
        
        if (seller.success) {
            alert(`Informations du ${SELLER_TYPE === 'admin' ? 'administrateur' : 'vendeur'}:\n\n` +
                  `Nom: ${seller.username}\n` +
                  `Type: ${SELLER_TYPE === 'admin' ? 'Administrateur' : 'Vendeur'}\n` +
                  `Rôle: ${seller.role || 'Non spécifié'}\n` +
                  `Téléphone: ${seller.tel || 'Non disponible'}`);
        }
    } catch (error) {
        alert('Impossible de charger les informations');
    }
}

function attachFile() {
    alert('Envoi de fichier à implémenter');
}

function toggleEmojiPicker() {
    alert('Sélecteur d\'emoji à implémenter');
}
</script>