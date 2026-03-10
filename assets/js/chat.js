document.addEventListener('DOMContentLoaded', function () {
    var input    = document.getElementById('aaa-input');
    var sendBtn  = document.getElementById('aaa-send');
    var messages = document.getElementById('aaa-messages');
    var clearBtn = document.getElementById('aaa-clear');
    var exportBtn= document.getElementById('aaa-export');

    // Load chat history from sessionStorage
    var chatHistory = JSON.parse(sessionStorage.getItem('aaa_chat_history') || '[]');
    loadHistory();

    // Event listeners
    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keydown', function(e) { if (e.key === 'Enter') sendMessage(); });
    if (clearBtn)  clearBtn.addEventListener('click', clearChat);
    if (exportBtn) exportBtn.addEventListener('click', exportChat);

    // Suggested questions map
    var suggestions = {
        'header'     : ['Change header color', 'Make header sticky', 'Transparent header', 'Header logo size'],
        'footer'     : ['Change footer color', 'Add footer widgets', 'Hide footer', 'Footer columns'],
        'sidebar'    : ['Remove sidebar', 'Sidebar width', 'Sidebar on mobile', 'Move sidebar left'],
        'font'       : ['Change body font', 'Increase font size', 'Change heading font', 'Font weight'],
        'color'      : ['Change primary color', 'Change link color', 'Change text color', 'Background color'],
        'logo'       : ['Change logo size', 'Add retina logo', 'Hide logo on mobile', 'Logo padding'],
        'mobile'     : ['Mobile header layout', 'Hide element on mobile', 'Mobile font size', 'Mobile menu'],
        'layout'     : ['Full width layout', 'Boxed layout', 'Content width', 'Change container size'],
        'woocommerce': ['Shop page columns', 'Product sidebar', 'Cart page layout', 'Checkout style'],
        'menu'       : ['Change menu color', 'Sticky menu', 'Mobile menu style', 'Dropdown menu'],
        'default'    : ['Change header color', 'Full width layout', 'Disable sidebar', 'Change fonts'],
    };

    function updateSuggestions(query) {
        var quick = document.getElementById('aaa-quick');
        if (!quick) return;
        var matched = 'default';
        var q = query.toLowerCase();
        Object.keys(suggestions).forEach(function(key) {
            if (q.indexOf(key) !== -1) matched = key;
        });
        quick.innerHTML = '';
        suggestions[matched].forEach(function(s) {
            var btn = document.createElement('button');
            btn.className = 'aaa-quick-btn';
            btn.textContent = s;
            btn.addEventListener('click', function() {
                input.value = s;
                sendMessage();
            });
            quick.appendChild(btn);
        });
    }

    // Update suggestions as user types
    input.addEventListener('input', function() {
        if (this.value.length > 2) updateSuggestions(this.value);
    });

    // Set default suggestions on load
    updateSuggestions('default');

    // Load previous messages from history
    function loadHistory() {
        if (chatHistory.length === 0) return;
        chatHistory.forEach(function(msg) {
            renderMessage(msg.text, msg.type, false);
        });
    }

    function sendMessage() {
        var msg = input.value.trim();
        if (!msg) return;

        // Show and save user message
        renderMessage(msg, 'user', true);
        input.value = '';

        // Show typing
        var loader = addTyping();

        fetch(AAA.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'aaa_chat', nonce: AAA.nonce, message: msg })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            loader.remove();
            var reply = data.success ? data.data.reply : 'Error. Try again.';
            renderMessage(reply, 'bot', true);
        })
        .catch(function() {
            loader.remove();
            renderMessage('Connection error. Please try again.', 'bot', true);
        });
    }

    function renderMessage(text, type, save) {
        var row = document.createElement('div');
        row.className = 'aaa-msg-row ' + type;
        var icon = type === 'bot' ? '🤖' : '👤';
        var content = type === 'bot' ? formatText(text) : text;
        row.innerHTML = '<div class="aaa-msg-icon ' + type + '">' + icon + '</div>'
                      + '<div class="aaa-msg ' + type + '">' + content + '</div>';
        messages.appendChild(row);
        messages.scrollTop = messages.scrollHeight;

        // Save to session history
        if (save) {
            chatHistory.push({ text: text, type: type });
            sessionStorage.setItem('aaa_chat_history', JSON.stringify(chatHistory));
        }
        return row;
    }

    function addTyping() {
        var row = document.createElement('div');
        row.className = 'aaa-msg-row bot';
        row.innerHTML = '<div class="aaa-msg-icon bot">🤖</div>'
                      + '<div class="aaa-msg typing"><div class="typing-dots"><span></span><span></span><span></span></div></div>';
        messages.appendChild(row);
        messages.scrollTop = messages.scrollHeight;
        return row;
    }

    function formatText(text) {
        var lines = text.split('\n');
        var html = '';
        var inList = false;
        lines.forEach(function(line) {
            line = line.trim();
            var numbered = line.match(/^(\d+)\.\s+(.+)/);
            if (numbered) {
                if (!inList) { html += '<ol style="margin:8px 0 8px 18px;padding:0;display:flex;flex-direction:column;gap:6px;">'; inList = true; }
                html += '<li style="padding:4px 0;line-height:1.6">' + numbered[2] + '</li>';
            } else {
                if (inList) { html += '</ol>'; inList = false; }
                if (line !== '') html += '<p style="margin:4px 0;line-height:1.6">' + line + '</p>';
            }
        });
        if (inList) html += '</ol>';
        return html;
    }

    // Clear chat
    function clearChat() {
        chatHistory = [];
        sessionStorage.removeItem('aaa_chat_history');
        messages.innerHTML = '<div class="aaa-time">Today</div>'
            + '<div class="aaa-msg-row bot"><div class="aaa-msg-icon bot">🤖</div>'
            + '<div class="aaa-msg bot">Chat cleared! Ask me anything about Astra Theme.</div></div>';
    }

    // Export chat as text file
    function exportChat() {
        if (chatHistory.length === 0) {
            alert('No chat history to export!');
            return;
        }
        var text = 'Astra AI Assistant - Chat Export\n';
        text += '================================\n';
        text += 'Date: ' + new Date().toLocaleString() + '\n\n';
        chatHistory.forEach(function(msg) {
            var label = msg.type === 'bot' ? 'AI Assistant' : 'You';
            text += label + ':\n' + msg.text + '\n\n';
        });
        var blob = new Blob([text], { type: 'text/plain' });
        var url  = URL.createObjectURL(blob);
        var a    = document.createElement('a');
        a.href   = url;
        a.download = 'astra-ai-chat-' + Date.now() + '.txt';
        a.click();
        URL.revokeObjectURL(url);
    }
});