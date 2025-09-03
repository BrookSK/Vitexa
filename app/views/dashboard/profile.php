<div class="min-h-screen bg-gray-50 pb-20">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Meu Perfil</h1>
                <p class="text-gray-600 mt-1">Gerencie suas informações pessoais e preferências</p>
            </div>
        </div>

        <!-- Formulário de Perfil -->
        <div class="bg-white rounded-lg shadow">
            <form method="POST" action="/profile" class="p-6">
                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                
                <div class="space-y-6">
                    <!-- Informações Pessoais -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informações Pessoais</h3>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nome Completo *
                                </label>
                                <input type="text" id="name" name="name" required
                                       value="<?= htmlspecialchars($this->old('name', $user['name'])) ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <?php if ($this->hasError('name')): ?>
                                    <p class="mt-1 text-sm text-red-600">
                                        <?= htmlspecialchars($this->error('name')[0]) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Email
                                </label>
                                <input type="email" id="email" disabled
                                       value="<?= htmlspecialchars($user['email']) ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500">
                                <p class="mt-1 text-xs text-gray-500">O email não pode ser alterado</p>
                            </div>
                        </div>
                    </div>

                    <!-- Dados Físicos -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Dados Físicos</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="age" class="block text-sm font-medium text-gray-700 mb-1">
                                    Idade *
                                </label>
                                <input type="number" id="age" name="age" min="16" max="100" required
                                       value="<?= htmlspecialchars($this->old('age', $user['age'])) ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <?php if ($this->hasError('age')): ?>
                                    <p class="mt-1 text-sm text-red-600">
                                        <?= htmlspecialchars($this->error('age')[0]) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">
                                    Peso (kg) *
                                </label>
                                <input type="number" id="weight" name="weight" min="30" max="300" step="0.1" required
                                       value="<?= htmlspecialchars($this->old('weight', $user['weight'])) ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <?php if ($this->hasError('weight')): ?>
                                    <p class="mt-1 text-sm text-red-600">
                                        <?= htmlspecialchars($this->error('weight')[0]) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700 mb-1">
                                    Altura (cm) *
                                </label>
                                <input type="number" id="height" name="height" min="100" max="250" required
                                       value="<?= htmlspecialchars($this->old('height', $user['height'])) ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <?php if ($this->hasError('height')): ?>
                                    <p class="mt-1 text-sm text-red-600">
                                        <?= htmlspecialchars($this->error('height')[0]) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Objetivo -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Objetivo</h3>
                        
                        <div>
                            <label for="goal" class="block text-sm font-medium text-gray-700 mb-1">
                                Qual é o seu objetivo principal? *
                            </label>
                            <select id="goal" name="goal" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Selecione seu objetivo</option>
                                <option value="perder_peso" <?= $this->old('goal', $user['goal']) === 'perder_peso' ? 'selected' : '' ?>>
                                    Perder peso
                                </option>
                                <option value="ganhar_massa" <?= $this->old('goal', $user['goal']) === 'ganhar_massa' ? 'selected' : '' ?>>
                                    Ganhar massa muscular
                                </option>
                                <option value="manter_forma" <?= $this->old('goal', $user['goal']) === 'manter_forma' ? 'selected' : '' ?>>
                                    Manter a forma
                                </option>
                                <option value="melhorar_condicionamento" <?= $this->old('goal', $user['goal']) === 'melhorar_condicionamento' ? 'selected' : '' ?>>
                                    Melhorar condicionamento físico
                                </option>
                            </select>
                            <?php if ($this->hasError('goal')): ?>
                                <p class="mt-1 text-sm text-red-600">
                                    <?= htmlspecialchars($this->error('goal')[0]) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Informações da Conta -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informações da Conta</h3>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Membro desde:</span>
                                    <span class="font-medium text-gray-900 ml-2">
                                        <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Última atualização:</span>
                                    <span class="font-medium text-gray-900 ml-2">
                                        <?= date('d/m/Y H:i', strtotime($user['updated_at'])) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit"
                                    class="flex-1 bg-primary-600 text-white py-2 px-4 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition duration-150">
                                Salvar Alterações
                            </button>
                            
                            <button type="button" onclick="showChangePasswordModal()"
                                    class="flex-1 bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-150">
                                Alterar Senha
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Estatísticas do Perfil -->
        <div class="bg-white rounded-lg shadow mt-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Estatísticas do Perfil</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <?php 
                    $bmi = round($user['weight'] / (($user['height'] / 100) ** 2), 1);
                    $bmiCategory = $bmi < 18.5 ? 'Abaixo do peso' : ($bmi < 25 ? 'Normal' : ($bmi < 30 ? 'Sobrepeso' : 'Obesidade'));
                    $bmiColor = $bmi < 18.5 ? 'text-blue-600' : ($bmi < 25 ? 'text-green-600' : ($bmi < 30 ? 'text-yellow-600' : 'text-red-600'));
                    ?>
                    
                    <div class="text-center">
                        <div class="text-2xl font-bold <?= $bmiColor ?>"><?= $bmi ?></div>
                        <div class="text-sm text-gray-500">IMC</div>
                        <div class="text-xs text-gray-400"><?= $bmiCategory ?></div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary-600"><?= $user['age'] ?></div>
                        <div class="text-sm text-gray-500">Anos</div>
                        <div class="text-xs text-gray-400">Idade</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-2xl font-bold text-secondary-600"><?= $user['weight'] ?>kg</div>
                        <div class="text-sm text-gray-500">Peso</div>
                        <div class="text-xs text-gray-400">Atual</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-2xl font-bold text-success-600"><?= $user['height'] ?>cm</div>
                        <div class="text-sm text-gray-500">Altura</div>
                        <div class="text-xs text-gray-400">Medida</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Alterar Senha -->
<div id="changePasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Alterar Senha</h3>
                <button onclick="hideChangePasswordModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="changePasswordForm" onsubmit="changePassword(event)">
                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Senha Atual</label>
                        <input type="password" name="current_password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
                        <input type="password" name="new_password" required minlength="8"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Senha</label>
                        <input type="password" name="new_password_confirmation" required minlength="8"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div class="flex space-x-3 mt-6">
                    <button type="button" onclick="hideChangePasswordModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                        Alterar Senha
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.remove('hidden');
}

function hideChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.add('hidden');
    document.getElementById('changePasswordForm').reset();
}

async function changePassword(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Verificar se as senhas coincidem
    const newPassword = formData.get('new_password');
    const confirmPassword = formData.get('new_password_confirmation');
    
    if (newPassword !== confirmPassword) {
        alert('As senhas não coincidem');
        return;
    }
    
    try {
        const response = await fetch('/profile/change-password', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            hideChangePasswordModal();
            alert('Senha alterada com sucesso!');
        } else {
            alert(result.error || 'Erro ao alterar senha');
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao alterar senha');
    }
}

// Calcular IMC em tempo real
document.addEventListener('DOMContentLoaded', function() {
    const weightInput = document.getElementById('weight');
    const heightInput = document.getElementById('height');
    
    function updateBMI() {
        const weight = parseFloat(weightInput.value);
        const height = parseFloat(heightInput.value) / 100;
        
        if (weight && height) {
            const bmi = (weight / (height * height)).toFixed(1);
            console.log('IMC calculado:', bmi);
        }
    }
    
    weightInput.addEventListener('input', updateBMI);
    heightInput.addEventListener('input', updateBMI);
});
</script>

