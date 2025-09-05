<div class="min-h-screen bg-gray-50">
    <!-- Header Stats -->
    <div class="bg-gradient-to-r from-primary-600 to-secondary-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Olá, <?= htmlspecialchars($user['name']) ?>! 👋</h1>
                    <p class="text-primary-100 mt-1">Vamos alcançar seus objetivos hoje</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-primary-100">IMC</div>
                    <div class="text-2xl font-bold"><?= $stats['bmi'] ?></div>
                    <div class="text-xs text-primary-200"><?= $stats['bmi_category'] ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <!-- Peso Atual -->
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-gray-900"><?= $stats['current_weight'] ?>kg</div>
                <div class="text-sm text-gray-500">Peso Atual</div>
                <?php if ($stats['weight_change'] != 0): ?>
                    <div class="text-xs mt-1 <?= $stats['weight_change'] > 0 ? 'text-red-600' : 'text-green-600' ?>">
                        <?= $stats['weight_change'] > 0 ? '+' : '' ?><?= $stats['weight_change'] ?>kg
                    </div>
                <?php endif; ?>
            </div>

            <!-- Planos Ativos -->
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-primary-600"><?= $active_plans['active_plans'] ?></div>
                <div class="text-sm text-gray-500">Planos Ativos</div>
                <div class="text-xs text-gray-400 mt-1">
                    <?= $active_plans['workout_plans'] ?> treino, <?= $active_plans['diet_plans'] ?> dieta
                </div>
            </div>

            <!-- Dias no App -->
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-success-600"><?= $stats['days_since_joined'] ?></div>
                <div class="text-sm text-gray-500">Dias no App</div>
                <div class="text-xs text-gray-400 mt-1">Desde o cadastro</div>
            </div>

            <!-- Objetivo -->
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-lg font-semibold text-secondary-600 truncate"><?= $stats['goal'] ?></div>
                <div class="text-sm text-gray-500">Objetivo</div>
                <div class="text-xs text-gray-400 mt-1">Meta definida</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Treino de Hoje -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Treino de Hoje
                        </h2>
                        <a href="<?= APP_URL ?>/plans/workout" class="text-primary-600 text-sm hover:text-primary-800">Ver todos</a>
                    </div>

                    <?php if ($weekly_workout): ?>
                        <?php 
                        $today = date('N'); // 1=Monday, 7=Sunday
                        $todayWorkout = $weekly_workout['weekly_workout'][$today] ?? null;
                        ?>
                        
                        <?php if ($todayWorkout): ?>
                            <div class="space-y-3">
                                <?php foreach (array_slice($todayWorkout['exercises'], 0, 3) as $exercise): ?>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <div class="font-medium text-gray-900"><?= htmlspecialchars($exercise['name']) ?></div>
                                            <div class="text-sm text-gray-500">
                                                <?= $exercise['sets'] ?> séries × <?= htmlspecialchars($exercise['reps']) ?>
                                                <?php if ($exercise['weight']): ?>
                                                    - <?= $exercise['weight'] ?>kg
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-400 bg-white px-2 py-1 rounded">
                                            <?= htmlspecialchars($exercise['muscle_group']) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                
                                <?php if (count($todayWorkout['exercises']) > 3): ?>
                                    <div class="text-center">
                                        <a href="<?= APP_URL ?>/plans/workout" class="text-primary-600 text-sm hover:text-primary-800">
                                            +<?= count($todayWorkout['exercises']) - 3 ?> exercícios restantes
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <p class="text-gray-500 mb-4">Dia de descanso! 😴</p>
                                <a href="<?= APP_URL ?>/plans/workout" class="text-primary-600 hover:text-primary-800 text-sm">
                                    Gerar novo plano de treino
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <p class="text-gray-500 mb-4">Nenhum plano de treino ativo</p>
                            <a href="<?= APP_URL ?>/plans/workout" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-primary-700 transition duration-150">
                                Gerar Plano de Treino
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Dieta de Hoje -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-success-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                            </svg>
                            Dieta de Hoje
                        </h2>
                        <a href="<?= APP_URL ?>/plans/diet" class="text-success-600 text-sm hover:text-success-800">Ver todas</a>
                    </div>

                    <?php if ($daily_meals): ?>
                        <div class="space-y-3 mb-4">
                            <?php foreach (array_slice($daily_meals['meals'], 0, 4) as $meal): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900"><?= htmlspecialchars($meal['name']) ?></div>
                                        <div class="text-sm text-gray-500">
                                            <?php if ($meal['calories']): ?>
                                                <?= $meal['calories'] ?> kcal
                                            <?php endif; ?>
                                            <?php if ($meal['proteins']): ?>
                                                • <?= $meal['proteins'] ?>g proteína
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-400 bg-white px-2 py-1 rounded">
                                        <?= ucfirst(str_replace('_', ' ', $meal['type'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Resumo Nutricional -->
                        <div class="bg-success-50 rounded-lg p-4">
                            <div class="text-sm font-medium text-success-800 mb-2">Resumo do Dia</div>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-success-600">Calorias:</span>
                                    <span class="font-medium"><?= $daily_meals['totals']['calories'] ?></span>
                                </div>
                                <div>
                                    <span class="text-success-600">Proteínas:</span>
                                    <span class="font-medium"><?= $daily_meals['totals']['proteins'] ?>g</span>
                                </div>
                                <div>
                                    <span class="text-success-600">Carboidratos:</span>
                                    <span class="font-medium"><?= $daily_meals['totals']['carbs'] ?>g</span>
                                </div>
                                <div>
                                    <span class="text-success-600">Gorduras:</span>
                                    <span class="font-medium"><?= $daily_meals['totals']['fats'] ?>g</span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <p class="text-gray-500 mb-4">Nenhum plano de dieta ativo</p>
                            <a href="<?= APP_URL ?>/plans/diet" class="bg-success-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-success-700 transition duration-150">
                                Gerar Plano de Dieta
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Progresso Recente -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-secondary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Progresso Recente
                        </h2>
                        <a href="<?= APP_URL ?>/progress" class="text-secondary-600 text-sm hover:text-secondary-800">Ver histórico</a>
                    </div>

                    <?php if (!empty($recent_progress)): ?>
                        <div class="space-y-3">
                            <?php foreach (array_slice($recent_progress, -3) as $progress): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900"><?= $progress['weight'] ?>kg</div>
                                        <div class="text-sm text-gray-500">
                                            <?= date('d/m/Y', strtotime($progress['date'])) ?>
                                        </div>
                                    </div>
                                    <?php if ($progress['body_fat']): ?>
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-700"><?= $progress['body_fat'] ?>%</div>
                                            <div class="text-xs text-gray-500">Gordura</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Botão para adicionar progresso -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <button onclick="openProgressModal()" class="w-full bg-secondary-600 text-white py-2 px-4 rounded-lg text-sm hover:bg-secondary-700 transition duration-150">
                                Registrar Progresso Hoje
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p class="text-gray-500 mb-4">Nenhum progresso registrado</p>
                            <button onclick="openProgressModal()" class="bg-secondary-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-secondary-700 transition duration-150">
                                Registrar Primeiro Progresso
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Lembretes -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Lembretes Ativos
                        </h2>
                        <?php if (!empty($reminders)): ?>
                            <button onclick="openReminderModal()" class="text-reminders-600 text-sm hover:text-secondary-800">Criar novo lembrete</button>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($reminders)): ?>
                        <div class="space-y-3">
                            <?php foreach (array_slice($reminders, 0, 3) as $reminder): ?>
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-900"><?= htmlspecialchars($reminder['title']) ?></div>
                                        <div class="text-sm text-gray-500">
                                            <?= date('H:i', strtotime($reminder['time'])) ?>
                                            <?php 
                                            $days = json_decode($reminder['days_of_week'], true);
                                            $dayNames = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
                                            $activeDays = array_map(function($day) use ($dayNames) {
                                                return $dayNames[$day];
                                            }, $days);
                                            echo ' • ' . implode(', ', $activeDays);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-400 bg-white px-2 py-1 rounded capitalize">
                                        <?= htmlspecialchars($reminder['type']) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-500 mb-4">Nenhum lembrete configurado</p>
                            <button onclick="openReminderModal()" class="bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-700 transition duration-150">
                                Criar Lembrete
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Registrar Progresso -->
<div id="progressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50" x-data="{ show: false }" x-show="show" x-transition>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Peso (kg)</label>
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

<script>
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
                            $days = ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"];
                            for ($i = 0; $i < 7; $i++): 
                            ?>
                                <label class="flex flex-col items-center">
                                    <input type="checkbox" name="days_of_week[]" value="<?= $i ?>" 
                                           class="form-checkbox h-5 w-5 text-primary-600 rounded focus:ring-primary-500">
                                    <span class="mt-1 text-xs"><?= $days[$i] ?></span>
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