<!-- <link rel="stylesheet" href="./style/chat.css">
 -->
 <style>
    /* chat.css - Styles spécifiques au chat */

/* ========= CONTAINER PRINCIPAL ========= */
.chat-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.chat-header-main {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #eaeaea;
}

.chat-header-main h1 {
    color: #2c3e50;
    font-size: 2.2rem;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.chat-header-main p {
    color: #7f8c8d;
    font-size: 1.1rem;
}

/* ========= GRILLE DES CONVERSATIONS ========= */
.conversations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.conversation-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 1px solid #eaeaea;
}

.conversation-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    border-color: #4361ee;
}

.conversation-card-header {
    display: flex;
    align-items: center;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-bottom: 1px solid #eaeaea;
    position: relative;
}

.conversation-avatar {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    overflow: hidden;
    margin-right: 15px;
    border: 2px solid #4361ee;
}

.conversation-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.conversation-title {
    flex: 1;
}

.conversation-title h4 {
    margin: 0 0 5px 0;
    color: #2c3e50;
    font-size: 1.1rem;
    font-weight: 600;
}

.conversation-time {
    font-size: 0.85rem;
    color: #7f8c8d;
}

.unread-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #e74c3c;
    color: white;
    font-size: 0.8rem;
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: bold;
    min-width: 25px;
    text-align: center;
}

.conversation-card-body {
    padding: 20px;
}

.conversation-preview {
    color: #34495e;
    margin-bottom: 15px;
    line-height: 1.5;
    font-size: 0.95rem;
}

.conversation-product {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #4361ee;
    font-size: 0.9rem;
    font-weight: 500;
}

.conversation-card-footer {
    padding: 15px 20px;
    background: #f8f9fa;
    border-top: 1px solid #eaeaea;
    text-align: right;
}

.btn-view-chat {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #4361ee;
    color: white;
    padding: 8px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-view-chat:hover {
    background: #3a0ca3;
    transform: translateX(3px);
}

.loading-conversations {
    grid-column: 1 / -1;
    text-align: center;
    padding: 50px;
    color: #7f8c8d;
}

.loading-conversations i {
    font-size: 2rem;
    margin-bottom: 15px;
    color: #4361ee;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    margin-top: 30px;
}

.empty-state i {
    font-size: 4rem;
    color: #dfe6e9;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.empty-state p {
    color: #7f8c8d;
    max-width: 500px;
    margin: 0 auto 30px;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    color: white;
    padding: 12px 30px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
}

/* ========= CHAT FULLSCREEN ========= */
.chat-app {
    height: calc(100vh - 80px);
    display: flex;
}

.chat-container-full {
    display: flex;
    flex: 1;
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

/* Sidebar mini */
.chat-sidebar-mini {
    width: 280px;
    background: #f8f9fa;
    border-right: 1px solid #eaeaea;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s ease;
}

.sidebar-header-mini {
    padding: 20px;
    border-bottom: 1px solid #eaeaea;
}

.back-to-conversations {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #4361ee;
    text-decoration: none;
    font-weight: 500;
    padding: 10px;
    border-radius: 8px;
    transition: all 0.3s;
}

.back-to-conversations:hover {
    background: rgba(67, 97, 238, 0.1);
}

.conversations-list-mini {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
}

.conversation-item-mini {
    display: flex;
    align-items: center;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.conversation-item-mini:hover {
    background: white;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
}

.conversation-item-mini.active {
    background: white;
    border-left: 4px solid #4361ee;
    box-shadow: 0 3px 15px rgba(67, 97, 238, 0.15);
}

/* Zone principale */
.chat-main-full {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.chat-header-full {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 30px;
    background: white;
    border-bottom: 1px solid #eaeaea;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
}

.chat-user-info {
    display: flex;
    align-items: center;
    gap: 20px;
}

.mobile-sidebar-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 1.2rem;
    color: #2c3e50;
    cursor: pointer;
    padding: 10px;
}

.user-display {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-avatar-mini {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    font-weight: bold;
    position: relative;
}

.status-dot {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #95a5a6;
    border: 2px solid white;
}

.status-dot.online {
    background: #2ecc71;
}

.status-dot.admin {
    background: #e74c3c;
}

.user-info h2 {
    margin: 0;
    color: #2c3e50;
    font-size: 1.3rem;
}

.user-status {
    font-size: 0.9rem;
    color: #7f8c8d;
}

.chat-actions-full {
    display: flex;
    gap: 10px;
}

.action-btn-full {
    display: flex;
    align-items: center;
    gap: 8px;
    background: white;
    border: 1px solid #eaeaea;
    padding: 10px 20px;
    border-radius: 8px;
    color: #2c3e50;
    cursor: pointer;
    transition: all 0.3s;
    font-weight: 500;
}

.action-btn-full:hover {
    background: #f8f9fa;
    border-color: #4361ee;
    color: #4361ee;
}

/* Messages area */
.messages-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.product-chat-banner {
    background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
    color: white;
    padding: 15px 25px;
    margin: 20px;
    border-radius: 12px;
    display: none;
    animation: slideDown 0.5s ease;
}

.banner-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.product-thumb {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    overflow: hidden;
    border: 3px solid rgba(255, 255, 255, 0.3);
}

.product-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-desc h4 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
}

.product-desc .price {
    margin: 0;
    font-size: 1.2rem;
    font-weight: bold;
    opacity: 0.9;
}

.close-banner {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s;
}

.close-banner:hover {
    background: rgba(255, 255, 255, 0.3);
}

.messages-wrapper-full {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.empty-chat {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.empty-chat i {
    font-size: 4rem;
    margin-bottom: 20px;
    color: #dfe6e9;
}

.empty-chat h3 {
    color: #2c3e50;
    margin-bottom: 10px;
}

/* Messages */
.message-item {
    display: flex;
    max-width: 75%;
    animation: fadeIn 0.3s ease;
}

.message-item.own {
    margin-left: auto;
}

.message-bubble {
    padding: 15px 20px;
    border-radius: 18px;
    position: relative;
    word-wrap: break-word;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.other .message-bubble {
    background: white;
    border: 1px solid #eaeaea;
    border-top-left-radius: 5px;
}

.own .message-bubble {
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    color: white;
    border-top-right-radius: 5px;
}

.message-text {
    font-size: 1rem;
    line-height: 1.5;
}

.message-meta {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 8px;
}

.message-time {
    font-size: 0.8rem;
    opacity: 0.8;
}

.message-read {
    font-size: 0.8rem;
}

.message-read.read {
    color: #2ecc71;
}

.admin-label {
    display: inline-block;
    background: #e74c3c;
    color: white;
    font-size: 0.7rem;
    padding: 3px 10px;
    border-radius: 12px;
    margin-bottom: 8px;
    font-weight: bold;
}

/* Zone de saisie */
.input-area-full {
    padding: 20px 30px;
    border-top: 1px solid #eaeaea;
    background: white;
}

.input-wrapper-full {
    display: flex;
    gap: 15px;
    align-items: flex-end;
}

.message-input-full {
    flex: 1;
    padding: 15px 20px;
    border: 2px solid #eaeaea;
    border-radius: 12px;
    font-size: 1rem;
    resize: none;
    max-height: 150px;
    line-height: 1.5;
    transition: all 0.3s;
    font-family: inherit;
}

.message-input-full:focus {
    outline: none;
    border-color: #4361ee;
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
}

.send-btn-full {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    transition: all 0.3s;
    flex-shrink: 0;
}

.send-btn-full:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
}

.send-btn-full:disabled {
    background: #bdc3c7;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.input-hint {
    margin-top: 10px;
    text-align: center;
}

.input-hint small {
    color: #95a5a6;
    font-size: 0.85rem;
}

.input-hint kbd {
    background: #f8f9fa;
    padding: 3px 8px;
    border-radius: 5px;
    border: 1px solid #dfe6e9;
    font-size: 0.8rem;
}

/* Animations */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 1024px) {
    .chat-sidebar-mini {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1000;
        transform: translateX(-100%);
        background: white;
        width: 300px;
    }
    
    .chat-sidebar-mini.active {
        transform: translateX(0);
    }
    
    .mobile-sidebar-toggle {
        display: block;
    }
}

@media (max-width: 768px) {
    .chat-header-full {
        padding: 15px 20px;
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .chat-actions-full {
        width: 100%;
        justify-content: flex-end;
    }
    
    .action-btn-full span {
        display: none;
    }
    
    .action-btn-full {
        padding: 10px;
    }
    
    .conversations-grid {
        grid-template-columns: 1fr;
    }
    
    .message-item {
        max-width: 85%;
    }
    
    .input-area-full {
        padding: 15px 20px;
    }
    
    .product-chat-banner {
        margin: 10px;
        padding: 12px 15px;
    }
}

@media (max-width: 480px) {
    .chat-container {
        padding: 10px;
    }
    
    .chat-header-main h1 {
        font-size: 1.8rem;
    }
    
    .message-item {
        max-width: 90%;
    }
    
    .message-bubble {
        padding: 12px 15px;
    }
    
    .send-btn-full {
        width: 50px;
        height: 50px;
    }
}

/* Scrollbar styling */
.messages-wrapper-full::-webkit-scrollbar {
    width: 8px;
}

.messages-wrapper-full::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.messages-wrapper-full::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.messages-wrapper-full::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
 </style>
<?php
/* =========================
   CHAT - PAGE PRINCIPALE
========================= */

// Récupérer les paramètres
$seller_id = isset($_GET['seller']) ? intval($_GET['seller']) : 0;
$product_id = isset($_GET['product']) ? intval($_GET['product']) : 0;
$seller_type = isset($_GET['seller_type']) ? $_GET['seller_type'] : 'client';

// Utiliser l'ID client de la session
$client_id = $id_client;

// Vérifier les paramètres minimum
if (!$seller_id || !$product_id) {
    // Si pas de paramètres, afficher la liste des conversations
    displayConversationsList();
} else {
    // Si paramètres, afficher une conversation spécifique
    displayChatConversation($seller_id, $product_id, $client_id, $seller_type);
}

/* =========================
   FONCTIONS D'AFFICHAGE
========================= */

/**
 * Affiche la liste des conversations
 */
function displayConversationsList() {
    global $id_client;
    ?>
    <div class="chat-container">
        <div class="chat-header-main">
            <h1><i class="fas fa-comments"></i> Mes conversations</h1>
            <p>Gérez toutes vos discussions avec les vendeurs</p>
        </div>
        
        <div class="conversations-grid" id="conversationsGrid">
            <!-- Les conversations seront chargées en JS -->
            <div class="loading-conversations">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Chargement de vos conversations...</span>
            </div>
        </div>
        
        <div class="empty-conversations" id="emptyConversations" style="display: none;">
            <div class="empty-state">
                <i class="far fa-comments"></i>
                <h3>Aucune conversation</h3>
                <p>Vous n'avez pas encore de messages. Commencez une conversation en contactant un vendeur !</p>
                <a href="index.php?page=Home" class="btn-primary">
                    <i class="fas fa-shopping-bag"></i> Voir les produits
                </a>
            </div>
        </div>
    </div>
    
    <script>
    // Charger les conversations au démarrage
    document.addEventListener('DOMContentLoaded', function() {
        loadAllConversations();
    });
    
    async function loadAllConversations() {
        try {
            const response = await fetch('/backend/clients/get_conversations.php');
            const result = await response.json();
            
            const grid = document.getElementById('conversationsGrid');
            const emptyState = document.getElementById('emptyConversations');
            
            if (result.success && result.conversations.length > 0) {
                grid.innerHTML = '';
                result.conversations.forEach(conv => {
                    const convElement = createConversationCard(conv);
                    grid.appendChild(convElement);
                });
                emptyState.style.display = 'none';
            } else {
                grid.style.display = 'none';
                emptyState.style.display = 'block';
            }
        } catch (error) {
            console.error('Erreur chargement conversations:', error);
        }
    }
    
    function createConversationCard(conversation) {
        const div = document.createElement('div');
        div.className = 'conversation-card';
        
        const unreadBadge = conversation.unread_count > 0 ? 
            `<span class="unread-badge">${conversation.unread_count}</span>` : '';
        
        const lastMessage = conversation.last_message ? 
            conversation.last_message.substring(0, 60) + '...' : 'Aucun message';
        
        const timeAgo = conversation.last_message_time ? 
            formatTimeAgo(conversation.last_message_time) : '';
        
        div.innerHTML = `
            <div class="conversation-card-header">
                <div class="conversation-avatar">
                    <img src="${conversation.product_image || 'assets/default-product.jpg'}" alt="Produit">
                </div>
                <div class="conversation-title">
                    <h4>${conversation.other_user_name || 'Utilisateur'}</h4>
                    <span class="conversation-time">${timeAgo}</span>
                </div>
                ${unreadBadge}
            </div>
            <div class="conversation-card-body">
                <p class="conversation-preview">${lastMessage}</p>
                <div class="conversation-product">
                    <i class="fas fa-tag"></i>
                    <span>${conversation.product_title || 'Produit'}</span>
                </div>
            </div>
            <div class="conversation-card-footer">
                <a href="index.php?page=Chat&seller=${conversation.seller_id}&product=${conversation.product_id}&seller_type=${conversation.seller_type}" 
                   class="btn-view-chat">
                    <i class="fas fa-comment"></i> Ouvrir
                </a>
            </div>
        `;
        
        return div;
    }
    
    function formatTimeAgo(dateString) {
        // Implémentation simple de time ago
        return 'Aujourd\'hui';
    }
    </script>
    <?php
}

/**
 * Affiche une conversation spécifique
 */
function displayChatConversation($seller_id, $product_id, $client_id, $seller_type) {
    // Vérifications de sécurité
    if ($seller_type == 'client' && $seller_id == $client_id) {
        echo '<div class="error-message">
                <h2><i class="fas fa-exclamation-triangle"></i> Action impossible</h2>
                <p>Vous ne pouvez pas vous contacter vous-même.</p>
                <a href="index.php?page=Chat" class="btn-primary">Retour aux conversations</a>
              </div>';
        return;
    }
    ?>
    <div class="chat-app">
        <div class="chat-container-full">
            <!-- SIDEBAR DES CONVERSATIONS (pour navigation) -->
            <aside class="chat-sidebar-mini">
                <div class="sidebar-header-mini">
                    <a href="index.php?page=Chat" class="back-to-conversations">
                        <i class="fas fa-arrow-left"></i>
                        <span>Conversations</span>
                    </a>
                </div>
                <div class="conversations-list-mini" id="conversationsListMini">
                    <!-- Liste des conversations mini -->
                </div>
            </aside>
            
            <!-- ZONE PRINCIPALE DU CHAT -->
            <main class="chat-main-full">
                <!-- En-tête -->
                <header class="chat-header-full">
                    <div class="chat-user-info">
                        <button class="mobile-sidebar-toggle" onclick="toggleMiniSidebar()">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="user-display">
                            <div class="user-avatar-mini" id="chatAvatarMini">
                                <span id="avatarInitials">V</span>
                                <div class="status-dot" id="statusDot"></div>
                            </div>
                            <div class="user-info">
                                <h2 id="chatUserName">Chargement...</h2>
                                <span id="chatUserStatus" class="user-status">En ligne</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chat-actions-full">
                        <button class="action-btn-full" onclick="showProductDetails()" title="Voir le produit">
                            <i class="fas fa-eye"></i>
                            <span>Voir produit</span>
                        </button>
                        <button class="action-btn-full" onclick="showUserDetails()" title="Informations">
                            <i class="fas fa-info-circle"></i>
                            <span>Info</span>
                        </button>
                    </div>
                </header>
                
                <!-- Messages -->
                <div class="messages-area" id="messagesArea">
                    <!-- Bannière produit -->
                    <div class="product-chat-banner" id="productChatBanner">
                        <div class="banner-content">
                            <div class="product-info">
                                <div class="product-thumb">
                                    <img id="productChatImage" src="" alt="Produit">
                                </div>
                                <div class="product-desc">
                                    <h4 id="productChatTitle">Chargement...</h4>
                                    <p id="productChatPrice" class="price"></p>
                                </div>
                            </div>
                            <button class="close-banner" onclick="hideProductBanner()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Zone des messages -->
                    <div class="messages-wrapper-full" id="messagesWrapperFull">
                        <div class="empty-chat" id="emptyChat">
                            <i class="far fa-comments"></i>
                            <h3>Commencez la conversation</h3>
                            <p>Envoyez votre premier message au vendeur</p>
                        </div>
                    </div>
                </div>
                
                <!-- Zone de saisie -->
                <div class="input-area-full">
                    <div class="input-wrapper-full">
                        <textarea 
                            class="message-input-full" 
                            id="messageInputFull" 
                            placeholder="Tapez votre message ici..." 
                            rows="1"
                        ></textarea>
                        <button class="send-btn-full" id="sendBtnFull" onclick="sendMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="input-hint">
                        <small>Appuyez sur <kbd>Entrée</kbd> pour envoyer</small>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Données passées au JS -->
    <script>
    // Variables globales accessibles
    window.CHAT_DATA = {
        seller_id: <?php echo $seller_id; ?>,
        product_id: <?php echo $product_id; ?>,
        client_id: <?php echo $client_id; ?>,
        seller_type: "<?php echo $seller_type; ?>"
    };
    
    </script>
    <!-- <script src="./js/chat.js"></script> -->
    <script>
        // chat.js - Gestion complète du chat
class ChatSystem {
    constructor() {
        // Variables globales
        this.SELLER_ID = window.CHAT_DATA?.seller_id || 0;
        this.PRODUCT_ID = window.CHAT_DATA?.product_id || 0;
        this.CLIENT_ID = window.CHAT_DATA?.client_id || 0;
        this.SELLER_TYPE = window.CHAT_DATA?.seller_type || 'client';
        
        // État du chat
        this.currentConversationId = null;
        this.pollingInterval = null;
        this.isLoading = false;
        this.lastMessageId = 0;
        
        // Initialisation
        this.init();
    }
    
    async init() {
        try {
            // Attendre que le DOM soit prêt
            await this.waitForDOM();
            
            // Si nous avons une conversation spécifique
            if (this.SELLER_ID && this.PRODUCT_ID) {
                await this.initializeSpecificChat();
            } else {
                // Sinon, nous sommes dans la liste des conversations
                await this.initializeConversationsList();
            }
            
            // Charger les conversations pour la sidebar mini
            await this.loadMiniConversations();
            
            // Mettre à jour les badges de notification
            await this.updateNotificationBadges();
            
        } catch (error) {
            console.error('Erreur initialisation chat:', error);
            this.showError('Impossible de charger le chat');
        }
    }
    
    waitForDOM() {
        return new Promise(resolve => {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', resolve);
            } else {
                resolve();
            }
        });
    }
    
    async initializeSpecificChat() {
        // 1. Charger les infos du produit
        if (this.PRODUCT_ID) {
            await this.loadProductInfo();
        }
        
        // 2. Charger les infos du vendeur
        await this.loadSellerInfo();
        
        // 3. Créer ou charger la conversation
        await this.createOrLoadConversation();
        
        // 4. Initialiser l'interface
        this.initializeChatInterface();
        
        // 5. Démarrer le polling
        this.startMessagePolling();
        
        // 6. Focus sur le champ de message
        setTimeout(() => {
            const input = document.getElementById('messageInputFull');
            if (input) input.focus();
        }, 500);
    }
    
    async initializeConversationsList() {
        // Cette fonction est appelée quand on est dans la liste des conversations
        // Les conversations sont déjà chargées par le PHP
        // On peut ajouter des fonctionnalités supplémentaires ici
        console.log('Mode liste des conversations');
    }
    
    async loadProductInfo() {
        try {
            const response = await fetch(`/backend/clients/product_detail.php?id=${this.PRODUCT_ID}`);
            const product = await response.json();
            
            // Mettre à jour la bannière produit
            const banner = document.getElementById('productChatBanner');
            const image = document.getElementById('productChatImage');
            const title = document.getElementById('productChatTitle');
            const price = document.getElementById('productChatPrice');
            
            if (image && product.image_path) {
                image.src = product.image_path;
            }
            
            if (title) {
                title.textContent = product.title || 'Produit';
            }
            
            if (price) {
                price.textContent = product.price ? `${product.price} €` : 'Prix non disponible';
            }
            
            if (banner) {
                banner.style.display = 'block';
            }
            
            // Mettre à jour le placeholder du message
            const messageInput = document.getElementById('messageInputFull');
            if (messageInput && product.title) {
                messageInput.placeholder = `Posez votre question sur "${product.title.substring(0, 30)}..."`;
            }
            
        } catch (error) {
            console.error('Erreur chargement produit:', error);
        }
    }
    
    async loadSellerInfo() {
        try {
            const response = await fetch(`/backend/clients/get_seller_info.php?id=${this.SELLER_ID}&type=${this.SELLER_TYPE}`);
            const seller = await response.json();
            
            if (seller.success) {
                // Mettre à jour le nom
                const nameElement = document.getElementById('chatUserName');
                const statusElement = document.getElementById('chatUserStatus');
                const avatarElement = document.getElementById('chatAvatarMini');
                const statusDot = document.getElementById('statusDot');
                
                if (nameElement) {
                    nameElement.textContent = seller.username || 
                        (this.SELLER_TYPE === 'admin' ? 'Administrateur' : 'Vendeur');
                }
                
                // Mettre à jour l'avatar
                if (avatarElement) {
                    if (seller.profile_picture) {
                        avatarElement.innerHTML = `<img src="${seller.profile_picture}" alt="Avatar" style="width:100%;height:100%;border-radius:50%;">`;
                    } else {
                        const initials = seller.username ? seller.username.charAt(0).toUpperCase() : 'V';
                        document.getElementById('avatarInitials').textContent = initials;
                    }
                }
                
                // Mettre à jour le statut
                if (statusElement && statusDot) {
                    if (this.SELLER_TYPE === 'admin') {
                        statusElement.textContent = 'Administrateur';
                        statusDot.className = 'status-dot admin';
                    } else {
                        statusElement.textContent = seller.is_online ? 'En ligne' : 'Hors ligne';
                        statusDot.className = seller.is_online ? 'status-dot online' : 'status-dot';
                    }
                }
            }
            
        } catch (error) {
            console.error('Erreur chargement vendeur:', error);
        }
    }
    
    async createOrLoadConversation() {
        if (!this.SELLER_ID || !this.CLIENT_ID || !this.PRODUCT_ID) {
            this.showError('Paramètres manquants pour la conversation');
            return;
        }
        
        const data = {
            seller_id: this.SELLER_ID,
            client_id: this.CLIENT_ID,
            product_id: this.PRODUCT_ID,
            seller_type: this.SELLER_TYPE
        };
        
        try {
            const response = await fetch('/backend/clients/create_chat.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.currentConversationId = result.conversation_id;
                await this.loadMessages();
            } else {
                this.showError(result.message || 'Erreur création conversation');
            }
        } catch (error) {
            console.error('Erreur création chat:', error);
            this.showError('Erreur de connexion au serveur');
        }
    }
    
    async loadMessages() {
        if (!this.currentConversationId) return;
        
        if (this.isLoading) return;
        this.isLoading = true;
        
        try {
            const response = await fetch(`/backend/clients/get_messages.php?conversation_id=${this.currentConversationId}`);
            const result = await response.json();
            
            if (result.success) {
                this.displayMessages(result.messages, result.current_user_id);
                
                // Mettre à jour le dernier message ID
                if (result.messages && result.messages.length > 0) {
                    const lastMsg = result.messages[result.messages.length - 1];
                    this.lastMessageId = lastMsg.message_id;
                }
                
                // Mettre à jour le compteur de non-lus
                this.updateUnreadCount(result.messages);
            }
        } catch (error) {
            console.error('Erreur chargement messages:', error);
        } finally {
            this.isLoading = false;
        }
    }
    
    displayMessages(messages, currentUserId) {
        const container = document.getElementById('messagesWrapperFull');
        const emptyState = document.getElementById('emptyChat');
        
        if (!messages || messages.length === 0) {
            if (emptyState) emptyState.style.display = 'flex';
            if (container) container.innerHTML = '';
            return;
        }
        
        if (emptyState) emptyState.style.display = 'none';
        if (!container) return;
        
        // Grouper les messages par date
        const groupedMessages = this.groupMessagesByDate(messages);
        
        let html = '';
        
        Object.keys(groupedMessages).forEach(date => {
            // Ajouter la date
            html += `<div class="message-date-separator">
                        <span>${this.formatDate(date)}</span>
                     </div>`;
            
            // Ajouter les messages du jour
            groupedMessages[date].forEach(message => {
                const isOwn = message.sender_id == currentUserId;
                const isAdmin = message.sender_type === 'admin';
                
                html += this.createMessageHTML(message, isOwn, isAdmin);
            });
        });
        
        container.innerHTML = html;
        
        // Faire défiler vers le bas
        this.scrollToBottom();
    }
    
    groupMessagesByDate(messages) {
        const groups = {};
        
        messages.forEach(message => {
            const date = new Date(message.created_at).toLocaleDateString('fr-FR');
            
            if (!groups[date]) {
                groups[date] = [];
            }
            
            groups[date].push(message);
        });
        
        return groups;
    }
    
    createMessageHTML(message, isOwn, isAdmin) {
        const adminBadge = isAdmin && !isOwn ? 
            `<div class="admin-label">Admin</div>` : '';
        
        const readIndicator = isOwn ? 
            `<div class="message-read ${message.is_read ? 'read' : ''}">
                <i class="fas fa-check${message.is_read ? '-double' : ''}"></i>
            </div>` : '';
        
        return `
            <div class="message-item ${isOwn ? 'own' : 'other'} ${isAdmin ? 'admin-msg' : ''}">
                <div class="message-bubble">
                    ${adminBadge}
                    <div class="message-text">${this.escapeHtml(message.message)}</div>
                    <div class="message-meta">
                        <span class="message-time">${message.time_only || this.formatTime(message.created_at)}</span>
                        ${readIndicator}
                    </div>
                </div>
            </div>
        `;
    }
    
    async sendMessage() {
        const input = document.getElementById('messageInputFull');
        if (!input) return;
        
        const message = input.value.trim();
        
        if (!message || !this.currentConversationId) {
            return;
        }
        
        // Désactiver le bouton pendant l'envoi
        const sendBtn = document.getElementById('sendBtnFull');
        const originalHTML = sendBtn?.innerHTML;
        
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }
        
        // Déterminer le type d'expéditeur
        const isSeller = (this.CLIENT_ID === this.SELLER_ID && this.SELLER_TYPE === 'client');
        const senderType = isSeller ? 'seller' : 'client';
        
        const data = {
            conversation_id: this.currentConversationId,
            sender_id: this.CLIENT_ID,
            sender_type: senderType,
            message: message,
            product_id: this.PRODUCT_ID
        };
        
        try {
            const response = await fetch('/backend/clients/send_message.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Vider le champ
                input.value = '';
                input.style.height = 'auto';
                
                // Recharger les messages
                await this.loadMessages();
                
                // Jouer un son de notification (optionnel)
                this.playSendSound();
                
                // Mettre à jour la liste des conversations
                await this.loadMiniConversations();
                
            } else {
                this.showError(result.message || 'Erreur lors de l\'envoi');
            }
        } catch (error) {
            console.error('Erreur envoi message:', error);
            this.showError('Erreur de connexion au serveur');
        } finally {
            // Réactiver le bouton
            if (sendBtn) {
                sendBtn.disabled = false;
                sendBtn.innerHTML = originalHTML || '<i class="fas fa-paper-plane"></i>';
            }
        }
    }
    
    startMessagePolling() {
        // Arrêter tout polling existant
        this.stopMessagePolling();
        
        // Démarrer un nouveau polling (toutes les 3 secondes)
        this.pollingInterval = setInterval(async () => {
            if (this.currentConversationId && !this.isLoading) {
                await this.loadMessages();
                await this.updateNotificationBadges();
            }
        }, 3000);
    }
    
    stopMessagePolling() {
        if (this.pollingInterval) {
            clearInterval(this.pollingInterval);
            this.pollingInterval = null;
        }
    }
    
    async loadMiniConversations() {
        try {
            const response = await fetch('/backend/clients/get_conversations.php');
            const result = await response.json();
            
            if (result.success) {
                this.displayMiniConversations(result.conversations);
            }
        } catch (error) {
            console.error('Erreur chargement conversations mini:', error);
        }
    }
    
    displayMiniConversations(conversations) {
        const container = document.getElementById('conversationsListMini');
        if (!container) return;
        
        if (!conversations || conversations.length === 0) {
            container.innerHTML = '<div class="no-conversations-mini">Aucune conversation</div>';
            return;
        }
        
        let html = '';
        
        conversations.forEach(conv => {
            const isActive = conv.conversation_id == this.currentConversationId;
            const unreadBadge = conv.unread_count > 0 ? 
                `<span class="mini-unread-badge">${conv.unread_count}</span>` : '';
            
            const lastMessage = conv.last_message ? 
                conv.last_message.substring(0, 25) + (conv.last_message.length > 25 ? '...' : '') : 
                'Aucun message';
            
            html += `
                <div class="conversation-item-mini ${isActive ? 'active' : ''}" 
                     onclick="window.location.href='index.php?page=Chat&seller=${conv.seller_id}&product=${conv.product_id}&seller_type=${conv.seller_type}'">
                    <div class="mini-avatar">
                        <img src="${conv.product_image || 'assets/default-product.jpg'}" alt="Produit">
                    </div>
                    <div class="mini-info">
                        <div class="mini-header">
                            <strong>${conv.other_user_name || 'Utilisateur'}</strong>
                            ${unreadBadge}
                        </div>
                        <p class="mini-preview">${lastMessage}</p>
                        <small class="mini-product">${conv.product_title || 'Produit'}</small>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    async updateNotificationBadges() {
        try {
            const response = await fetch('/backend/clients/get_conversations.php');
            const result = await response.json();
            
            if (result.success) {
                // Calculer le total des messages non lus
                const totalUnread = result.conversations.reduce((sum, conv) => sum + (conv.unread_count || 0), 0);
                
                // Mettre à jour le badge dans la navbar
                const navbarBadge = document.getElementById('chatNotificationBadge');
                if (navbarBadge) {
                    if (totalUnread > 0) {
                        navbarBadge.textContent = totalUnread > 9 ? '9+' : totalUnread;
                        navbarBadge.style.display = 'inline-flex';
                    } else {
                        navbarBadge.style.display = 'none';
                    }
                }
                
                // Mettre à jour le badge dans le titre
                this.updateTitleBadge(totalUnread);
            }
        } catch (error) {
            console.error('Erreur mise à jour badges:', error);
        }
    }
    
    updateTitleBadge(unreadCount) {
        if (unreadCount > 0) {
            const title = document.title.replace(/^\(\d+\)\s*/, '');
            document.title = `(${unreadCount}) ${title}`;
        } else {
            document.title = document.title.replace(/^\(\d+\)\s*/, '');
        }
    }
    
    updateUnreadCount(messages) {
        if (!messages) return;
        
        const unread = messages.filter(m => 
            m.sender_id != this.CLIENT_ID && !m.is_read
        ).length;
        
        // Vous pouvez utiliser cette valeur pour afficher un badge local
        // Par exemple, dans le header de la conversation
    }
    
    initializeChatInterface() {
        const messageInput = document.getElementById('messageInputFull');
        const sendBtn = document.getElementById('sendBtnFull');
        
        if (!messageInput || !sendBtn) return;
        
        // Activer/désactiver le bouton d'envoi
        messageInput.addEventListener('input', () => {
            sendBtn.disabled = !messageInput.value.trim();
        });
        
        // Support de la touche Entrée (Shift+Enter pour nouvelle ligne)
        messageInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (!sendBtn.disabled) {
                    this.sendMessage();
                }
            }
            
            // Ctrl+Enter pour focus sur le champ
            if (e.key === 'Enter' && e.ctrlKey) {
                e.preventDefault();
                messageInput.focus();
            }
        });
        
        // Auto-resize du textarea
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 150) + 'px';
        });
        
        // Événement pour le bouton d'envoi
        sendBtn.addEventListener('click', () => this.sendMessage());
    }
    
    async loadAllConversations() {
        try {
            const response = await fetch('/backend/clients/get_conversations.php');
            const result = await response.json();
            
            const grid = document.getElementById('conversationsGrid');
            const emptyState = document.getElementById('emptyConversations');
            
            if (result.success && result.conversations.length > 0) {
                if (grid) {
                    grid.innerHTML = '';
                    result.conversations.forEach(conv => {
                        const convElement = this.createConversationCard(conv);
                        grid.appendChild(convElement);
                    });
                }
                if (emptyState) emptyState.style.display = 'none';
            } else {
                if (grid) grid.style.display = 'none';
                if (emptyState) emptyState.style.display = 'block';
            }
        } catch (error) {
            console.error('Erreur chargement conversations:', error);
        }
    }
    
    createConversationCard(conversation) {
        const div = document.createElement('div');
        div.className = 'conversation-card';
        
        const unreadBadge = conversation.unread_count > 0 ? 
            `<span class="unread-badge">${conversation.unread_count}</span>` : '';
        
        const lastMessage = conversation.last_message ? 
            conversation.last_message.substring(0, 60) + (conversation.last_message.length > 60 ? '...' : '') : 
            'Aucun message';
        
        const timeAgo = conversation.last_message_time ? 
            this.formatTimeAgo(conversation.last_message_time) : '';
        
        div.innerHTML = `
            <div class="conversation-card-header">
                <div class="conversation-avatar">
                    <img src="${conversation.product_image || 'assets/default-product.jpg'}" alt="Produit">
                </div>
                <div class="conversation-title">
                    <h4>${conversation.other_user_name || 'Utilisateur'}</h4>
                    <span class="conversation-time">${timeAgo}</span>
                </div>
                ${unreadBadge}
            </div>
            <div class="conversation-card-body">
                <p class="conversation-preview">${lastMessage}</p>
                <div class="conversation-product">
                    <i class="fas fa-tag"></i>
                    <span>${conversation.product_title || 'Produit'}</span>
                </div>
            </div>
            <div class="conversation-card-footer">
                <a href="index.php?page=Chat&seller=${conversation.seller_id}&product=${conversation.product_id}&seller_type=${conversation.seller_type}" 
                   class="btn-view-chat">
                    <i class="fas fa-comment"></i> Ouvrir
                </a>
            </div>
        `;
        
        return div;
    }
    
    // ========= FONCTIONS UTILITAIRES =========
    
    formatDate(dateString) {
        const date = new Date(dateString);
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);
        
        if (date.toDateString() === today.toDateString()) {
            return 'Aujourd\'hui';
        } else if (date.toDateString() === yesterday.toDateString()) {
            return 'Hier';
        } else {
            return date.toLocaleDateString('fr-FR', {
                weekday: 'long',
                day: 'numeric',
                month: 'long'
            });
        }
    }
    
    formatTime(dateString) {
        try {
            const date = new Date(dateString);
            return date.toLocaleTimeString('fr-FR', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false 
            });
        } catch (e) {
            return '--:--';
        }
    }
    
    formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 1) return 'À l\'instant';
        if (diffMins < 60) return `Il y a ${diffMins} min`;
        if (diffHours < 24) return `Il y a ${diffHours} h`;
        if (diffDays === 1) return 'Hier';
        if (diffDays < 7) return `Il y a ${diffDays} j`;
        
        return date.toLocaleDateString('fr-FR', { 
            day: 'numeric', 
            month: 'short' 
        });
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    scrollToBottom() {
        const container = document.getElementById('messagesWrapperFull');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }
    
    showError(message) {
        // Créer une notification d'erreur
        const errorDiv = document.createElement('div');
        errorDiv.className = 'chat-error-notification';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Styles pour l'erreur
        if (!document.getElementById('error-styles')) {
            const styles = document.createElement('style');
            styles.id = 'error-styles';
            styles.textContent = `
                .chat-error-notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: #f44336;
                    color: white;
                    padding: 15px 20px;
                    border-radius: 8px;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    z-index: 10000;
                    animation: slideInRight 0.3s ease;
                    max-width: 400px;
                }
                
                .chat-error-notification i {
                    font-size: 18px;
                }
                
                .chat-error-notification button {
                    background: none;
                    border: none;
                    color: white;
                    cursor: pointer;
                    margin-left: auto;
                }
                
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(styles);
        }
        
        document.body.appendChild(errorDiv);
        
        // Supprimer après 5 secondes
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 5000);
    }
    
    playSendSound() {
        // Créer un son de notification simple
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        } catch (e) {
            // Fallback silencieux si Web Audio API n'est pas supporté
        }
    }
    
    // ========= FONCTIONS GLOBALES =========
    
    toggleMiniSidebar() {
        const sidebar = document.querySelector('.chat-sidebar-mini');
        if (sidebar) {
            sidebar.classList.toggle('active');
        }
    }
    
    hideProductBanner() {
        const banner = document.getElementById('productChatBanner');
        if (banner) {
            banner.style.display = 'none';
        }
    }
    
    async showProductDetails() {
        if (!this.PRODUCT_ID) return;
        
        try {
            const response = await fetch(`/backend/clients/product_detail.php?id=${this.PRODUCT_ID}`);
            const product = await response.json();
            
            // Créer une modale avec les détails du produit
            this.createProductModal(product);
            
        } catch (error) {
            console.error('Erreur chargement détails produit:', error);
            alert('Impossible de charger les détails du produit');
        }
    }
    
    createProductModal(product) {
        // Créer la modale
        const modal = document.createElement('div');
        modal.className = 'product-modal-overlay';
        modal.innerHTML = `
            <div class="product-modal">
                <div class="modal-header">
                    <h3>${this.escapeHtml(product.title || 'Produit')}</h3>
                    <button class="close-modal" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    ${product.image_path ? 
                        `<img src="${product.image_path}" alt="Produit" class="modal-product-image">` : 
                        ''}
                    <div class="modal-details">
                        <p><strong>Prix :</strong> ${product.price || '0'} €</p>
                        <p><strong>Description :</strong> ${this.escapeHtml(product.description || 'Aucune description')}</p>
                        <p><strong>État :</strong> ${product.product_condition || 'Non spécifié'}</p>
                        <p><strong>Stock :</strong> ${product.stock_quantity > 0 ? product.stock_quantity + ' disponibles' : 'Rupture'}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="index.php?page=Home&product=${product.product_id}" class="btn-primary">
                        <i class="fas fa-external-link-alt"></i> Voir sur le site
                    </a>
                </div>
            </div>
        `;
        
        // Ajouter les styles si nécessaire
        if (!document.getElementById('modal-styles')) {
            const styles = document.createElement('style');
            styles.id = 'modal-styles';
            styles.textContent = `
                .product-modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0,0,0,0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                    animation: fadeIn 0.3s ease;
                }
                
                .product-modal {
                    background: white;
                    border-radius: 12px;
                    width: 90%;
                    max-width: 500px;
                    max-height: 90vh;
                    overflow-y: auto;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                }
                
                .modal-header {
                    padding: 20px;
                    border-bottom: 1px solid #eaeaea;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                }
                
                .modal-header h3 {
                    margin: 0;
                    color: #2c3e50;
                }
                
                .close-modal {
                    background: none;
                    border: none;
                    color: #7f8c8d;
                    cursor: pointer;
                    font-size: 1.2rem;
                    padding: 5px;
                }
                
                .modal-body {
                    padding: 20px;
                }
                
                .modal-product-image {
                    width: 100%;
                    max-height: 300px;
                    object-fit: contain;
                    border-radius: 8px;
                    margin-bottom: 20px;
                }
                
                .modal-details p {
                    margin-bottom: 10px;
                    color: #34495e;
                }
                
                .modal-footer {
                    padding: 20px;
                    border-top: 1px solid #eaeaea;
                    text-align: center;
                }
                
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
            `;
            document.head.appendChild(styles);
        }
        
        document.body.appendChild(modal);
        
        // Fermer en cliquant en dehors
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }
    
    async showUserDetails() {
        try {
            const response = await fetch(`/backend/clients/get_seller_info.php?id=${this.SELLER_ID}&type=${this.SELLER_TYPE}`);
            const seller = await response.json();
            
            if (seller.success) {
                let message = `Informations du ${this.SELLER_TYPE === 'admin' ? 'administrateur' : 'vendeur'}:\n\n`;
                message += `Nom : ${seller.username}\n`;
                message += `Type : ${this.SELLER_TYPE === 'admin' ? 'Administrateur' : 'Vendeur'}\n`;
                
                if (seller.role) {
                    message += `Rôle : ${seller.role}\n`;
                }
                
                if (seller.tel) {
                    message += `Téléphone : ${seller.tel}\n`;
                }
                
                if (seller.email) {
                    message += `Email : ${seller.email}\n`;
                }
                
                alert(message);
            }
        } catch (error) {
            alert('Impossible de charger les informations');
        }
    }
    
    // Nettoyage quand on quitte la page
    cleanup() {
        this.stopMessagePolling();
        
        // Réinitialiser le titre
        this.updateTitleBadge(0);
        
        // Supprimer les écouteurs d'événements
        const messageInput = document.getElementById('messageInputFull');
        const sendBtn = document.getElementById('sendBtnFull');
        
        if (messageInput) {
            messageInput.replaceWith(messageInput.cloneNode(true));
        }
        
        if (sendBtn) {
            sendBtn.replaceWith(sendBtn.cloneNode(true));
        }
    }
}

// ========= INITIALISATION =========

// Attendre que le DOM soit prêt
document.addEventListener('DOMContentLoaded', () => {
    // Initialiser le système de chat
    window.chatSystem = new ChatSystem();
    
    // Nettoyer à la fermeture de la page
    window.addEventListener('beforeunload', () => {
        if (window.chatSystem) {
            window.chatSystem.cleanup();
        }
    });
    
    // Exposer les fonctions globales
    window.toggleMiniSidebar = () => {
        if (window.chatSystem) window.chatSystem.toggleMiniSidebar();
    };
    
    window.hideProductBanner = () => {
        if (window.chatSystem) window.chatSystem.hideProductBanner();
    };
    
    window.showProductDetails = () => {
        if (window.chatSystem) window.chatSystem.showProductDetails();
    };
    
    window.showUserDetails = () => {
        if (window.chatSystem) window.chatSystem.showUserDetails();
    };
    
    window.sendMessage = () => {
        if (window.chatSystem) window.chatSystem.sendMessage();
    };
});

// Fonction pour charger toutes les conversations (utilisée dans la liste)
if (typeof loadAllConversations === 'undefined') {
    window.loadAllConversations = async function() {
        if (window.chatSystem) {
            await window.chatSystem.loadAllConversations();
        }
    };
}
    </script>
    <?php
}
?>