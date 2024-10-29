<x-pulse::card :cols="$cols" :rows="$rows" :class="$class" wire:poll.5s="">
    <x-pulse::card-header
        name="Weather Data"
        title="Time: {{ number_format($time) }}ms; Run at: {{ $runAt }};"
        details="past {{ $this->periodForHumans() }}"
    >
        <x-slot:icon>
            <x-pulse::icons.sun/>
        </x-slot:icon>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand">
        <div class="grid grid-cols-3 gap-3 text-center">
            @foreach($weatherData as $key => $value)
            <div class="flex flex-col justify-center @sm:block">
                        <span class="text-xl uppercase font-bold text-gray-700 dark:text-gray-300 tabular-nums">
                                {{ $value }}
                        </span>
                <span class="text-xs uppercase font-bold text-gray-500 dark:text-gray-400">
                            {{ $key }}
                        </span>
            </div>
            @endforeach
        </div>

    </x-pulse::scroll>
</x-pulse::card>
