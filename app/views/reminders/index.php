<div class="min-h-screen bg-gray-50 pb-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Lembretes</h1>
                    <p class="text-gray-600 mt-1">Gerencie seus lembretes de treino, dieta e bem-estar</p>
                </div>
                <button onclick="openReminderModal()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition duration-150">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Novo Lembrete
                </button>
            </div>
        </div>

        <!-- Lista de Lembretes -->
        <div class="space-y-4" id="remindersList">
            <?php if (!empty($reminders)): ?>
                <?php foreach ($reminders as $reminder): ?>
                    <div class="bg-white rounded-lg shadow p-6 reminder-item" data-id="<?= $reminder['id'] ?>">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <!-- Ícone do Tipo -->
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl
                                    <?php 
                                    switch($reminder['type']) {
                                        case 'treino': echo 'bg-primary-100 text-primary-600'; break;
                                        case 'dieta': echo 'bg-green-100 text-green-600'; break;
                                        case 'agua': echo 'bg-blue-100 text-blue-600'; break;
                                        case 'medicamento': echo 'bg-red-100 text-red-600'; break;
                                        default: echo 'bg-gray-100 text-gray-600'; break;
                                    }
                                    ?>">
                                    <?php 
                                    switch($reminder['type']) {
                                        case 'treino': echo '💪'; break;
                                        case 'dieta': echo '🥗'; break;
                                        case 'agua': echo '💧'; break;
                                        case 'medicamento': echo '💊'; break;
                                        default: echo '⏰'; break;
                                    }
                                    ?>
                                </div>
                                
                                <!-- Informações do Lembrete -->
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($reminder['title']) ?></h3>
                                    <?php if ($reminder['message']): ?>
                                        <p class="text-gray-600 text-sm mt-1"><?= htmlspecialchars($reminder['message']) ?></p>
                                    <?php endif; ?>
                                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <?= date('H:i', strtotime($reminder['time'])) ?>
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <?php 
                                            $days = json_decode($reminder['days_of_week'], true);
                                            $dayNames = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
                                            $activeDays = array_map(function($day) use ($dayNames) {
                                                return $dayNames[$day];
                                            }, $days);
                                            echo implode(', ', $activeDays);
                                            ?>
                                        </span>
                                        <span class="capitalize px-2 py-1 rounded-full text-xs
                                            <?php 
                                            switch($reminder['type']) {
                                                case 'treino': echo 'bg-primary-100 text-primary-800'; break;
                                                case 'dieta': echo 'bg-green-100 text-green-800'; break;
                                                case 'agua': echo 'bg-blue-100 text-blue-800'; break;
                                                case 'medicamento': echo 'bg-red-100 text-red-800'; break;
                                                default: echo 'bg-gray-100 text-gray-800'; break;
                                            }
                                            ?>">
                                            <?= ucfirst($reminder['type']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Controles -->
                            <div class="flex items-center space-x-2">
                                <!-- Toggle Ativo/Inativo -->
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           class="sr-only peer reminder-toggle" 
                                           data-id="<?= $reminder['id'] ?>"
                                           <?= $reminder['is_active'] ? 'checked' : '' ?>>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                                </label>
                                
                                <!-- Botão Editar -->
                                <button onclick="editReminder(<?= $reminder['id'] ?>)" 
                                        class="p-2 text-gray-400 hover:text-primary-600 transition duration-150" 
                                        title="Editar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                
                                <!-- Botão Deletar -->
                                <button onclick="deleteReminder(<?= $reminder['id'] ?>)" 
                                        class="p-2 text-gray-400 hover:text-red-600 transition duration-150" 
                                        title="Deletar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow text-center py-12">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Nenhum Lembrete Configurado</h2>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        Crie lembretes personalizados para treino, dieta, hidratação e mais. 
                        Mantenha-se motivado e no caminho certo para seus objetivos!
                    </p>
                    <button onclick="openReminderModal()" class="bg-primary-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-primary-700 transition duration-150">
                        Criar Primeiro Lembrete
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Dicas sobre Lembretes -->
        <div class="bg-white rounded-lg shadow mt-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">💡 Dicas para Lembretes Eficazes</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Tipos de Lembretes</h4>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li class="flex items-center">
                                <span class="text-lg mr-2">💪</span>
                                <span><strong>Treino:</strong> Horários de exercícios e descanso</span>
                            </li>
                            <li class="flex items-center">
                                <span class="text-lg mr-2">🥗</span>
                                <span><strong>Dieta:</strong> Refeições e suplementos</span>
                            </li>
                            <li class="flex items-center">
                                <span class="text-lg mr-2">💧</span>
                                <span><strong>Água:</strong> Hidratação regular</span>
                            </li>
                            <li class="flex items-center">
                                <span class="text-lg mr-2">💊</span>
                                <span><strong>Medicamento:</strong> Vitaminas e remédios</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Melhores Práticas</h4>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li>• Configure horários realistas e consistentes</li>
                            <li>• Use mensagens motivacionais personalizadas</li>
                            <li>• Ajuste os dias da semana conforme sua rotina</li>
                            <li>• Desative lembretes temporariamente se necessário</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Criar/Editar Lembrete -->
<div id="reminderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Novo Lembrete</h3>
                <button onclick="closeReminderModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="reminderForm" onsubmit="saveReminder(event)">
                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                <input type="hidden" id="reminderId" name="reminder_id">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                        <input type="text" id="reminderTitle" name="title" required maxlength="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                               placeholder="Ex: Hora do treino!">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mensagem</label>
                        <textarea id="reminderMessage" name="message" rows="2" maxlength="255"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                                  placeholder="Mensagem motivacional (opcional)"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo *</label>
                        <select id="reminderType" name="type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">Selecione o tipo</option>
                            <option value="treino">💪 Treino</option>
                            <option value="dieta">🥗 Dieta</option>
                            <option value="agua">💧 Água</option>
                            <option value="medicamento">💊 Medicamento</option>
                            <option value="geral">⏰ Geral</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Horário *</label>
                        <input type="time" id="reminderTime" name="time" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dias da Semana *</label>
                        <div class="grid grid-cols-7 gap-1">
                            <?php 
                            $days = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
                            for ($i = 0; $i < 7; $i++): 
                            ?>
                                <label class="flex flex-col items-center">
                                    <input type="checkbox" name="days_of_week[]" value="<?= $i ?>" 
                                           class="day-checkbox mb-1">
                                    <span class="text-xs text-gray-600"><?= $days[$i] ?></span>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3 mt-6">
                    <button type="button" onclick="closeReminderModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let editingReminderId = null;

// Abrir modal para novo lembrete
function openReminderModal() {
    editingReminderId = null;
    document.getElementById('modalTitle').textContent = 'Novo Lembrete';
    document.getElementById('reminderForm').reset();
    document.getElementById('reminderId').value = '';
    document.getElementById('reminderModal').classList.remove('hidden');
}

// Fechar modal
function closeReminderModal() {
    document.getElementById('reminderModal').classList.add('hidden');
    editingReminderId = null;
}

// Salvar lembrete
async function saveReminder(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Verificar se pelo menos um dia foi selecionado
    const selectedDays = formData.getAll('days_of_week[]');
    if (selectedDays.length === 0) {
        alert('Selecione pelo menos um dia da semana');
        return;
    }
    
    const url = editingReminderId ? '/reminders/update' : '/reminders/create';
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeReminderModal();
            location.reload(); // Recarregar para mostrar alterações
        } else {
            alert(result.error || 'Erro ao salvar lembrete');
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao salvar lembrete');
    }
}

// Editar lembrete
function editReminder(id) {
    // Encontrar o lembrete na página
    const reminderElement = document.querySelector(`[data-id="${id}"]`);
    if (!reminderElement) return;
    
    editingReminderId = id;
    document.getElementById('modalTitle').textContent = 'Editar Lembrete';
    document.getElementById('reminderId').value = id;
    
    // Aqui você precisaria fazer uma requisição para obter os dados do lembrete
    // Por simplicidade, vou mostrar o modal vazio para edição
    document.getElementById('reminderModal').classList.remove('hidden');
}

// Deletar lembrete
async function deleteReminder(id) {
    if (!confirm('Tem certeza que deseja deletar este lembrete?')) {
        return;
    }
    
    try {
        const response = await fetch('/reminders/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                '_token': window.csrfToken,
                'reminder_id': id
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Remover elemento da página
            const reminderElement = document.querySelector(`[data-id="${id}"]`);
            if (reminderElement) {
                reminderElement.remove();
            }
        } else {
            alert(result.error || 'Erro ao deletar lembrete');
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao deletar lembrete');
    }
}

// Toggle ativo/inativo
document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.reminder-toggle');
    
    toggles.forEach(toggle => {
        toggle.addEventListener('change', async function() {
            const id = this.dataset.id;
            
            try {
                const response = await fetch('/reminders/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        '_token': window.csrfToken,
                        'reminder_id': id
                    })
                });
                
                const result = await response.json();
                
                if (!result.success) {
                    // Reverter o toggle se houve erro
                    this.checked = !this.checked;
                    alert(result.error || 'Erro ao alterar status');
                }
            } catch (error) {
                console.error('Erro:', error);
                // Reverter o toggle se houve erro
                this.checked = !this.checked;
                alert('Erro ao alterar status');
            }
        });
    });
});

// Configurar CSRF token global
window.csrfToken = '<?= $csrf_token ?>';
</script>

