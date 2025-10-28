document.getElementById('chatbot-button').onclick = function() {
    const container = document.getElementById('chatbot-container');
    container.style.display = 'flex';
    document.getElementById('chatbot-input').focus();
};

document.getElementById('chatbot-send').onclick = function() {
    const message = document.getElementById('chatbot-input').value;
    if (message) {
        // Mostrar el mensaje del usuario
        addMessage(message, 'user');

        // Enviar la pregunta al backend (chatbot.php)
        fetch('/public/chatbot/chatbot.php', {
            method: 'POST',
            body: JSON.stringify({ message: message }),
            headers: { 'Content-Type': 'application/json' },
        })
        .then(response => response.json())
        .then(data => {
            // Mostrar la respuesta del chatbot
            addMessage(data.response, 'bot');
        })
        .catch(error => console.error('Error:', error));

        document.getElementById('chatbot-input').value = '';
    }
};

// Función para agregar un mensaje al chat
function addMessage(message, sender) {
    const chatbox = document.getElementById('chatbox');
    const div = document.createElement('div');
    div.classList.add('message', sender === 'bot' ? 'bot-message' : 'user-message');
    div.textContent = message;
    chatbox.appendChild(div);
    chatbox.scrollTop = chatbox.scrollHeight; // Desplazarse hacia abajo
}
