<div class="min-h-screen flex items-center justify-center gradient-bg py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-white">
                Crie sua conta
            </h2>
            <p class="mt-2 text-sm text-gray-200">
                Ou
                <a href="<?= APP_URL ?>/login" class="font-medium text-yellow-300 hover:text-yellow-200">
                    entre na sua conta existente
                </a>
            </p>
        </div>
        
        <div class="glass-effect rounded-lg p-8">
            <form class="space-y-6" method="POST" action="<?= APP_URL ?>/register">
                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                
                <div>
                    <label for="name" class="block text-sm font-medium text-white">
                        Nome completo
                    </label>
                    <div class="mt-1">
                        <input id="name" name="name" type="text" autocomplete="name" required
                               value="<?= htmlspecialchars($this->old('name')) ?>"
                               class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                               placeholder="Digite seu nome completo">
                    </div>
                    <?php if ($this->hasError('name')): ?>
                        <p class="mt-1 text-sm text-red-300">
                            <?= htmlspecialchars($this->error('name')[0]) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-white">
                        Email
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="<?= htmlspecialchars($this->old('email')) ?>"
                               class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                               placeholder="Digite seu email">
                    </div>
                    <?php if ($this->hasError('email')): ?>
                        <p class="mt-1 text-sm text-red-300">
                            <?= htmlspecialchars($this->error('email')[0]) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="age" class="block text-sm font-medium text-white">
                            Idade
                        </label>
                        <div class="mt-1">
                            <input id="age" name="age" type="number" min="16" max="100" required
                                   value="<?= htmlspecialchars($this->old('age')) ?>"
                                   class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                                   placeholder="25">
                        </div>
                        <?php if ($this->hasError('age')): ?>
                            <p class="mt-1 text-sm text-red-300">
                                <?= htmlspecialchars($this->error('age')[0]) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="weight" class="block text-sm font-medium text-white">
                            Peso (kg)
                        </label>
                        <div class="mt-1">
                            <input id="weight" name="weight" type="number" min="30" max="300" step="0.1" required
                                   value="<?= htmlspecialchars($this->old('weight')) ?>"
                                   class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                                   placeholder="70">
                        </div>
                        <?php if ($this->hasError('weight')): ?>
                            <p class="mt-1 text-sm text-red-300">
                                <?= htmlspecialchars($this->error('weight')[0]) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="height" class="block text-sm font-medium text-white">
                            Altura (cm)
                        </label>
                        <div class="mt-1">
                            <input id="height" name="height" type="number" min="100" max="250" required
                                   value="<?= htmlspecialchars($this->old('height')) ?>"
                                   class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                                   placeholder="175">
                        </div>
                        <?php if ($this->hasError('height')): ?>
                            <p class="mt-1 text-sm text-red-300">
                                <?= htmlspecialchars($this->error('height')[0]) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <label for="goal" class="block text-sm font-medium text-white">
                        Objetivo
                    </label>
                    <div class="mt-1">
                        <select id="goal" name="goal" required
                                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm">
                            <option value="">Selecione seu objetivo</option>
                            <option value="perder_peso" <?= $this->old('goal') === 'perder_peso' ? 'selected' : '' ?>>Perder peso</option>
                            <option value="ganhar_massa" <?= $this->old('goal') === 'ganhar_massa' ? 'selected' : '' ?>>Ganhar massa muscular</option>
                            <option value="manter_forma" <?= $this->old('goal') === 'manter_forma' ? 'selected' : '' ?>>Manter a forma</option>
                            <option value="melhorar_condicionamento" <?= $this->old('goal') === 'melhorar_condicionamento' ? 'selected' : '' ?>>Melhorar condicionamento</option>
                        </select>
                    </div>
                    <?php if ($this->hasError('goal')): ?>
                        <p class="mt-1 text-sm text-red-300">
                            <?= htmlspecialchars($this->error('goal')[0]) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-white">
                        Senha
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                               class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                               placeholder="Digite sua senha">
                    </div>
                    <?php if ($this->hasError('password')): ?>
                        <p class="mt-1 text-sm text-red-300">
                            <?= htmlspecialchars($this->error('password')[0]) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-white">
                        Confirmar senha
                    </label>
                    <div class="mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                               class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                               placeholder="Confirme sua senha">
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150 ease-in-out">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-primary-500 group-hover:text-primary-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                            </svg>
                        </span>
                        Criar conta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

