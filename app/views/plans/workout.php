<div class="min-h-screen bg-gray-50 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-lg shadow mb-6">
            <div class="px-6 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Plano de Treino</h1>
                        <p class="text-primary-100 mt-1">Seu programa de exercícios personalizado</p>
                    </div>
                    <div class="text-right">
                        <button onclick="generatePlan('treino')" class="bg-white text-primary-600 px-4 py-2 rounded-lg font-medium hover:bg-primary-50 transition duration-150">
                            Gerar Novo Plano
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($weekly_workout): ?>
            <!-- Informações do Plano -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($weekly_workout['plan']['title']) ?></h2>
                    <?php if (isset($weekly_workout['plan']['content']['description'])): ?>
                        <p class="text-gray-600 mt-1"><?= htmlspecialchars($weekly_workout['plan']['content']['description']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-primary-600">
                                <?= isset($weekly_workout['plan']['content']['duration_weeks']) ? $weekly_workout['plan']['content']['duration_weeks'] : '4' ?>
                            </div>
                            <div class="text-sm text-gray-500">Semanas</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-success-600">5</div>
                            <div class="text-sm text-gray-500">Dias/Semana</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-secondary-600">
                                <?php 
                                $totalExercises = 0;
                                if (isset($weekly_workout['weekly_workout'])) {
                                    foreach ($weekly_workout['weekly_workout'] as $day) {
                                        $totalExercises += count($day['exercises']);
                                    }
                                }
                                echo $totalExercises;
                                ?>
                            </div>
                            <div class="text-sm text-gray-500">Exercícios</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">
                                <?= date('d/m/Y', strtotime($weekly_workout['plan']['created_at'])) ?>
                            </div>
                            <div class="text-sm text-gray-500">Criado</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Treino Semanal -->
            <div class="space-y-6">
                <?php foreach ($weekly_workout['weekly_workout'] as $dayNumber => $dayWorkout): ?>
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <span class="bg-primary-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold mr-3">
                                    <?= $dayNumber ?>
                                </span>
                                <?= $dayWorkout['day_name'] ?>
                                <?php if ($dayNumber == date('N')): ?>
                                    <span class="ml-2 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Hoje</span>
                                <?php endif; ?>
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php foreach ($dayWorkout['exercises'] as $index => $exercise): ?>
                                    <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-300 transition duration-150">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-900 mb-2">
                                                    <?= ($index + 1) ?>. <?= htmlspecialchars($exercise['name']) ?>
                                                </h4>
                                                
                                                <div class="space-y-1 text-sm text-gray-600 mb-3">
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                        </svg>
                                                        <span><?= $exercise['sets'] ?> séries × <?= htmlspecialchars($exercise['reps']) ?></span>
                                                    </div>
                                                    
                                                    <?php if ($exercise['weight']): ?>
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 text-secondary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                            </svg>
                                                            <span><?= $exercise['weight'] ?>kg</span>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <span><?= $exercise['rest_time'] ?>s descanso</span>
                                                    </div>
                                                </div>

                                                <?php if ($exercise['instructions']): ?>
                                                    <div class="bg-gray-50 rounded p-3 text-sm text-gray-600">
                                                        <strong>Como executar:</strong><br>
                                                        <?= htmlspecialchars($exercise['instructions']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="ml-4">
                                                <span class="bg-primary-100 text-primary-800 text-xs px-2 py-1 rounded-full">
                                                    <?= htmlspecialchars($exercise['muscle_group']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Dicas e Observações -->
            <div class="bg-white rounded-lg shadow mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">💡 Dicas Importantes</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-3">Antes do Treino</h4>
                            <ul class="text-sm text-gray-600 space-y-2">
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Faça um aquecimento de 5-10 minutos
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Hidrate-se adequadamente
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Use roupas e calçados adequados
                                </li>
                            </ul>
                        </div>
                        
                        <div>
                            <h4 class="font-medium text-gray-900 mb-3">Durante o Treino</h4>
                            <ul class="text-sm text-gray-600 space-y-2">
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Mantenha a forma correta dos exercícios
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Respeite os tempos de descanso
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Pare se sentir dor ou desconforto
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Nenhum Plano Ativo -->
            <div class="bg-white rounded-lg shadow text-center py-12">
                <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Nenhum Plano de Treino Ativo</h2>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    Crie seu primeiro plano de treino personalizado com nossa inteligência artificial. 
                    Será gerado especificamente para seus objetivos e características físicas.
                </p>
                <button onclick="generatePlan('treino')" class="bg-primary-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-primary-700 transition duration-150">
                    Gerar Meu Plano de Treino
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Loading -->
<div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Gerando seu plano de treino...</h3>
            <p class="text-gray-600 text-sm">Nossa IA está analisando seu perfil e criando exercícios personalizados. Isso pode levar alguns segundos.</p>
        </div>
    </div>
</div>

<script>
async function generatePlan(type) {
    // Mostrar loading
    document.getElementById('loadingModal').classList.remove('hidden');
    
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
        // Esconder loading
        document.getElementById('loadingModal').classList.add('hidden');
    }
}
</script>

