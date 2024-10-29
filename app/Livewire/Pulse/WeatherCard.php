<?php

namespace App\Livewire\Pulse;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

#[Lazy]
class WeatherCard extends Card
{
    public function render()
    {
        [$weatherData, $time, $runAt] = $this->remember(
            function() {
                return json_decode($this->values('weather')['data']->value);
            },
            ttl: 30
        );

        return view('livewire.pulse.weather-card', [
            'weatherData' => $weatherData,
            'time' => $time,
            'runAt' => $runAt,
        ]);
    }
}
