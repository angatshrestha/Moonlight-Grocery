<!-- Chatbot Toggle Button -->
<div id="chatbot-toggle">
    <i class="fas fa-comment-dots"></i>
</div>

<!-- Chatbot Widget -->
<div id="chatbot-widget">
    <div id="chatbot-header">
        <span><i class="fas fa-robot mr-2"></i> AI Assistant</span>
        <button type="button" class="close text-white" id="chatbot-close" style="opacity: 1;">&times;</button>
    </div>
    <div id="chatbot-body">
        <div class="chat-msg bot">
            Hello! Welcome to Moonlight Grocery. How can I help you today?
        </div>
    </div>
    <div id="chatbot-footer">
        <input type="text" id="chatbot-input" placeholder="Type your message..." autocomplete="off">
        <button id="chatbot-send"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('chatbot-toggle');
    const closeBtn = document.getElementById('chatbot-close');
    const widget = document.getElementById('chatbot-widget');
    const input = document.getElementById('chatbot-input');
    const sendBtn = document.getElementById('chatbot-send');
    const body = document.getElementById('chatbot-body');

    // Toggle widget
    toggleBtn.addEventListener('click', () => {
        widget.style.display = widget.style.display === 'flex' ? 'none' : 'flex';
        if(widget.style.display === 'flex') {
            input.focus();
            body.scrollTop = body.scrollHeight;
        }
    });

    closeBtn.addEventListener('click', () => {
        widget.style.display = 'none';
    });

    // Send message
    function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        // Add user message
        appendMessage(text, 'user');
        input.value = '';

        // Show typing indicator
        const typingId = 'typing-' + Date.now();
        appendMessage('<i class="fas fa-circle-notch fa-spin"></i> Typing...', 'bot', typingId);

        // Send to backend
        fetch('/chatbot/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: text })
        })
        .then(response => response.json())
        .then(data => {
            // Remove typing indicator
            document.getElementById(typingId).remove();
            
            if (data.reply) {
                appendMessage(data.reply, 'bot');
            } else {
                appendMessage('Sorry, I encountered an error.', 'bot');
            }
        })
        .catch(error => {
            document.getElementById(typingId).remove();
            appendMessage('Connection error. Please try again later.', 'bot');
            console.error('Error:', error);
        });
    }

    function appendMessage(text, sender, id = null) {
        const div = document.createElement('div');
        div.className = `chat-msg ${sender}`;
        if (id) div.id = id;
        div.innerHTML = text;
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
    }

    sendBtn.addEventListener('click', sendMessage);
    
    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
});
</script>
