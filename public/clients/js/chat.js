let lastMessageId = 0;
let pollingTimer = null;
let isMobile = window.innerWidth <= 768;

/* ===========================
   CHARGEMENT DES MESSAGES
=========================== */
async function loadMessages() {
    try {
        const res = await fetch(
            `${CHAT_CONFIG.getUrl}?conversation_id=${CHAT_CONFIG.conversationId}&last_id=${lastMessageId}`,
            { credentials: 'same-origin' }
        );

        if (!res.ok) return;

        const data = await res.json();
        if (!data.success || !data.messages.length) return;

        const container = document.getElementById('messagesContainer');

        data.messages.forEach(msg => {
            lastMessageId = Math.max(lastMessageId, msg.id);
            container.appendChild(renderMessage(msg));
        });

        scrollToBottom();
    } catch (e) {
        console.error('Erreur chargement messages', e);
    }
}

/* ===========================
   ENVOI MESSAGE
=========================== */
async function sendMessage() {
    const input = document.getElementById('messageInput');
    const text = input.value.trim();
    if (!text) return;

    input.value = '';
    updateSendButton();

    try {
        const res = await fetch(CHAT_CONFIG.sendUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({
                conversation_id: CHAT_CONFIG.conversationId,
                message: text
            })
        });

        const data = await res.json();
        if (!data.success) return;

        const container = document.getElementById('messagesContainer');
        container.appendChild(renderMessage(data.message));
        lastMessageId = data.message.id;

        scrollToBottom();
    } catch (e) {
        console.error('Erreur envoi message', e);
    }
}

/* ===========================
   RENDER MESSAGE
=========================== */
function renderMessage(msg) {
    const wrapper = document.createElement('div');
    const type = msg.sender_id == CHAT_CONFIG.userId ? 'sent' : 'received';

    wrapper.className = `message-group ${type}`;
    wrapper.innerHTML = `
        <div class="message ${type}">
            ${type === 'received' && msg.avatar ? `
                <img src="${msg.avatar}" class="message-avatar">
            ` : ''}
            <div class="message-content">
                <div class="message-bubble">
                    <div class="message-text">${escapeHtml(msg.message)}</div>
                </div>
                <div class="message-time">
                    ${msg.time}
                </div>
            </div>
        </div>
    `;
    return wrapper;
}

/* ===========================
   POLLING
=========================== */
function startPolling() {
    if (pollingTimer) clearInterval(pollingTimer);
    pollingTimer = setInterval(loadMessages, CHAT_CONFIG.pollingInterval);
}

/* ===========================
   UTILITAIRES
=========================== */
function scrollToBottom() {
    const c = document.getElementById('messagesContainer');
    if (c) c.scrollTop = c.scrollHeight;
}

function updateSendButton() {
    const btn = document.getElementById('sendBtn');
    const input = document.getElementById('messageInput');
    btn.disabled = !input.value.trim();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function handleResize() {
    isMobile = window.innerWidth <= 768;
}

/* ===========================
   INIT
=========================== */
document.addEventListener('DOMContentLoaded', () => {
    handleResize();
    window.addEventListener('resize', handleResize);

    loadMessages();
    startPolling();

    const input = document.getElementById('messageInput');
    if (input) {
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
});
