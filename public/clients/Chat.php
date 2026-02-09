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
    <?php
}
?>