<?php

namespace App\Recorders;

use App\Events\WeatherChanged;
use Carbon\Carbon;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Config\Repository as ConfigRespository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Laravel\Pulse\Events\IsolatedBeat;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns\Throttling;

class WeatherRecorder
{
    use Throttling;

    public string $listen = IsolatedBeat::class;

    public function __construct(
        protected Pulse $pulse,
        protected ConfigRespository $config,
        protected CacheRepository $cache,
    )
    {

    }

    public function record(IsolatedBeat $event): void
    {
        $this->throttle(30, $event, function ($event) {
            $coordinates = $this->config->get('pulse.recorders.'.self::class.'.coordinates');

            $data = Http::get("https://api.open-meteo.com/v1/forecast?latitude={$coordinates['lat']}&longitude={$coordinates['lng']}&current=temperature_2m,wind_speed_10m")
                ->json();


            $weather = [
                'temperature' => $data['current']['temperature_2m'].' '.$data['current_units']['temperature_2m'],
                'wind' => $data['current']['wind_speed_10m'].' '.$data['current_units']['wind_speed_10m'],
                'time' => Carbon::parse($data['current']['time'])->format('d/m/Y H:i'),
            ];

            $this->pulse->set('weather', 'data', json_encode($weather, flags: JSON_THROW_ON_ERROR));

            $this->checkActuators($weather, ['temperature', 'wind']);

            $this->pulse->record('temperature', 'data', (int) ((float) $weather['temperature'] * 10))
                ->avg()->onlyBuckets();
            $this->pulse->record('wind', 'data', (int) ((float) $weather['wind'] * 10))
                ->avg()->onlyBuckets();
        });
    }

    private function checkActuators(array $weathers, array $measures): void
    {
        foreach ($measures as $measure) {
            $this->needsAction($weathers[$measure], $measure);
        }
    }

    private function needsAction($value, string $measure): void
    {
        $currentValue = (float) $value;

        if ($this->measureHasChangedEnough($currentValue, $measure)) {
            Event::dispatch(new WeatherChanged($currentValue, $measure));
        }
    }

    private function measureHasChangedEnough(float $currentValue, string $measure): bool
    {

        $cacheKey = self::class.':'.$measure.'-actuated';

        $thresholds = $this->config->get('pulse.recorders.'.self::class.'.thresholds.'.$measure);
        $alreadyNotified = cache()->get($cacheKey) ?? false;

        dump("Current value for {$measure} is {$currentValue}");

        if (($currentValue >= $thresholds['upper']) && (! $alreadyNotified)) {
            cache()->set($cacheKey, true);
            return true;
        } elseif ($currentValue <= $thresholds['lower']) {
            dump('Deleting cache key');
            cache()->forget($cacheKey);
        }

        return false;
    }
}
