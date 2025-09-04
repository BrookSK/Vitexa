<div class="min-h-screen flex items-center justify-center gradient-bg py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-white">
                Redefinir sua senha
            </h2>
            <p class="mt-2 text-sm text-gray-200">
                Informe sua nova senha.
            </p>
        </div>
        
        <div class="glass-effect rounded-lg p-8">
            <form class="space-y-6" method="POST" action="<?= APP_URL ?>/reset-password">
                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div>
                    <label for="password" class="block text-sm font-medium text-white">
                        Nova Senha
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                               class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                               placeholder="Digite sua nova senha">
                    </div>
                    <?php if ($this->hasError("password")): ?>
                        <p class="mt-1 text-sm text-red-300">
                            <?= htmlspecialchars($this->error("password")[0]) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-white">
                        Confirmar Nova Senha
                    </label>
                    <div class="mt-1">
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                               class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                               placeholder="Confirme sua nova senha">
                    </div>
                    <?php if ($this->hasError("password_confirmation")): ?>
                        <p class="mt-1 text-sm text-red-300">
                            <?= htmlspecialchars($this->error("password_confirmation")[0]) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150 ease-in-out">
                        Redefinir Senha
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>