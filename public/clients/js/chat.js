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