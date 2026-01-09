<h2>Messagerie</h2>
<p class="page-description">Communiquez avec vos clients et vendeurs.</p>

<div class="chat-container">
    <div class="chat-sidebar">
        <div class="chat-search">
            <input type="text" placeholder="Rechercher une conversation...">
        </div>
        <div class="chat-list">
            <!-- Conversation 1 -->
            <div class="chat-item active" data-user-id="1">
                <div class="chat-avatar" style="background-color: #3498db;">JD</div>
                <div class="chat-info">
                    <h4>Jean Dupont</h4>
                    <p>Bonjour, je suis intéressé par votre produit...</p>
                </div>
                <div class="chat-time">10:30</div>
            </div>
            
            <!-- Conversation 2 -->
            <div class="chat-item" data-user-id="2">
                <div class="chat-avatar" style="background-color: #9b59b6;">MM</div>
                <div class="chat-info">
                    <h4>Marie Martin</h4>
                    <p>Est-ce que vous faites des livraisons à domicile ?</p>
                </div>
                <div class="chat-time">09:15</div>
            </div>
            
            <!-- Conversation 3 -->
            <div class="chat-item" data-user-id="3">
                <div class="chat-avatar" style="background-color: #2ecc71;">PL</div>
                <div class="chat-info">
                    <h4>Pierre Lambert</h4>
                    <p>Merci pour votre réponse rapide !</p>
                </div>
                <div class="chat-time">Hier</div>
            </div>
            
            <!-- Conversation 4 -->
            <div class="chat-item" data-user-id="4">
                <div class="chat-avatar" style="background-color: #e74c3c;">SC</div>
                <div class="chat-info">
                    <h4>Sophie Chartier</h4>
                    <p>Quand sera-t-il disponible ?</p>
                </div>
                <div class="chat-time">Hier</div>
            </div>
        </div>
    </div>
    
    <div class="chat-main">
        <div class="chat-header">
            <div class="chat-user">
                <div class="chat-avatar" style="background-color: #3498db;">JD</div>
                <div class="chat-user-info">
                    <h3>Jean Dupont</h3>
                    <p>En ligne</p>
                </div>
            </div>
            <div class="chat-header-actions">
                <button class="btn-action">
                    <i class="fas fa-phone"></i>
                </button>
                <button class="btn-action">
                    <i class="fas fa-video"></i>
                </button>
                <button class="btn-action">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>
        
        <div class="chat-messages">
            <!-- Les messages seront chargés ici via JavaScript -->
        </div>
        
        <div class="chat-input">
            <input type="text" placeholder="Tapez votre message...">
            <button class="chat-send">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>