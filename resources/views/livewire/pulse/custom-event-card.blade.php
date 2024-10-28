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
        <div class="flex flex-col justify-center @sm:block">
                        <span class="text-xl uppercase font-bold text-gray-700 dark:text-gray-300 tabular-nums">
                            @if ($sampleRate < 1)
                                <span
                                    title="Sample rate: {{ $sampleRate }}, Raw value: {{ number_format($total) }}">~{{ number_format($total * (1 / $sampleRate)) }}</span>
                            @else
                                {{ number_format($total) }}
                            @endif
                        </span>
            <span class="text-xs uppercase font-bold text-gray-500 dark:text-gray-400">
                            Total events
                        </span>
        </div>
        @if ($totalByUsers->isEmpty())
            <x-pulse::no-results/>
        @else
            <div class="grid grid-cols-1 @lg:grid-cols-2 @3xl:grid-cols-3 @6xl:grid-cols-4 gap-2">


                @foreach ($totalByUsers as $totalForUser)
                    <x-pulse::user-card wire:key="{{ $totalForUser->key }}" :user="$totalForUser->user">
                        <x-slot:stats>
                            @if ($sampleRate < 1)
                                <span
                                    title="Sample rate: {{ $sampleRate }}, Raw value: {{ number_format($totalForUser->count) }}">~{{ number_format($totalForUser->count * (1 / $sampleRate)) }}</span>
                            @else
                                {{ number_format($totalForUser->count) }}
                            @endif
                        </x-slot:stats>
                    </x-pulse::user-card>
                @endforeach
            </div>
        @endif
    </x-pulse::scroll>
</x-pulse::card>
