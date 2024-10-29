<?php

namespace App\Recorders;

use Carbon\Carbon;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Http;
use Laravel\Pulse\Events\IsolatedBeat;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns\Throttling;

class WeatherRecorder
{
    use Throttling;

    public string $listen = IsolatedBeat::class;

    public function __construct(protected Pulse $pulse, protected Repository $config)
    {

    }

    public function record(IsolatedBeat $event): void
    {
        $this->throttle(60, $event, function ($event) {
            $coordinates = $this->config->get('pulse.recorders.'.self::class.'.coordinates');

            $data = Http::get("https://api.open-meteo.com/v1/forecast?latitude={$coordinates['lat']}&longitude={$coordinates['lng']}&current=temperature_2m,wind_speed_10m")
                ->json();


            $weather = [
                'temperature' => $data['current']['temperature_2m'].' '.$data['current_units']['temperature_2m'],
                'wind' => $data['current']['wind_speed_10m'].' '.$data['current_units']['wind_speed_10m'],
                'time' => Carbon::parse($data['current']['time'])->format('d/m/Y H:i'),
            ];

            $this->pulse->set('weather', 'data', json_encode($weather, flags: JSON_THROW_ON_ERROR));
        });
    }
}
