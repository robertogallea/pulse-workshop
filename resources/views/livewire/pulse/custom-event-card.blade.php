@php
    $sampleRate = $config['sample_rate'];
@endphp
<x-pulse::card :cols="$cols" :rows="$rows" :class="$class" wire:poll.5s="">
    <x-pulse::card-header
            name="Custom event"
            title="Time: {{ number_format($time) }}ms; Run at: {{ $runAt }};"
            details="past {{ $this->periodForHumans() }}"
    >
        <x-slot:icon>
            <x-pulse::icons.circle-stack/>
        </x-slot:icon>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand">
        <div class="grid grid-cols-3 gap-3 text-center">
            <div class="flex flex-col justify-center @sm:block">
                        <span class="text-xl uppercase font-bold text-gray-700 dark:text-gray-300 tabular-nums">
                            @if ($config['sample_rate'] < 1)
                                <span title="Sample rate: {{ $config['sample_rate'] }}, Raw value: {{ number_format($total) }}">~{{ number_format($total * (1 / $config['sample_rate'])) }}</span>
                            @else
                                {{ number_format($total) }}
                            @endif
                        </span>
                <span class="text-xs uppercase font-bold text-gray-500 dark:text-gray-400">
                            Total events
                        </span>
            </div>
        </div>
        <div>
            @if ($totalByUsers->isEmpty())
                <x-pulse::no-results/>
            @else
                <div class="grid grid-cols-1 @lg:grid-cols-2 @3xl:grid-cols-3 @6xl:grid-cols-4 gap-2">

                    @foreach ($totalByUsers as $userTotal)
                        <x-pulse::user-card wire:key="{{ $userTotal->key }}" :user="$userTotal->user">
                            <x-slot:stats>
                                @if ($sampleRate < 1)
                                    <span title="Sample rate: {{ $sampleRate }}, Raw value: {{ number_format($userTotal->count) }}">~{{ number_format($userTotal->count * (1 / $sampleRate)) }}</span>
                                @else
                                    {{ number_format($userTotal->count) }}
                                @endif
                            </x-slot:stats>
                        </x-pulse::user-card>
                    @endforeach
                </div>
            @endif
        </div>
    </x-pulse::scroll>
</x-pulse::card>
