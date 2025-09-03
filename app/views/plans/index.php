<div class="min-h-screen bg-gray-50 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Meus Planos</h1>
                <p class="text-gray-600 mt-1">Gerencie seus planos de treino e dieta personalizados</p>
            </div>
        </div>

        <!-- Botões de Geração -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-lg shadow-lg text-white p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold mb-2">Plano de Treino</h3>
                        <p class="text-primary-100 mb-4">Exercícios personalizados com IA</p>
                        <button onclick="generatePlan('treino')" 
                                class="bg-white text-primary-600 px-4 py-2 rounded-lg font-medium hover:bg-primary-50 transition duration-150">
                            <span id="workout-btn-text">Gerar Novo Plano</span>
                            <span id="workout-loading" class="hidden">Gerando...</span>
                        </button>
                    </div>
                    <div class="text-6xl opacity-20">
                        <svg fill="currentColor" viewBox="0 0 24 24" class="w-16 h-16">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-success-500 to-success-600 rounded-lg shadow-lg text-white p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold mb-2">Plano de Dieta</h3>
                        <p class="text-success-100 mb-4">Cardápio balanceado com IA</p>
                        <button onclick="generatePlan('dieta')" 
                                class="bg-white text-success-600 px-4 py-2 rounded-lg font-medium hover:bg-success-50 transition duration-150">
                            <span id="diet-btn-text">Gerar Novo Plano</span>
                            <span id="diet-loading" class="hidden">Gerando...</span>
                        </button>
                    </div>
                    <div class="text-6xl opacity-20">
                        <svg fill="currentColor" viewBox="0 0 24 24" class="w-16 h-16">
                            <path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Planos de Treino -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Planos de Treino
                    </h2>
                    <a href="<?= APP_URL ?>/plans/workout" class="text-primary-600 text-sm hover:text-primary-800">Ver detalhes</a>
                </div>
                <div class="p-6">
                    <?php if (!empty($workout_plans)): ?>
                        <div class="space-y-4">
                            <?php foreach ($workout_plans as $plan): ?>
                                <div class="border border-gray-200 rounded-lg p-4 <?= $plan['status'] === 'ativo' ? 'border-primary-200 bg-primary-50' : '' ?>">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="font-medium text-gray-900"><?= htmlspecialchars($plan['title']) ?></h3>
                                            <p class="text-sm text-gray-500 mt-1">
                                                Criado em <?= date('d/m/Y', strtotime($plan['created_at'])) ?>
                                            </p>
                                            <?php if (isset($plan['content']['duration_weeks'])): ?>
                                                <p class="text-xs text-gray-400">
                                                    Duração: <?= $plan['content']['duration_weeks'] ?> semanas
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <?php if ($plan['status'] === 'ativo'): ?>
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Ativo</span>
                                            <?php else: ?>
                                                <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">Inativo</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($plan['status'] === 'ativo' && isset($plan['content']['description'])): ?>
                                        <div class="mt-3 p-3 bg-white rounded border">
                                            <p class="text-sm text-gray-600"><?= htmlspecialchars($plan['content']['description']) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <p class="text-gray-500 mb-4">Nenhum plano de treino criado</p>
                            <button onclick="generatePlan('treino')" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-primary-700">
                                Criar Primeiro Plano
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Planos de Dieta -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-success-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                        Planos de Dieta
                    </h2>
                    <a href="<?= APP_URL ?>/plans/diet" class="text-success-600 text-sm hover:text-success-800">Ver detalhes</a>
                </div>
                <div class="p-6">
                    <?php if (!empty($diet_plans)): ?>
                        <div class="space-y-4">
                            <?php foreach ($diet_plans as $plan): ?>
                                <div class="border border-gray-200 rounded-lg p-4 <?= $plan['status'] === 'ativo' ? 'border-success-200 bg-success-50' : '' ?>">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="font-medium text-gray-900"><?= htmlspecialchars($plan['title']) ?></h3>
                                            <p class="text-sm text-gray-500 mt-1">
                                                Criado em <?= date('d/m/Y', strtotime($plan['created_at'])) ?>
                                            </p>
                                            <?php if (isset($plan['content']['daily_calories'])): ?>
                                                <p class="text-xs text-gray-400">
                                                    <?= $plan['content']['daily_calories'] ?> kcal/dia
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <?php if ($plan['status'] === 'ativo'): ?>
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Ativo</span>
                                            <?php else: ?>
                                                <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">Inativo</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($plan['status'] === 'ativo' && isset($plan['content']['description'])): ?>
                                        <div class="mt-3 p-3 bg-white rounded border">
                                            <p class="text-sm text-gray-600"><?= htmlspecialchars($plan['content']['description']) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                            </svg>
                            <p class="text-gray-500 mb-4">Nenhum plano de dieta criado</p>
                            <button onclick="generatePlan('dieta')" class="bg-success-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-success-700">
                                Criar Primeiro Plano
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Dicas e Informações -->
        <div class="bg-white rounded-lg shadow mt-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">💡 Dicas para Melhores Resultados</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Treino</h3>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Mantenha consistência nos horários</li>
                            <li>• Respeite os dias de descanso</li>
                            <li>• Aumente a carga progressivamente</li>
                            <li>• Hidrate-se bem durante os exercícios</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 mb-2">Dieta</h3>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Faça as refeições nos horários corretos</li>
                            <li>• Beba pelo menos 2L de água por dia</li>
                            <li>• Evite pular refeições</li>
                            <li>• Ajuste as porções conforme sua fome</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Loading -->
<div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Gerando seu plano...</h3>
            <p class="text-gray-600 text-sm">Nossa IA está criando um plano personalizado para você. Isso pode levar alguns segundos.</p>
        </div>
    </div>
</div>

<script>
async function generatePlan(type) {
    // Mostrar loading
    showLoading(type);
    
    try {
        const response = await fetch('/plans/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': window.csrfToken
            },
            body: new URLSearchParams({
                '_token': window.csrfToken,
                'type': type
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Sucesso - recarregar página para mostrar novo plano
            location.reload();
        } else {
            alert(result.error || 'Erro ao gerar plano');
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao gerar plano. Tente novamente.');
    } finally {
        hideLoading(type);
    }
}

function showLoading(type) {
    // Mostrar modal de loading
    document.getElementById('loadingModal').classList.remove('hidden');
    
    // Atualizar botões
    if (type === 'treino') {
        document.getElementById('workout-btn-text').classList.add('hidden');
        document.getElementById('workout-loading').classList.remove('hidden');
    } else {
        document.getElementById('diet-btn-text').classList.add('hidden');
        document.getElementById('diet-loading').classList.remove('hidden');
    }
}

function hideLoading(type) {
    // Esconder modal de loading
    document.getElementById('loadingModal').classList.add('hidden');
    
    // Restaurar botões
    if (type === 'treino') {
        document.getElementById('workout-btn-text').classList.remove('hidden');
        document.getElementById('workout-loading').classList.add('hidden');
    } else {
        document.getElementById('diet-btn-text').classList.remove('hidden');
        document.getElementById('diet-loading').classList.add('hidden');
    }
}
</script>

