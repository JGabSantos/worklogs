@if ($status === 'active')
    <flux:badge size="sm" color="emerald">{{ __('Active') }}</flux:badge>
@else
    <flux:badge size="sm" color="zinc">{{ __('Draft') }}</flux:badge>
@endif
