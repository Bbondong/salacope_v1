<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ../');
    exit;
}
?>

<?php
// Données des conversations
$conversations = [
    [
        'id' => 1,
        'name' => 'Boutique Électronique',
        'avatar' => 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=200&h=200&fit=crop',
        'last_message' => 'Votre commande a été expédiée ce matin.',
        'time' => '10:30',
        'unread' => 3,
        'online' => true,
        'messages' => [
            [
                'type' => 'received',
                'text' => 'Bonjour ! Votre commande #CMD-2024-00125 est prête.',
                'time' => '09:30',
                'avatar' => 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=200&h=200&fit=crop'
            ],
            [
                'type' => 'sent',
                'text' => 'Super ! Quand sera-t-elle livrée ?',
                'time' => '09:32',
                'status' => 'read'
            ],
            [
                'type' => 'received',
                'text' => 'Livraison prévue demain entre 14h et 18h.',
                'time' => '09:35',
                'avatar' => 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=200&h=200&fit=crop'
            ],
            [
                'type' => 'received',
                'text' => 'Votre colis vient d\'être pris en charge par le transporteur.',
                'time' => '10:30',
                'avatar' => 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=200&h=200&fit=crop'
            ]
        ]
    ],
    [
        'id' => 2,
        'name' => 'Service Client',
        'avatar' => 'https://images.unsplash.com/photo-1544717305-99670f9c28f4?w=200&h=200&fit=crop',
        'last_message' => 'Comment puis-je vous aider aujourd\'hui ?',
        'time' => 'Hier',
        'unread' => 0,
        'online' => true,
        'messages' => [
            [
                'type' => 'received',
                'text' => 'Bonjour, je suis Emma du service client. Comment puis-je vous aider aujourd\'hui ?',
                'time' => '15:20',
                'avatar' => 'https://images.unsplash.com/photo-1544717305-99670f9c28f4?w=200&h=200&fit=crop'
            ],
            [
                'type' => 'sent',
                'text' => 'J\'ai un problème avec mon dernier achat.',
                'time' => '15:22',
                'status' => 'read'
            ]
        ]
    ],
    [
        'id' => 3,
        'name' => 'Support Technique',
        'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop',
        'last_message' => 'Nous avons résolu votre problème.',
        'time' => '14/01',
        'unread' => 0,
        'online' => false,
        'messages' => [
            [
                'type' => 'received',
                'text' => 'Bonjour, équipe support technique. Comment pouvons-nous vous aider ?',
                'time' => '10:15',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop'
            ],
            [
                'type' => 'sent',
                'text' => 'Mon produit ne fonctionne plus correctement.',
                'time' => '10:20',
                'status' => 'read'
            ]
        ]
    ]
];

// Conversation active
$activeConversationId = isset($_GET['conversation']) ? intval($_GET['conversation']) : 1;
$activeConversation = null;

foreach ($conversations as $conv) {
    if ($conv['id'] === $activeConversationId) {
        $activeConversation = $conv;
        break;
    }
}

if (!$activeConversation && count($conversations) > 0) {
    $activeConversation = $conversations[0];
    $activeConversationId = $activeConversation['id'];
}

$totalUnread = array_sum(array_column($conversations, 'unread'));
?>

<div class="chat-app">
    <div class="chat-container">
        <!-- SIDEBAR -->
        <aside class="chat-sidebar">
            <div class="sidebar-header">
                <h1>
                    <i class="fas fa-comments"></i>
                    Messages
                </h1>
                <div class="header-stats">
                    <span><?php echo count($conversations); ?> conversations</span>
                    <?php if ($totalUnread > 0): ?>
                    <span class="unread-count"><?php echo $totalUnread; ?> non lus</span>
                    <?php endif; ?>
                </div>
                
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-input" placeholder="Rechercher une conversation..." 
                           onkeyup="searchConversations(this.value)">
                </div>
            </div>
            
            <div class="conversations-list" id="conversationsList">
                <?php foreach ($conversations as $conversation): ?>
                <div class="conversation-item <?php echo $conversation['id'] === $activeConversationId ? 'active' : ''; ?>" 
                     data-conversation-id="<?php echo $conversation['id']; ?>"
                     onclick="openConversation(<?php echo $conversation['id']; ?>)">
                    
                    <div class="conversation-avatar">
                        <img src="<?php echo htmlspecialchars($conversation['avatar']); ?>" 
                             class="avatar-img">
                        <div class="online-dot <?php echo $conversation['online'] ? '' : 'offline'; ?>"></div>
                    </div>
                    
                    <div class="conversation-content">
                        <div class="conversation-header">
                            <div class="conversation-name"><?php echo htmlspecialchars($conversation['name']); ?></div>
                            <div class="conversation-time"><?php echo htmlspecialchars($conversation['time']); ?></div>
                        </div>
                        
                        <div class="conversation-preview"><?php echo htmlspecialchars($conversation['last_message']); ?></div>
                        
                        <div class="conversation-meta">
                            <?php if ($conversation['unread'] > 0): ?>
                            <div class="unread-badge"><?php echo $conversation['unread']; ?></div>
                            <?php endif; ?>
                            <div class="message-count">
                                <?php echo isset($conversation['messages']) ? count($conversation['messages']) : '0'; ?> messages
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </aside>

        <!-- ZONE PRINCIPALE -->
        <main class="chat-main">
            <?php if ($activeConversation): ?>
            <!-- Header -->
            <header class="chat-header">
                <div class="chat-info">
                    <button class="chat-action-btn mobile-only" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="chat-avatar">
                        <img src="<?php echo htmlspecialchars($activeConversation['avatar']); ?>" 
                             class="chat-avatar-img">
                        <div class="online-dot <?php echo $activeConversation['online'] ? '' : 'offline'; ?>"></div>
                    </div>
                    
                    <div class="chat-details">
                        <h2><?php echo htmlspecialchars($activeConversation['name']); ?></h2>
                        <div class="chat-status">
                            <div class="chat-status-dot"></div>
                            <span><?php echo $activeConversation['online'] ? 'En ligne' : 'Hors ligne'; ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="chat-actions desktop-only">
                    <button class="chat-action-btn" onclick="makeCall()">
                        <i class="fas fa-phone"></i>
                    </button>
                    <button class="chat-action-btn" onclick="showInfo()">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </div>
                
                <div class="chat-actions mobile-only">
                    <button class="chat-action-btn" onclick="makeCall()">
                        <i class="fas fa-phone"></i>
                    </button>
                </div>
            </header>

            <!-- Messages -->
            <div class="messages-container" id="messagesContainer">
                <?php if (isset($activeConversation['messages']) && count($activeConversation['messages']) > 0): ?>
                    <div class="date-separator">
                        <span class="date-label">Aujourd'hui</span>
                    </div>
                    
                    <?php foreach ($activeConversation['messages'] as $message): ?>
                    <div class="message-group <?php echo $message['type']; ?>">
                        <div class="message <?php echo $message['type']; ?>">
                            <?php if ($message['type'] === 'received'): ?>
                            <img src="<?php echo htmlspecialchars($message['avatar']); ?>" 
                                 class="message-avatar">
                            <?php endif; ?>
                            
                            <div class="message-content">
                                <div class="message-bubble">
                                    <div class="message-text"><?php echo htmlspecialchars($message['text']); ?></div>
                                </div>
                                
                                <div class="message-time">
                                    <?php echo htmlspecialchars($message['time']); ?>
                                    <?php if ($message['type'] === 'sent' && isset($message['status'])): ?>
                                    <span class="message-status">
                                        <i class="fas fa-check <?php echo $message['status']; ?>"></i>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if ($activeConversation['online']): ?>
                    <div class="typing-indicator" id="typingIndicator" style="display: none;">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="empty-state">
                        <i class="far fa-comments"></i>
                        <h3>Pas encore de messages</h3>
                        <p>Envoyez votre premier message à <?php echo htmlspecialchars($activeConversation['name']); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- CHAMP DE SAISIE -->
            <div class="message-input-area" id="messageInputArea">
                <div class="input-wrapper">
                    <button class="attachment-btn" onclick="attachFile()">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    
                    <textarea class="message-input" 
                              id="messageInput" 
                              placeholder="Écrivez votre message..." 
                              rows="1"
                              oninput="autoResize(this); updateSendButton()"></textarea>
                    
                    <div class="input-actions">
                        <button class="emoji-btn" onclick="toggleEmojiPicker()">
                            <i class="far fa-smile"></i>
                        </button>
                        
                        <button class="send-btn" id="sendBtn" onclick="sendMessage()" disabled>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <?php else: ?>
            <div class="empty-state">
                <i class="far fa-comments"></i>
                <h3>Sélectionnez une conversation</h3>
                <button class="send-btn" style="width: auto; padding: 0.75rem 2rem; margin-top: 1rem;" 
                        onclick="toggleSidebar()">
                    Voir les conversations
                </button>
            </div>
            <?php endif; ?>
        </main>
    </div>
    
    <!-- Overlay mobile -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    
    <!-- Bouton toggle mobile -->
    <button class="sidebar-toggle mobile-only" onclick="toggleSidebar()">
        <i class="fas fa-comments"></i>
    </button>
</div>

<script>
// Variables
let currentConversationId = <?php echo $activeConversationId; ?>;
let isMobile = window.innerWidth <= 768;
let keyboardVisible = false;
let typingTimeout = null;

// Ouvrir une conversation
function openConversation(conversationId) {
    if (currentConversationId === conversationId) return;
    
    currentConversationId = conversationId;
    
    // Mettre à jour l'URL
    const url = new URL(window.location);
    url.searchParams.set('conversation', conversationId);
    window.history.pushState({ conversationId }, '', url);
    
    // Charger les messages
    loadConversationMessages(conversationId);
    
    // Mettre à jour l'UI
    updateActiveConversation(conversationId);
    
    // Marquer comme lu
    markAsRead(conversationId);
    
    // Sur mobile, fermer la sidebar
    if (isMobile) {
        toggleSidebar();
    }
}

// Charger les messages
function loadConversationMessages(conversationId) {
    const messagesContainer = document.getElementById('messagesContainer');
    
    // Afficher un loader
    messagesContainer.innerHTML = `
        <div style="display: flex; justify-content: center; align-items: center; height: 100%;">
            <div style="width: 40px; height: 40px; border: 3px solid #f3f4f6; border-top-color: #3b82f6; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        </div>
    `;
    
    // Simuler le chargement
    setTimeout(() => {
        window.location.href = `?page=Chat&conversation=${conversationId}`;
    }, 300);
}

// Mettre à jour la conversation active
function updateActiveConversation(conversationId) {
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
    });
    
    const activeItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
    if (activeItem) {
        activeItem.classList.add('active');
    }
}

// Marquer comme lu
function markAsRead(conversationId) {
    const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
    if (conversationItem) {
        const badge = conversationItem.querySelector('.unread-badge');
        if (badge) {
            badge.remove();
        }
    }
}

// Envoyer un message
function sendMessage() {
    const input = document.getElementById('messageInput');
    const messageText = input.value.trim();
    
    if (!messageText) return;
    
    const messagesContainer = document.getElementById('messagesContainer');
    const now = new Date();
    const timeString = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    
    // Créer le message
    const messageDiv = document.createElement('div');
    messageDiv.className = 'message-group sent';
    messageDiv.innerHTML = `
        <div class="message sent">
            <div class="message-content">
                <div class="message-bubble">
                    <div class="message-text">${escapeHtml(messageText)}</div>
                </div>
                <div class="message-time">
                    ${timeString}
                    <span class="message-status">
                        <i class="fas fa-check sent"></i>
                    </span>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter le message
    messagesContainer.appendChild(messageDiv);
    
    // Vider l'input
    input.value = '';
    autoResize(input);
    updateSendButton();
    
    // Scroll
    scrollToBottom();
    
    // Simuler une réponse
    if (<?php echo $activeConversation['online'] ? 'true' : 'false'; ?>) {
        simulateTyping();
        setTimeout(() => {
            sendAutoReply(messageText);
        }, 1500);
    }
    
    // Mettre à jour la conversation
    updateLastMessage(currentConversationId, messageText);
}

// Simuler l'écriture
function simulateTyping() {
    const typingIndicator = document.getElementById('typingIndicator');
    if (typingIndicator) {
        typingIndicator.style.display = 'flex';
        
        if (typingTimeout) {
            clearTimeout(typingTimeout);
        }
        
        typingTimeout = setTimeout(() => {
            typingIndicator.style.display = 'none';
        }, 1500);
    }
}

// Réponse automatique
function sendAutoReply(userMessage) {
    const typingIndicator = document.getElementById('typingIndicator');
    if (typingIndicator) {
        typingIndicator.style.display = 'none';
    }
    
    const messagesContainer = document.getElementById('messagesContainer');
    const now = new Date();
    const timeString = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    
    // Générer réponse
    let replyText = '';
    const messageLower = userMessage.toLowerCase();
    
    if (messageLower.includes('bonjour') || messageLower.includes('salut')) {
        replyText = 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?';
    } else if (messageLower.includes('commande') || messageLower.includes('colis')) {
        replyText = 'Votre commande est en cours de traitement. Elle sera expédiée sous 24h.';
    } else {
        replyText = 'Merci pour votre message. Un conseiller vous répondra rapidement.';
    }
    
    const replyDiv = document.createElement('div');
    replyDiv.className = 'message-group received';
    replyDiv.innerHTML = `
        <div class="message received">
            <img src="<?php echo htmlspecialchars($activeConversation['avatar']); ?>" 
                 class="message-avatar">
            <div class="message-content">
                <div class="message-bubble">
                    <div class="message-text">${escapeHtml(replyText)}</div>
                </div>
                <div class="message-time">${timeString}</div>
            </div>
        </div>
    `;
    
    messagesContainer.appendChild(replyDiv);
    scrollToBottom();
}

// Mettre à jour le dernier message
function updateLastMessage(conversationId, message) {
    const conversationItem = document.querySelector(`.conversation-item[data-conversation-id="${conversationId}"]`);
    if (conversationItem) {
        const preview = conversationItem.querySelector('.conversation-preview');
        if (preview) {
            preview.textContent = message.length > 50 ? message.substring(0, 50) + '...' : message;
        }
        
        const time = conversationItem.querySelector('.conversation-time');
        if (time) {
            const now = new Date();
            time.textContent = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        }
    }
}

// Redimensionner textarea
function autoResize(textarea) {
    textarea.style.height = 'auto';
    const maxHeight = isMobile ? 120 : 160;
    textarea.style.height = Math.min(textarea.scrollHeight, maxHeight) + 'px';
}

// Mettre à jour bouton d'envoi
function updateSendButton() {
    const input = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    sendBtn.disabled = !input.value.trim();
}

// Toggle sidebar
function toggleSidebar() {
    const sidebar = document.querySelector('.chat-sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
    
    // Fermer le clavier si ouvert
    if (isMobile && keyboardVisible) {
        document.activeElement.blur();
    }
}

// Rechercher conversations
function searchConversations(query) {
    const items = document.querySelectorAll('.conversation-item');
    const searchTerm = query.toLowerCase().trim();
    
    items.forEach(item => {
        const name = item.querySelector('.conversation-name').textContent.toLowerCase();
        const preview = item.querySelector('.conversation-preview').textContent.toLowerCase();
        
        if (name.includes(searchTerm) || preview.includes(searchTerm)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

// Joindre fichier
function attachFile() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*, .pdf, .doc, .txt';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            alert(`Fichier "${file.name}" sélectionné. Taille: ${(file.size / 1024).toFixed(1)} KB`);
        }
    };
    input.click();
}

// Appeler
function makeCall() {
    alert(`📞 Appel en cours vers <?php echo htmlspecialchars($activeConversation['name']); ?>...`);
}

// Afficher info
function showInfo() {
    alert(`ℹ️ Informations sur <?php echo htmlspecialchars($activeConversation['name']); ?>`);
}

// Toggle emoji
function toggleEmojiPicker() {
    alert('😊 Sélectionnez un emoji');
}

// Scroll vers le bas
function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    if (container) {
        if (isMobile) {
            container.scrollTop = container.scrollHeight;
        } else {
            container.scrollTo({
                top: container.scrollHeight,
                behavior: 'smooth'
            });
        }
    }
}

// Échapper HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Gérer la taille d'écran
function handleResize() {
    isMobile = window.innerWidth <= 768;
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    handleResize();
    
    // Écouter le resize
    window.addEventListener('resize', handleResize);
    window.addEventListener('orientationchange', function() {
        setTimeout(handleResize, 100);
    });
    
    // Focus sur l'input desktop
    if (!isMobile) {
        const input = document.getElementById('messageInput');
        if (input) {
            setTimeout(() => input.focus(), 500);
        }
    }
    
    // Scroll initial
    scrollToBottom();
    
    // Envoyer avec Enter
    const messageInput = document.getElementById('messageInput');
    if (messageInput) {
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (messageInput.value.trim()) {
                    sendMessage();
                }
            }
        });
    }
    
    // Gérer le back/forward
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.conversationId) {
            openConversation(e.state.conversationId);
        }
    });
    
    // Fix iOS
    document.addEventListener('touchstart', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            e.target.style.fontSize = '16px';
        }
    }, { passive: true });
});
</script>

<style>
/* Styles inline pour mobile/desktop */
.mobile-only { display: none; }
.desktop-only { display: flex; }

@keyframes spin {
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .mobile-only { display: flex; }
    .desktop-only { display: none; }
}
</style>