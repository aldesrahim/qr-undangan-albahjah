<x-filament-widgets::widget>
    <x-filament::section>
        <form
            wire:submit.prevent="submit"
            x-data="{
                loading: $wire.entangle('loading') ?? false,
            }"
        >
            <div class="mb-4">
                {{ $this->form }}
            </div>

            <x-filament::button type="submit" wire:loading.attr="disabled">
                Submit

                <x-filament::loading-indicator class="h-5 w-5 inline" x-show="loading" />
            </x-filament::button>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>
