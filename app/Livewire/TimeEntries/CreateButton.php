<?php

namespace App\Livewire\TimeEntries;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateButton extends Component
{
    public function openModal(): void
    {
        if (Auth::user()?->hasPermission('time-entries.create.own')) {
            $this->dispatch('open-create-time-entry-modal');
        }
    }

    public function render()
    {
        return view('livewire.time-entries.create-button');
    }
}
