<flux:modal wire:model="showModal" class="w-full max-w-4xl">
    <div class="space-y-6">
        <div class="space-y-1">
            <flux:heading size="lg">Detalhes do registro</flux:heading>
            <flux:subheading>
                Visualize os detalhes deste registro.
            </flux:subheading>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <flux:input name="date" label="Data" wire:model.blur="date" mask="99/99/9999"
                placeholder="dd/mm/aaaa" disabled />

            <flux:input name="location" label="Local" wire:model.blur="location" type="text"
                placeholder="ex.: Escritório, Cliente, Remoto" disabled />

            <flux:input name="start_time" label="Hora início" wire:model.blur="start_time" type="text"
                mask="99:99" placeholder="hh:mm" disabled />

            <flux:input name="end_time" label="Hora fim" wire:model.blur="end_time" type="text"
                mask="99:99" placeholder="hh:mm" disabled />

            <flux:input name="activity_type" label="Tipo de atividade" type="text"
                value="{{ $activity_type }}" disabled />

            <flux:input name="client" label="Cliente" type="text" value="{{ $client }}"
                disabled />
        </div>

        @if ($status === 'draft')
            <flux:badge color="yellow" class="ml-4">
                Rascunho
            </flux:badge>
        @elseif ($status === 'active')
            <flux:badge color="green" class="ml-4">
                Ativo
            </flux:badge>
        @endif

        <flux:textarea name="description" label="Descrição" wire:model.blur="description" rows="5"
            placeholder="Adicione notas sobre o trabalho realizado" disabled />

        <div class="flex justify-end">
            <flux:button type="button" wire:click="closeModal" variant="ghost">
                Fechar
            </flux:button>
        </div>
    </div>
</flux:modal>
