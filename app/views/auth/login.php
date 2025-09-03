<div class="min-h-screen flex items-center justify-center gradient-bg py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-white">
                Entre na sua conta
            </h2>
            <p class="mt-2 text-sm text-gray-200">
                Ou
                <a href="<?= APP_URL ?>/register" class="font-medium text-yellow-300 hover:text-yellow-200">
                    crie uma conta gratuita
                </a>
            </p>
        </div>
        
        <div class="glass-effect rounded-lg p-8">
            <form class="space-y-6" method="POST" action="<?= APP_URL ?>/login">
                <input type="hidden" name="_token" value="<?= $csrf_token ?>">
                
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

                <div>
                    <label for="password" class="block text-sm font-medium text-white">
                        Senha
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-primary-500 focus:border-primary-500 focus:z-10 sm:text-sm"
                               placeholder="Digite sua senha">
                    </div>
                    <?php if ($this->hasError('password')): ?>
                        <p class="mt-1 text-sm text-red-300">
                            <?= htmlspecialchars($this->error('password')[0]) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox"
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-white">
                            Lembrar de mim
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-yellow-300 hover:text-yellow-200">
                            Esqueceu a senha?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-150 ease-in-out">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-primary-500 group-hover:text-primary-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        Entrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

