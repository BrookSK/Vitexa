<div class="min-h-screen bg-gray-50 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Meu Progresso</h1>
                <p class="text-gray-600 mt-1">Acompanhe sua evolução e conquiste seus objetivos</p>
            </div>
        </div>

        <!-- Estatísticas Rápidas -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-primary-600"><?= $stats['current_weight'] ?>kg</div>
                <div class="text-sm text-gray-500">Peso Atual</div>
                <?php if ($stats['weight_change'] != 0): ?>
                    <div class="text-xs mt-1 <?= $stats['weight_change'] > 0 ? 'text-red-600' : 'text-green-600' ?>">
                        <?= $stats['weight_change'] > 0 ? '+' : '' ?><?= $stats['weight_change'] ?>kg
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-secondary-600"><?= $stats['bmi'] ?></div>
                <div class="text-sm text-gray-500">IMC</div>
                <div class="text-xs text-gray-400 mt-1"><?= $stats['bmi_category'] ?></div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-success-600"><?= $stats['days_since_joined'] ?></div>
                <div class="text-sm text-gray-500">Dias no App</div>
                <div class="text-xs text-gray-400 mt-1">Jornada</div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-yellow-600"><?= count($progress) ?></div>
                <div class="text-sm text-gray-500">Registros</div>
                <div class="text-xs text-gray-400 mt-1">Últimos 30 dias</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Gráfico de Peso -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Evolução do Peso</h2>
                </div>
                <div class="p-6">
                    <?php if (!empty($progress)): ?>
                        <div class="relative">
                            <canvas id="weightChart" width="400" height="200"></canvas>
                        </div>
                        
                        <!-- Resumo da evolução -->
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <?php 
                            $firstWeight = $progress[0]['weight'];
                            $lastWeight = end($progress)['weight'];
                            $weightDiff = $lastWeight - $firstWeight;
                            ?>
                            <div class="text-sm">
                                <span class="text-gray-600">Variação no período:</span>
                                <span class="font-medium <?= $weightDiff > 0 ? 'text-red-600' : 'text-green-600' ?>">
                                    <?= $weightDiff > 0 ? '+' : '' ?><?= number_format($weightDiff, 1) ?>kg
                                </span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p class="text-gray-500 mb-4">Nenhum dado de progresso ainda</p>
                            <button onclick="openProgressModal()" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-primary-700">
                                Registrar Primeiro Progresso
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Histórico de Registros -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Histórico de Registros</h2>
                    <button onclick="openProgressModal()" class="bg-primary-600 text-white px-3 py-1 rounded text-sm hover:bg-primary-700">
                        + Novo
                    </button>
                </div>
                <div class="p-6">
                    <?php if (!empty($progress)): ?>
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            <?php foreach (array_reverse($progress) as $record): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            <?= $record['weight'] ?>kg
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= date('d/m/Y', strtotime($record['date'])) ?>
                                        </div>
                                        <?php if ($record['notes']): ?>
                                            <div class="text-xs text-gray-400 mt-1">
                                                <?= htmlspecialchars($record['notes']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-right">
                                        <?php if ($record['body_fat']): ?>
                                            <div class="text-sm font-medium text-yellow-600">
                                                <?= $record['body_fat'] ?>% gordura
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($record['muscle_mass']): ?>
                                            <div class="text-sm font-medium text-green-600">
                                                <?= $record['muscle_mass'] ?>kg músculo
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-gray-500">Nenhum registro encontrado</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Análise de Composição Corporal -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Composição Corporal</h2>
                </div>
                <div class="p-6">
                    <?php 
                    $latestRecord = !empty($progress) ? end($progress) : null;
                    ?>
                    
                    <?php if ($latestRecord && ($latestRecord['body_fat'] || $latestRecord['muscle_mass'])): ?>
                        <div class="space-y-4">
                            <?php if ($latestRecord['body_fat']): ?>
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-700">Percentual de Gordura</span>
                                        <span class="text-sm font-bold text-yellow-600"><?= $latestRecord['body_fat'] ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-600 h-2 rounded-full" style="width: <?= min($latestRecord['body_fat'], 50) * 2 ?>%"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <?php 
                                        $bodyFat = $latestRecord['body_fat'];
                                        if ($bodyFat < 10) echo 'Muito baixo';
                                        elseif ($bodyFat < 15) echo 'Baixo';
                                        elseif ($bodyFat < 25) echo 'Normal';
                                        elseif ($bodyFat < 30) echo 'Alto';
                                        else echo 'Muito alto';
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($latestRecord['muscle_mass']): ?>
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-700">Massa Muscular</span>
                                        <span class="text-sm font-bold text-green-600"><?= $latestRecord['muscle_mass'] ?>kg</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: <?= min($latestRecord['muscle_mass'] / $latestRecord['weight'] * 100, 100) ?>%"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <?= round($latestRecord['muscle_mass'] / $latestRecord['weight'] * 100, 1) ?>% do peso corporal
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p class="text-gray-500 mb-4">Dados de composição corporal não disponíveis</p>
                            <p class="text-xs text-gray-400">Registre seu percentual de gordura e massa muscular para ver a análise</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Metas e Objetivos -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Metas e Objetivos</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Objetivo Principal -->
                        <div class="p-4 bg-primary-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-primary-800">
                                        Objetivo Principal
                                    </div>
                                    <div class="text-sm text-primary-600">
                                        <?= $stats['goal'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- IMC Meta -->
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-700">IMC Atual</div>
                                    <div class="text-lg font-bold text-gray-900"><?= $stats['bmi'] ?></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">Meta: 18.5 - 24.9</div>
                                    <div class="text-xs text-gray-400"><?= $stats['bmi_category'] ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Progresso Semanal -->
                        <div class="p-4 bg-success-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-success-700">Esta Semana</div>
                                    <div class="text-xs text-success-600">
                                        <?php 
                                        $weekRecords = array_filter($progress, function($record) {
                                            return strtotime($record['date']) >= strtotime('-7 days');
                                        });
                                        echo count($weekRecords) . ' registros';
                                        ?>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <svg class="w-8 h-8 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Registrar Progresso -->
<div id="progressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Registrar Progresso</h3>
                <button onclick="closeProgressModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="progressForm" onsubmit="submitProgress(event)">
                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Peso (kg) *</label>
                        <input type="number" name="weight" step="0.1" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Percentual de Gordura (%)</label>
                        <input type="number" name="body_fat" step="0.1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Massa Muscular (kg)</label>
                        <input type="number" name="muscle_mass" step="0.1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                        <input type="date" name="date" value="<?= date('Y-m-d') ?>" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                                  placeholder="Como você está se sentindo hoje?"></textarea>
                    </div>
                </div>

                <div class="flex space-x-3 mt-6">
                    <button type="button" onclick="closeProgressModal()"
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Dados do progresso para o gráfico
const progressData = <?= json_encode($progress) ?>;

// Configurar gráfico de peso
if (progressData.length > 0) {
    const ctx = document.getElementById('weightChart').getContext('2d');
    
    const labels = progressData.map(record => {
        const date = new Date(record.date);
        return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
    });
    
    const weights = progressData.map(record => parseFloat(record.weight));
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Peso (kg)',
                data: weights,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function openProgressModal() {
    document.getElementById('progressModal').classList.remove('hidden');
}

function closeProgressModal() {
    document.getElementById('progressModal').classList.add('hidden');
}

async function submitProgress(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        const response = await fetch('/progress', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeProgressModal();
            location.reload(); // Recarregar para mostrar novo progresso
        } else {
            alert(result.error || 'Erro ao salvar progresso');
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao salvar progresso');
    }
}
</script>

