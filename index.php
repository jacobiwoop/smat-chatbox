<?php
// vérification du token
require_once 'fonction.php';
$userToken = checkAndSetSmatToken();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Application</title>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --light: #f8f9fa;
            --dark: #212529;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        
        .chat-container {
            display: flex;
            flex-direction: column;
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
            width: 90%;
            overflow: hidden;
        }
        
        .chat-header {
            background: var(--primary);
            color: white;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }
        
        .chat-messages {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            scroll-behavior: smooth;
        }
        
        .message {
            margin-bottom: 15px;
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 18px;
            line-height: 1.4;
            position: relative;
            animation: fadeIn 0.3s ease-out;
        }
        
        .message-outgoing {
            background: var(--primary);
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 5px;
        }
        
        .message-incoming {
            background: #e9ecef;
            color: var(--dark);
            margin-right: auto;
            border-bottom-left-radius: 5px;
        }
        
        .message-timestamp {
            font-size: 0.7rem;
            opacity: 0.7;
            display: block;
            margin-top: 5px;
            text-align: right;
        }
        
        .chat-input {
            display: flex;
            padding: 15px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        
        #messageInput {
            flex-grow: 1;
            padding: 12px 15px;
            border: 1px solid #ced4da;
            border-radius: 25px;
            outline: none;
            transition: border 0.3s;
        }
        
        #messageInput:focus {
            border-color: var(--primary);
        }
        
        #sendButton {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin-left: 10px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }
        
        #sendButton:hover {
            background: var(--secondary);
        }
        
        #sendButton:active {
            transform: scale(0.95);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .typing-indicator {
            display: none;
            padding: 10px 15px;
            background: #e9ecef;
            border-radius: 18px;
            margin-bottom: 15px;
            width: fit-content;
        }
        
        .typing-dots {
            display: inline-block;
        }
        
        .typing-dots span {
            height: 8px;
            width: 8px;
            background: #6c757d;
            border-radius: 50%;
            display: inline-block;
            margin: 0 2px;
            animation: bounce 1.5s infinite ease-in-out;
        }
        
        .typing-dots span:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .typing-dots span:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes bounce {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-5px); }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <h2>Chat Application</h2>
            <div class="token-info">Votre token: <?php echo htmlspecialchars(substr($userToken, 0, 10)) ?>...</div>
        </div>
        
        <div class="chat-messages" id="chatMessages">
            <!-- Messages will appear here -->
            <div class="typing-indicator" id="typingIndicator">
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
        
        <div class="chat-input">
            <input type="text" id="messageInput" placeholder="Tapez votre message..." autocomplete="off">
            <button id="sendButton">→</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatMessages = document.getElementById('chatMessages');
            const messageInput = document.getElementById('messageInput');
            const sendButton = document.getElementById('sendButton');
            const typingIndicator = document.getElementById('typingIndicator');
            
            // Scroll automatique optimisé
            function scrollToBottom() {
                chatMessages.scrollTo({
                    top: chatMessages.scrollHeight,
                    behavior: 'smooth'
                });
            }
            
            // Ajouter un message dans le chat
            function addMessage(text, isOutgoing) {
                const messageDiv = document.createElement('div');
                messageDiv.className = `message message-${isOutgoing ? 'outgoing' : 'incoming'}`;
                
                const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                messageDiv.innerHTML = `
                    ${text}
                    <span class="message-timestamp">${timestamp}</span>
                `;
                
                chatMessages.insertBefore(messageDiv, typingIndicator);
                scrollToBottom();
            }
            
            // Envoyer un message
            async function sendMessage() {
                const text = messageInput.value.trim();
                if (!text) return;
                
                // Afficher le message immédiatement
                addMessage(text, true);
                messageInput.value = '';
                
                // Afficher l'indicateur de typing
                typingIndicator.style.display = 'block';
                scrollToBottom();
                
                try {
                    const response = await fetch('chat.php', {
                        method: 'POST',
                        credentials: 'include',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ text })
                    });
                    
                    if (!response.ok) throw new Error(`Erreur HTTP: ${response.status}`);
                    
                    const data = await response.text();
                    
                    // Simuler un délai de réponse plus naturel
                    setTimeout(() => {
                        typingIndicator.style.display = 'none';
                        addMessage(data, false);
                    }, 500);
                    
                } catch (error) {
                    typingIndicator.style.display = 'none';
                    addMessage(`❌ Erreur: ${error.message}`, false);
                    console.error('Échec:', error);
                }
            }
            
            // Gestionnaires d'événements
            sendButton.addEventListener('click', sendMessage);
            messageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') sendMessage();
            });
            
            // Focus automatique sur l'input
            messageInput.focus();
            
            // Exemple de message de bienvenue
            setTimeout(() => {
                addMessage('Bonjour ! Comment puis-je vous aider ?', false);
            }, 1000);
        });
    </script>
</body>
</html>
