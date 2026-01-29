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
                        <h2 id="chatName">Sélectionnez une conversation</h2>
                        <div class="chat-status" id="chatStatus">
                            <div class="chat-status-dot"></div>
                            <span id="chatStatusText">Hors ligne</span>
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
                    <textarea class="message-input" id="messageInput" placeholder="Écrivez votre message..." rows="1"></textarea>
                    <div class="input-actions">
                        <button class="emoji-btn" onclick="toggleEmojiPicker()"><i class="far fa-smile"></i></button>
                        <button class="send-btn" id="sendBtn" onclick="sendMessage()" disabled><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Overlay mobile -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    <button class="sidebar-toggle mobile-only" onclick="toggleSidebar()"><i class="fas fa-comments"></i></button>
</div>
