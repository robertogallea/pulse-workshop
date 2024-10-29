@php
    $friendlySize = function(int $mb, int $precision = 0) {
        if ($mb >= 1024 * 1024 * 1024) {
            return round($mb / 1024 / 1024 / 1024, $precision) . 'GB';
        }
        if ($mb >= 1024 * 1024) {
            return round($mb / 1024 / 1024, $precision) . 'MB';
        }
        if ($mb >= 1024) {
            return round($mb / 1024, $precision) . 'KB';
        }
        return round($mb, $precision) . 'B';
    };

    $cols = ! empty($cols) ? $cols : 'full';
    $rows = ! empty($rows) ? $rows : 1;
@endphp
<x-pulse::card :cols="$cols" :rows="$rows" :class="$class" wire:poll.5s="">
    <x-pulse::card-header
        name="Pulse tables size"
        title="Time: {{ number_format($time) }}ms; Run at: {{ $runAt }};"
        details="past {{ $this->periodForHumans() }}"
    >
        <x-slot:icon>
            <x-pulse::icons.circle-stack/>
        </x-slot:icon>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand">
        <div class="grid grid-cols-3 gap-3 text-center">
            @foreach($storageSizeData as $value)
                <div class="flex flex-col justify-center @sm:block">
                        <span class="text-xl uppercase font-bold text-gray-700 dark:text-gray-300 tabular-nums">
                                {{ $friendlySize($value->size) }}
                        </span>
                    <span class="text-xs uppercase font-bold text-gray-500 dark:text-gray-400">
                            {{ $value->name }}
                        </span>
                </div>
            @endforeach
        </div>

    </x-pulse::scroll>
</x-pulse::card>
