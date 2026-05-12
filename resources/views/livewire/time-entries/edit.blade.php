<flux:modal wire:model="showModal" class="w-full max-w-4xl">
    <div class="space-y-6">
        <div class="space-y-1">
            <flux:heading size="lg">Editar registro</flux:heading>
            <flux:subheading>
                Atualize os detalhes do registro. Salve antes de fechar.
            </flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-6">
            @if ($errorMessage)
                <flux:callout variant="danger" icon="exclamation-triangle">
                    {{ $errorMessage }}
                </flux:callout>
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input name="date" label="Data" wire:model.blur="date" mask="99/99/9999"
                    placeholder="dd/mm/aaaa" required />

                <flux:input name="location" label="Local" wire:model.blur="location" type="text"
                    placeholder="ex.: Escritório, Cliente, Remoto" required />

                <flux:input name="start_time" label="Hora início" wire:model.blur="start_time" type="text"
                    mask="99:99" placeholder="hh:mm" required />

                <flux:input name="end_time" label="Hora fim" wire:model.blur="end_time" type="text"
                    mask="99:99" placeholder="hh:mm" required />

                <flux:select name="activity_type_id" wire:model="activity_type_id" label="Tipo de atividade" required>
                    <flux:select.option value="">Selecione o tipo de atividade</flux:select.option>
                    @foreach ($activityTypes as $activityType)
                        <flux:select.option :value="$activityType->id">{{ $activityType->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select name="client_id" wire:model="client_id" label="Cliente" required>
                    <flux:select.option value="">Selecione o cliente</flux:select.option>
                    @foreach ($clients as $client)
                        <flux:select.option :value="$client->id">{{ $client->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select name="status" wire:model="status" label="Status" required>
                    <flux:select.option value="draft">Rascunho</flux:select.option>
                    <flux:select.option value="active">Ativo</flux:select.option>
                </flux:select>
            </div>

            <flux:textarea name="description" label="Descrição" wire:model.blur="description"
                rows="5" placeholder="Adicione notas sobre o trabalho realizado" required />

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <flux:button type="button" wire:click="closeModal" variant="ghost">
                    Cancelar
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                    Salvar
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>
