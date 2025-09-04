<div class="min-h-screen bg-gray-100 flex flex-col">
    <!-- Header do Chat -->
    <div class="bg-white shadow-sm border-b border-gray-200 px-4 py-3 flex items-center justify-between">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-3">
                🤖
            </div>
            <div>
                <h1 class="text-lg font-semibold text-gray-900">Vitexa AI</h1>
                <p class="text-sm text-green-600 flex items-center">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    Online
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="clearChat()" class="p-2 text-gray-400 hover:text-gray-600 transition duration-150" title="Limpar conversa">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Área de Mensagens -->
    <div id="messagesContainer" class="flex-1 overflow-y-auto px-4 py-4 space-y-4 pb-20">
        <!-- Mensagem de Boas-vindas -->
        <div class="flex items-start space-x-3">
            <div class="w-8 h-8 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                🤖
            </div>
            <div class="bg-white rounded-lg shadow-sm p-3 max-w-xs lg:max-w-md">
                <p class="text-gray-800 text-sm">
                    Olá, <?= htmlspecialchars($user['name']) ?>! 👋<br><br>
                    Sou seu assistente virtual de fitness e nutrição. Estou aqui para ajudar você com:
                    <br><br>
                    • Dúvidas sobre exercícios<br>
                    • Dicas de alimentação<br>
                    • Motivação para treinar<br>
                    • Esclarecimentos sobre seus planos<br><br>
                    Como posso te ajudar hoje? 💪
                </p>
                <div class="text-xs text-gray-500 mt-2">Agora</div>
            </div>
        </div>

        <!-- Mensagens do Histórico -->
        <?php if (!empty($conversations)): ?>
            <?php foreach ($conversations as $conversation): ?>
                <?php if ($conversation['type'] === 'user'): ?>
                    <!-- Mensagem do Usuário -->
                    <div class="flex items-start space-x-3 justify-end">
                        <div class="bg-primary-500 text-white rounded-lg shadow-sm p-3 max-w-xs lg:max-w-md">
                            <p class="text-sm"><?= nl2br(htmlspecialchars($conversation['message'])) ?></p>
                            <div class="text-xs text-primary-100 mt-2">
                                <?= date('H:i', strtotime($conversation['created_at'])) ?>
                            </div>
                        </div>
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-sm font-bold flex-shrink-0">
                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Mensagem da IA -->
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                            🤖
                        </div>
                        <div class="bg-white rounded-lg shadow-sm p-3 max-w-xs lg:max-w-md">
                            <p class="text-gray-800 text-sm"><?= nl2br(htmlspecialchars($conversation['message'])) ?></p>
                            <div class="text-xs text-gray-500 mt-2">
                                <?= date('H:i', strtotime($conversation['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Indicador de digitação (oculto por padrão) -->
        <div id="typingIndicator" class="flex items-start space-x-3 hidden">
            <div class="w-8 h-8 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                🤖
            </div>
            <div class="bg-white rounded-lg shadow-sm p-3">
                <div class="flex space-x-1">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Área de Input -->
    <div class="bg-white border-t border-gray-200 px-4 py-3 fixed bottom-0 left-0 right-0 md:relative">
        <form id="chatForm" onsubmit="sendMessage(event)" class="flex items-center space-x-3">
            <input type="hidden" name="_token" value="<?= $csrf_token ?>">
            <div class="flex-1 relative">
                <input
                    type="text"
                    id="messageInput"
                    name="message"
                    placeholder="Digite sua mensagem..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    maxlength="1000"
                    required>
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-xs text-gray-400">
                    <span id="charCount">0</span>/1000
                </div>
            </div>
            <button
                type="submit"
                id="sendButton"
                class="bg-primary-500 text-white p-2 rounded-full hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500 transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </button>
        </form>
    </div>

    <!-- Sugestões de Perguntas (aparece quando não há mensagens) -->
    <?php if (empty($conversations)): ?>
        <div class="px-4 pb-4">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3">💡 Perguntas frequentes:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <button onclick="sendSuggestedMessage('Como posso melhorar meu treino?')"
                        class="text-left p-2 text-sm text-gray-600 hover:bg-gray-50 rounded border border-gray-200 transition duration-150">
                        Como posso melhorar meu treino?
                    </button>
                    <button onclick="sendSuggestedMessage('Que alimentos devo evitar?')"
                        class="text-left p-2 text-sm text-gray-600 hover:bg-gray-50 rounded border border-gray-200 transition duration-150">
                        Que alimentos devo evitar?
                    </button>
                    <button onclick="sendSuggestedMessage('Como manter a motivação?')"
                        class="text-left p-2 text-sm text-gray-600 hover:bg-gray-50 rounded border border-gray-200 transition duration-150">
                        Como manter a motivação?
                    </button>
                    <button onclick="sendSuggestedMessage('Dicas para dormir melhor?')"
                        class="text-left p-2 text-sm text-gray-600 hover:bg-gray-50 rounded border border-gray-200 transition duration-150">
                        Dicas para dormir melhor?
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Variáveis globais
    let isTyping = false;

    // Inicialização
    document.addEventListener('DOMContentLoaded', function() {
        const messageInput = document.getElementById('messageInput');
        const charCount = document.getElementById('charCount');

        // Contador de caracteres
        messageInput.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });

        // Scroll para o final das mensagens
        scrollToBottom();

        // Focar no input
        messageInput.focus();
    });

    // Enviar mensagem
    async function sendMessage(event) {
        event.preventDefault();

        if (isTyping) return;

        const form = event.target;
        const formData = new FormData(form);
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        const message = messageInput.value.trim();

        if (!message) return;

        // Desabilitar input e botão
        messageInput.disabled = true;
        sendButton.disabled = true;
        isTyping = true;

        // Adicionar mensagem do usuário à interface
        addUserMessage(message);

        // Limpar input
        messageInput.value = '';
        document.getElementById('charCount').textContent = '0';

        // Mostrar indicador de digitação
        showTypingIndicator();

        try {
            const response = await fetch('/chat/send', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Adicionar resposta da IA
                addAIMessage(result.ai_message.message);
            } else {
                addAIMessage('Desculpe, ocorreu um erro. Tente novamente.');
            }
        } catch (error) {
            console.error('Erro:', error);
            addAIMessage('Desculpe, não consegui processar sua mensagem. Verifique sua conexão e tente novamente.');
        } finally {
            // Reabilitar input e botão
            messageInput.disabled = false;
            sendButton.disabled = false;
            isTyping = false;

            // Esconder indicador de digitação
            hideTypingIndicator();

            // Focar no input
            messageInput.focus();
        }
    }

    // Adicionar mensagem do usuário
    function addUserMessage(message) {
        const container = document.getElementById('messagesContainer');
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex items-start space-x-3 justify-end';
        messageDiv.innerHTML = `
        <div class="bg-primary-500 text-white rounded-lg shadow-sm p-3 max-w-xs lg:max-w-md">
            <p class="text-sm">${escapeHtml(message).replace(/\n/g, '<br>')}</p>
            <div class="text-xs text-primary-100 mt-2">
                ${new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}
            </div>
        </div>
        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 text-sm font-bold flex-shrink-0">
            <?= strtoupper(substr($user['name'], 0, 1)) ?>
        </div>
    `;

        container.appendChild(messageDiv);
        scrollToBottom();
    }

    // Adicionar mensagem da IA
    function addAIMessage(message) {
        const container = document.getElementById('messagesContainer');
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex items-start space-x-3';
        messageDiv.innerHTML = `
        <div class="w-8 h-8 bg-gradient-to-r from-primary-500 to-secondary-500 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
            🤖
        </div>
        <div class="bg-white rounded-lg shadow-sm p-3 max-w-xs lg:max-w-md">
            <p class="text-gray-800 text-sm">${escapeHtml(message).replace(/\n/g, '<br>')}</p>
            <div class="text-xs text-gray-500 mt-2">
                ${new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}
            </div>
        </div>
    `;

        container.appendChild(messageDiv);
        scrollToBottom();
    }

    // Mostrar indicador de digitação
    function showTypingIndicator() {
        document.getElementById('typingIndicator').classList.remove('hidden');
        scrollToBottom();
    }

    // Esconder indicador de digitação
    function hideTypingIndicator() {
        document.getElementById('typingIndicator').classList.add('hidden');
    }

    // Scroll para o final
    function scrollToBottom() {
        const container = document.getElementById('messagesContainer');
        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 100);
    }

    // Enviar mensagem sugerida
    function sendSuggestedMessage(message) {
        const messageInput = document.getElementById('messageInput');
        messageInput.value = message;
        document.getElementById('chatForm').dispatchEvent(new Event('submit'));
    }

    // Limpar chat
    async function clearChat() {
        if (!confirm('Tem certeza que deseja limpar todo o histórico de conversa?')) {
            return;
        }

        try {
            const response = await fetch('/chat/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    '_token': window.csrfToken
                })
            });

            const result = await response.json();

            if (result.success) {
                // Limpar mensagens da interface
                const container = document.getElementById('messagesContainer');
                const messages = container.querySelectorAll('.flex:not(#typingIndicator)');
                messages.forEach((msg, index) => {
                    if (index > 0) { // Manter mensagem de boas-vindas
                        msg.remove();
                    }
                });
            } else {
                alert('Erro ao limpar histórico');
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao limpar histórico');
        }
    }

    // Utilitário para escapar HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Configurar CSRF token global
    window.csrfToken = '<?= $csrf_token ?>';
</script>

<style>
    /* Animações personalizadas */
    @keyframes bounce {

        0%,
        80%,
        100% {
            transform: translateY(0);
        }

        40% {
            transform: translateY(-6px);
        }
    }

    .animate-bounce {
        animation: bounce 1.4s infinite;
    }

    /* Scroll suave */
    #messagesContainer {
        scroll-behavior: smooth;
    }

    /* Responsividade para mobile */
    @media (max-width: 768px) {
        .max-w-xs {
            max-width: calc(100vw - 120px);
        }
    }
</style>