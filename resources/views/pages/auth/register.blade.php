<x-layouts::auth title="Registar">
    <div class="flex flex-col gap-6">
        <x-auth-header title="Criar conta" description="Preencha os dados abaixo para criar a sua conta" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                label="Nome completo"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Nome completo"
            />

            <!-- Username -->
            <flux:input
                name="username"
                label="Nome de utilizador"
                :value="old('username')"
                type="text"
                required
                autocomplete="username"
                placeholder="seu_utilizador"
            />

            <!-- Email Address (optional) -->
            <flux:input
                name="email"
                label="E-mail (opcional)"
                :value="old('email')"
                type="email"
                autocomplete="email"
                placeholder="email@exemplo.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                label="Senha"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Senha"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                label="Confirmar senha"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Confirmar senha"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    Criar conta
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>Já tem uma conta?</span>
            <flux:link :href="route('login')" wire:navigate>Entrar</flux:link>
        </div>
    </div>
</x-layouts::auth>
