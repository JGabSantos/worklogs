<flux:modal wire:model="showModal" class="w-full max-w-md">
    <div class="space-y-6">
        <div class="space-y-1">
            <flux:heading size="lg">Excluir registro</flux:heading>
            <flux:subheading>
                Tem certeza de que deseja excluir este registro? Esta ação não pode ser desfeita.
            </flux:subheading>
        </div>

        <div class="flex justify-end gap-3">
            <flux:button type="button" wire:click="closeModal" variant="ghost">
                Cancelar
            </flux:button>

            <flux:button type="button" wire:click="delete" variant="danger" wire:loading.attr="disabled"
                wire:target="delete">
                Excluir
            </flux:button>
        </div>
    </div>
</flux:modal>
