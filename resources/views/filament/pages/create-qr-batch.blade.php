<x-filament-panels::page>
    <form wire:submit="generate" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end gap-x-4 py-4">
            {{ $this->getFormActions() }}
        </div>
    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>