<x-filament-panels::page>
    @if ($this->hasInfolist())
        {{ $this->infolist }}
    @else
        {{ $this->form }}
    @endif

    <div class="mt-6">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
