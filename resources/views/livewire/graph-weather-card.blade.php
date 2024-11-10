<section
    wire:poll.5s
    x-data="{
        loading: false,
        init() {
            Livewire.hook('commit', ({ component, succeed }) => {
                if (component.id === $wire.__instance.id) {
                    succeed(() => this.loading = false)
                }
            })
        },
    }"
    class="overflow-x-auto pb-px default:col-span-full default:lg:col-span-{{ $cols }} default:row-span-{{ $rows }} {{ $class }}"
    :class="loading && 'opacity-25 animate-pulse'"
>
    @if ($weather)
        <div class="grid grid-cols-[max-content,minmax(max-content,1fr),max-content,minmax(min-content,2fr),max-content,minmax(min-content,2fr),minmax(max-content,1fr)]">
            <div></div>
            <div></div>
            <div class="text-xs uppercase text-left text-gray-500 dark:text-gray-400 font-bold">Temp.</div>
            <div></div>
            <div class="text-xs uppercase text-left text-gray-500 dark:text-gray-400 font-bold">Wind</div>
            <div></div>
            <div class="text-xs uppercase text-left text-gray-500 dark:text-gray-400 font-bold">Time</div>

            <div wire:key="weather-indicator" class="flex items-center" title="{{ $weather->updated_at->fromNow() }}">
                @if ($weather->recently_reported)
                    <div class="w-5 flex justify-center mr-1">
                        <div class="h-1 w-1 bg-green-500 rounded-full animate-pulse"></div>
                    </div>
                @else
                    <x-pulse::icons.signal-slash class="w-5 h-5 stroke-red-500 mr-1" />
                @endif
            </div>
            <div wire:key="weather-name" class="flex items-center pr-8 xl:pr-12 {{ ! $weather->recently_reported ? 'opacity-25 animate-pulse' : '' }}">
                <x-pulse::icons.sun class="w-6 h-6 mr-2 stroke-gray-500 dark:stroke-gray-400" />
                <span class="text-base font-bold text-gray-600 dark:text-gray-300" title="Time: {{ number_format($time) }}ms; Run at: {{ $runAt }};">{{ $weather->name }}</span>
            </div>
            <div wire:key="weather-temperature" class="flex items-center {{ ! $weather->recently_reported ? 'opacity-25 animate-pulse' : '' }}">
                <div class="text-xl font-bold text-gray-700 dark:text-gray-200 w-14 whitespace-nowrap tabular-nums">
                    {{ $weather->temperature_current }}
                </div>
            </div>
            <div wire:key="weather-temperature-graph" class="flex items-center pr-8 xl:pr-12 {{ ! $weather->recently_reported ? 'opacity-25 animate-pulse' : '' }}">
                <div
                    wire:ignore
                    class="w-full min-w-[5rem] max-w-xs h-9 relative"
                    x-data="temperatureChart({
                            slug: 'weather',
                            labels: @js($weather->temperature->keys()),
                            data: @js($weather->temperature->values()),
                        })"
                >
                    <canvas x-ref="canvas" class="w-full ring-1 ring-gray-900/5 bg-white dark:bg-gray-900 rounded-md shadow-sm"></canvas>
                </div>
            </div>
            <div wire:key="weather-wind" class="flex items-center {{ ! $weather->recently_reported ? 'opacity-25 animate-pulse' : '' }}">
                <div class="w-36 flex-shrink-0 whitespace-nowrap tabular-nums">
                        <span class="text-xl font-bold text-gray-700 dark:text-gray-200">
                            {{ $weather->wind_current }}
                        </span>
                </div>
            </div>
            <div wire:key="weather-wind-graph" class="flex items-center pr-8 xl:pr-12 {{ ! $weather->recently_reported ? 'opacity-25 animate-pulse' : '' }}">
                <div
                    wire:ignore
                    class="w-full min-w-[5rem] max-w-xs h-9 relative"
                    x-data="windChart({
                            slug: 'weather',
                            labels: @js($weather->wind->keys()),
                            data: @js($weather->wind->values()),
                        })"
                >
                    <canvas x-ref="canvas" class="w-full ring-1 ring-gray-900/5 bg-white dark:bg-gray-900 rounded-md shadow-sm"></canvas>
                </div>
            </div>
            <div wire:key="weather-time" class="flex items-center gap-8 {{ ! $weather->recently_reported ? 'opacity-25 animate-pulse' : '' }}">
                <div wire:key="weather-time" class="flex items-center gap-4" title="Time">
                    <div class="whitespace-nowrap tabular-nums">
                        <span class="text-xl font-bold text-gray-700 dark:text-gray-200">{{ $weather->time }}</span>
                    </div>
                </div>
            </div>

        </div>
    @endif
</section>

@script
<script>
    Alpine.data('temperatureChart', (config) => ({
        init() {
            let chart = new Chart(
                this.$refs.canvas,
                {
                    type: 'line',
                    data: {
                        labels: config.labels,
                        datasets: [
                            {
                                label: 'Temperature',
                                borderColor: '#1000ff',
                                borderWidth: 2,
                                borderCapStyle: 'round',
                                data: config.data,
                                pointHitRadius: 10,
                                pointStyle: false,
                                tension: 0.2,
                                spanGaps: false,
                            },
                        ],
                    },
                    options: {
                        maintainAspectRatio: false,
                        layout: {
                            autoPadding: false,
                        },
                        scales: {
                            x: {
                                display: false,
                                grid: {
                                    display: false,
                                },
                            },
                            y: {
                                display: false,
                                min: -100,
                                max: 500,
                                grid: {
                                    display: false,
                                },
                            },
                        },
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                mode: 'index',
                                position: 'nearest',
                                intersect: false,
                                callbacks: {
                                    title: () => '',
                                    label: (context) => `${context.label} - ${context.formattedValue/10} °C`
                                },
                                displayColors: false,
                            },
                        },
                    },
                }
            )

            Livewire.on('weather-chart-update', ({ weather }) => {
                if (chart === undefined) {
                    return
                }

                if (weather === undefined && chart) {
                    chart.destroy()
                    chart = undefined
                    return
                }

                chart.data.labels = Object.keys(weather.temperature)
                chart.data.datasets[0].data = Object.values(weather.temperature)
                chart.update()
            })
        }
    }))

    Alpine.data('windChart', (config) => ({
        init() {
            let chart = new Chart(
                this.$refs.canvas,
                {
                    type: 'line',
                    data: {
                        labels: config.labels,
                        datasets: [
                            {
                                label: 'Wind speed',
                                borderColor: '#00ffff',
                                borderWidth: 2,
                                borderCapStyle: 'round',
                                data: config.data,
                                pointHitRadius: 10,
                                pointStyle: false,
                                tension: 0.2,
                                spanGaps: false,
                            },
                        ],
                    },
                    options: {
                        maintainAspectRatio: false,
                        layout: {
                            autoPadding: false,
                        },
                        scales: {
                            x: {
                                display: false,
                                grid: {
                                    display: false,
                                },
                            },
                            y: {
                                display: false,
                                min: 0,
                                max: 1000,
                                grid: {
                                    display: false,
                                },
                            },
                        },
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                mode: 'index',
                                position: 'nearest',
                                intersect: false,
                                callbacks: {
                                    title: () => '',
                                    label: (context) => `${context.label} - ${context.formattedValue/10} Km/h`
                                },
                                displayColors: false,
                            },
                        },
                    },
                }
            )

            Livewire.on('weather-chart-update', ({ weather }) => {
                if (chart === undefined) {
                    return
                }

                if (weather === undefined && chart) {
                    chart.destroy()
                    chart = undefined
                    return
                }

                chart.data.labels = Object.keys(weather.wind)
                chart.data.datasets[0].data = Object.values(weather.wind)
                chart.update()
            })
        }
    }))

</script>
@endscript
