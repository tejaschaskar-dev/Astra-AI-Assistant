document.addEventListener('DOMContentLoaded', function () {
    var input    = document.getElementById('aaa-input');
    var sendBtn  = document.getElementById('aaa-send');
    var messages = document.getElementById('aaa-messages');

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') sendMessage();
    });

    document.querySelectorAll('.aaa-quick-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            input.value = this.textContent;
            sendMessage();
        });
    });

    function sendMessage() {
        var msg = input.value.trim();
        if (!msg) return;
        addMessage(msg, 'user');
        input.value = '';
        var loader = addTyping();
        fetch(AAA.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'aaa_chat', nonce: AAA.nonce, message: msg })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            loader.remove();
            addMessage(data.success ? data.data.reply : 'Error. Try again.', 'bot');
        })
        .catch(function() {
            loader.remove();
            addMessage('Connection error.', 'bot');
        });
    }

    function formatText(text) {
        // Convert numbered lines like "1. something" into <ol><li> list
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

    function addMessage(text, type) {
        var row = document.createElement('div');
        row.className = 'aaa-msg-row ' + type;
        var icon = type === 'bot' ? '🤖' : '👤';
        var content = type === 'bot' ? formatText(text) : text;
        row.innerHTML = '<div class="aaa-msg-icon ' + type + '">' + icon + '</div>'
                      + '<div class="aaa-msg ' + type + '">' + content + '</div>';
        messages.appendChild(row);
        messages.scrollTop = messages.scrollHeight;
        return row;
    }

    function addTyping() {
        var row = document.createElement('div');
        row.className = 'aaa-msg-row bot';
        row.innerHTML = '<div class="aaa-msg-icon bot">🤖</div>'
                      + '<div class="aaa-msg typing">'
                      + '<div class="typing-dots"><span></span><span></span><span></span></div>'
                      + '</div>';
        messages.appendChild(row);
        messages.scrollTop = messages.scrollHeight;
        return row;
    }
});