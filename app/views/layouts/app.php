<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $csrf_token ?>">
    <link rel="icon" href="<?= APP_URL ?>/assets/images/Vitexa-Icone.png" type="image/png">
    <title><?= $title ?? APP_NAME ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#fdf4ff',
                            100: '#fae8ff',
                            200: '#f5d0fe',
                            300: '#f0abfc',
                            400: '#e879f9',
                            500: '#d946ef',
                            600: '#c026d3',
                            700: '#a21caf',
                            800: '#86198f',
                            900: '#701a75',
                        },
                        success: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom CSS -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .mobile-menu {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 50;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Flash Messages -->
    <?php if (!empty($flash_messages)): ?>
        <div id="flash-messages" class="fixed top-4 right-4 z-50 space-y-2">
            <?php foreach ($flash_messages as $type => $messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= $type ?> px-4 py-3 rounded-lg shadow-lg max-w-sm
                        <?= $type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : '' ?>
                        <?= $type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : '' ?>
                        <?= $type === 'warning' ? 'bg-yellow-100 border border-yellow-400 text-yellow-700' : '' ?>
                        <?= $type === 'info' ? 'bg-blue-100 border border-blue-400 text-blue-700' : '' ?>"
                        x-data="{ show: true }" 
                        x-show="show" 
                        x-init="setTimeout(() => show = false, 5000)"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-full"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        x-transition:leave="transition ease-in duration-300"
                        x-transition:leave-start="opacity-100 transform translate-x-0"
                        x-transition:leave-end="opacity-0 transform translate-x-full">
                        <div class="flex items-center justify-between">
                            <span><?= htmlspecialchars($message) ?></span>
                            <button @click="show = false" class="ml-2 text-lg font-bold">&times;</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <?php if ($current_user): ?>
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <!-- <h1 class="text-xl font-bold text-gray-900"><?= APP_NAME ?></h1> -->
                        <a href="<?= APP_URL ?>/dashboard"><img src="<?= APP_URL ?>/assets/images/VitexaLogo-SF-Ajustado.png" alt="<?= APP_NAME ?> Logo" class="h-12"></a>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600"><a href="<?= APP_URL ?>/profile" class="text-sm text-gray-600 hover:underline">Olá, <?= htmlspecialchars($current_user['name']) ?></a></span>
                        <form method="POST" action="<?= APP_URL ?>/logout" class="inline">
                            <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="<?= $current_user ? 'pb-20' : '' ?>">
        <?= $content ?>
    </main>

    <!-- Mobile Navigation -->
    <?php if ($current_user): ?>
        <nav class="mobile-menu bg-white border-t border-gray-200 shadow-lg">
            <div class="grid grid-cols-5 h-16">
                <a href="<?= APP_URL ?>/dashboard" class="flex flex-col items-center justify-center text-xs text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Home
                </a>
                
                <a href="<?= APP_URL ?>/plans/workout" class="flex flex-col items-center justify-center text-xs text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Treino
                </a>
                
                <a href="<?= APP_URL ?>/plans/diet" class="flex flex-col items-center justify-center text-xs text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                    </svg>
                    Dieta
                </a>
                
                <a href="<?= APP_URL ?>/progress" class="flex flex-col items-center justify-center text-xs text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Progresso
                </a>
                
                <a href="<?= APP_URL ?>/chat" class="flex flex-col items-center justify-center text-xs text-gray-600 hover:text-primary-600 hover:bg-gray-50">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Chat
                </a>
            </div>
        </nav>
    <?php endif; ?>

    <!-- JavaScript -->
    <script>
        // CSRF Token para requisições AJAX
        window.csrfToken = '<?= $csrf_token ?>';
        
        // Configurar headers padrão para fetch
        const originalFetch = window.fetch;
        window.fetch = function(url, options = {}) {
            if (!options.headers) {
                options.headers = {};
            }
            
            if (options.method && options.method.toUpperCase() !== 'GET') {
                options.headers['X-CSRF-Token'] = window.csrfToken;
            }
            
            return originalFetch(url, options);
        };

        // Reminder Modal Script
        function openReminderModal() {
            document.getElementById("reminderModal").classList.remove("hidden");
        }

        function closeReminderModal() {
            document.getElementById("reminderModal").classList.add("hidden");
        }

        async function saveReminder(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            try {
                const response = await fetch("/reminders/save", {
                    method: "POST",
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    closeReminderModal();
                    location.reload();
                } else {
                    alert(result.error || "Erro ao salvar lembrete");
                }
            } catch (error) {
                console.error("Erro:", error);
                alert("Erro ao salvar lembrete");
            }
        }

        async function editReminder(id) {
            try {
                const response = await fetch(`/reminders/get/${id}`);
                const result = await response.json();

                if (result.success) {
                    const reminder = result.reminder;
                    document.getElementById("modalTitle").textContent = "Editar Lembrete";
                    document.getElementById("reminderId").value = reminder.id;
                    document.getElementById("reminderTitle").value = reminder.title;
                    document.getElementById("reminderMessage").value = reminder.message;
                    document.getElementById("reminderType").value = reminder.type;
                    document.getElementById("reminderTime").value = reminder.time.substring(0, 5);

                    const daysOfWeek = JSON.parse(reminder.days_of_week);
                    document.querySelectorAll("input[name=\'days_of_week[]\']").forEach(checkbox => {
                        checkbox.checked = daysOfWeek.includes(parseInt(checkbox.value));
                    });

                    openReminderModal();
                } else {
                    alert(result.error || "Erro ao carregar lembrete para edição");
                }
            } catch (error) {
                console.error("Erro:", error);
                alert("Erro ao carregar lembrete para edição");
            }
        }

        async function deleteReminder(id) {
            if (!confirm("Tem certeza que deseja deletar este lembrete?")) {
                return;
            }

            try {
                const response = await fetch("/reminders/delete", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: new URLSearchParams({
                        "_token": window.csrfToken,
                        "reminder_id": id
                    })
                });

                const result = await response.json();

                if (result.success) {
                    const reminderElement = document.querySelector(`[data-id="${id}"]`);
                    if (reminderElement) {
                        reminderElement.remove();
                    }
                } else {
                    alert(result.error || "Erro ao deletar lembrete");
                }
            } catch (error) {
                console.error("Erro:", error);
                alert("Erro ao deletar lembrete");
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const toggles = document.querySelectorAll(".reminder-toggle");
            toggles.forEach(toggle => {
                toggle.addEventListener("change", async function() {
                    const id = this.dataset.id;
                    try {
                        const response = await fetch("/reminders/toggle", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded",
                            },
                            body: new URLSearchParams({
                                "_token": window.csrfToken,
                                "reminder_id": id
                            })
                        });
                        const result = await response.json();
                        if (!result.success) {
                            this.checked = !this.checked;
                            alert(result.error || "Erro ao alterar status");
                        }
                    } catch (error) {
                        console.error("Erro:", error);
                        this.checked = !this.checked;
                        alert("Erro ao alterar status");
                    }
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const flashMessages = document.querySelectorAll(".alert");
            flashMessages.forEach(message => {
                setTimeout(() => {
                    message.style.display = "none";
                }, 5000);
            });
            // Fechar mensagens ao clicar no botão
            const closeButtons = document.querySelectorAll(".close-flash-message");
            closeButtons.forEach(button => {
                button.addEventListener("click", function() {
                    this.closest(".alert").style.display = "none";
                });
            });
        });
    </script>
</body>
</html>

