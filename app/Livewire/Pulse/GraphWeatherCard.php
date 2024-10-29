<?php

namespace App\Livewire\Pulse;

use Carbon\CarbonImmutable;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use Livewire\Livewire;

#[Lazy]
class GraphWeatherCard extends Card
{
    public function render()
    {
        [$weather, $time, $runAt] = $this->remember(function () {
            $graphs = $this->graph(['temperature', 'wind'], 'avg');


            $data = $this->values('weather')->first();
            $weatherData = json_decode($data->value, flags: JSON_THROW_ON_ERROR);

            return (object) [
                'name' => 'Weather',
                'temperature_current' => $weatherData->temperature,
                'temperature' => $graphs->get('data')?->get('temperature') ?? collect(),
                'wind_current' => $weatherData->wind,
                'wind' => $graphs->get('data')?->get('wind') ?? collect(),
                'time' => $weatherData->time,
                'updated_at' => $updatedAt = CarbonImmutable::createFromTimestamp($data->timestamp),
                'recently_reported' => $updatedAt->isAfter(now()->subSeconds(30)),
            ];
        });

        if (Livewire::isLivewireRequest()) {
            $this->dispatch('weather-chart-update', weather: $weather);
        }

        return view('livewire.graph-weather-card', [
            'weather' => $weather,
            'time' => $time,
            'runAt' => $runAt,
        ]);
    }
}
