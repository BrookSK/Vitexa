<div class="min-h-screen bg-gray-50 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-success-600 to-success-700 text-white rounded-lg shadow mb-6">
            <div class="px-6 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Plano de Dieta</h1>
                        <p class="text-success-100 mt-1">Seu cardápio nutricional personalizado</p>
                    </div>
                    <div class="text-right">
                        <button onclick="generatePlan('dieta')" class="bg-white text-success-600 px-4 py-2 rounded-lg font-medium hover:bg-success-50 transition duration-150">
                            Gerar Novo Plano
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($daily_meals): ?>
            <!-- Informações do Plano -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($daily_meals['plan']['title']) ?></h2>
                    <?php if (isset($daily_meals['plan']['content']['description'])): ?>
                        <p class="text-gray-600 mt-1"><?= htmlspecialchars($daily_meals['plan']['content']['description']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-success-600">
                                <?= $daily_meals['totals']['calories'] ?>
                            </div>
                            <div class="text-sm text-gray-500">Calorias/dia</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-primary-600"><?= $daily_meals['totals']['proteins'] ?>g</div>
                            <div class="text-sm text-gray-500">Proteínas</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600"><?= $daily_meals['totals']['carbs'] ?>g</div>
                            <div class="text-sm text-gray-500">Carboidratos</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600"><?= $daily_meals['totals']['fats'] ?>g</div>
                            <div class="text-sm text-gray-500">Gorduras</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Refeições do Dia -->
            <div class="space-y-6">
                <?php 
                $mealIcons = [
                    'cafe_manha' => '☀️',
                    'lanche_manha' => '🍎',
                    'almoco' => '🍽️',
                    'lanche_tarde' => '🥨',
                    'jantar' => '🌙',
                    'ceia' => '🥛'
                ];
                
                $mealTimes = [
                    'cafe_manha' => '07:00',
                    'lanche_manha' => '10:00',
                    'almoco' => '12:30',
                    'lanche_tarde' => '15:30',
                    'jantar' => '19:00',
                    'ceia' => '21:30'
                ];
                ?>
                
                <?php foreach ($daily_meals['meals'] as $mealKey => $meal): ?>
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <span class="text-2xl mr-3"><?= $mealIcons[$mealKey] ?? '🍴' ?></span>
                                    <?= htmlspecialchars($meal['name']) ?>
                                    <span class="ml-3 text-sm font-normal text-gray-500">
                                        <?= $mealTimes[$mealKey] ?? '' ?>
                                    </span>
                                </h3>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-success-600"><?= $meal['calories'] ?> kcal</div>
                                    <div class="text-xs text-gray-500">
                                        P: <?= $meal['proteins'] ?>g • C: <?= $meal['carbs'] ?>g • G: <?= $meal['fats'] ?>g
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Ingredientes -->
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                        <svg class="w-5 h-5 text-success-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                        Ingredientes
                                    </h4>
                                    <ul class="space-y-2">
                                        <?php foreach ($meal['ingredients'] as $ingredient): ?>
                                            <li class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                <?= htmlspecialchars($ingredient) ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>

                                <!-- Modo de Preparo -->
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-3 flex items-center">
                                        <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        Modo de Preparo
                                    </h4>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-sm text-gray-600 leading-relaxed">
                                            <?= nl2br(htmlspecialchars($meal['instructions'])) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Informações Nutricionais Detalhadas -->
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <h4 class="font-medium text-gray-900 mb-3">Informações Nutricionais</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="bg-success-50 rounded-lg p-3 text-center">
                                        <div class="text-lg font-bold text-success-600"><?= $meal['calories'] ?></div>
                                        <div class="text-xs text-success-700">Calorias</div>
                                    </div>
                                    <div class="bg-primary-50 rounded-lg p-3 text-center">
                                        <div class="text-lg font-bold text-primary-600"><?= $meal['proteins'] ?>g</div>
                                        <div class="text-xs text-primary-700">Proteínas</div>
                                    </div>
                                    <div class="bg-yellow-50 rounded-lg p-3 text-center">
                                        <div class="text-lg font-bold text-yellow-600"><?= $meal['carbs'] ?>g</div>
                                        <div class="text-xs text-yellow-700">Carboidratos</div>
                                    </div>
                                    <div class="bg-orange-50 rounded-lg p-3 text-center">
                                        <div class="text-lg font-bold text-orange-600"><?= $meal['fats'] ?>g</div>
                                        <div class="text-xs text-orange-700">Gorduras</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Resumo Nutricional Diário -->
            <div class="bg-white rounded-lg shadow mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">📊 Resumo Nutricional Diário</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Distribuição de Macronutrientes -->
                        <div>
                            <h4 class="font-medium text-gray-900 mb-4">Distribuição de Macronutrientes</h4>
                            <?php 
                            $totalMacros = $daily_meals['totals']['proteins'] * 4 + $daily_meals['totals']['carbs'] * 4 + $daily_meals['totals']['fats'] * 9;
                            $proteinPercent = round(($daily_meals['totals']['proteins'] * 4 / $totalMacros) * 100);
                            $carbPercent = round(($daily_meals['totals']['carbs'] * 4 / $totalMacros) * 100);
                            $fatPercent = round(($daily_meals['totals']['fats'] * 9 / $totalMacros) * 100);
                            ?>
                            
                            <div class="space-y-3">
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm font-medium text-primary-700">Proteínas</span>
                                        <span class="text-sm text-gray-600"><?= $proteinPercent ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-primary-600 h-2 rounded-full" style="width: <?= $proteinPercent ?>%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm font-medium text-yellow-700">Carboidratos</span>
                                        <span class="text-sm text-gray-600"><?= $carbPercent ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-600 h-2 rounded-full" style="width: <?= $carbPercent ?>%"></div>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm font-medium text-orange-700">Gorduras</span>
                                        <span class="text-sm text-gray-600"><?= $fatPercent ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-orange-600 h-2 rounded-full" style="width: <?= $fatPercent ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dicas Nutricionais -->
                        <div>
                            <h4 class="font-medium text-gray-900 mb-4">💡 Dicas Importantes</h4>
                            <ul class="text-sm text-gray-600 space-y-2">
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Beba pelo menos 2 litros de água por dia
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Respeite os horários das refeições
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Mastigue bem os alimentos
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Ajuste as porções conforme sua fome
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                </svg>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Nenhum Plano de Dieta Ativo</h2>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    Crie seu primeiro plano de dieta personalizado com nossa inteligência artificial. 
                    Será gerado especificamente para seus objetivos nutricionais e preferências.
                </p>
                <button onclick="generatePlan('dieta')" class="bg-success-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-success-700 transition duration-150">
                    Gerar Meu Plano de Dieta
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Loading -->
<div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-success-600 mx-auto mb-4"></div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Gerando seu plano de dieta...</h3>
            <p class="text-gray-600 text-sm">Nossa IA está analisando seu perfil nutricional e criando um cardápio personalizado. Isso pode levar alguns segundos.</p>
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

