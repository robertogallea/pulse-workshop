<?php

namespace App\Listeners;

use App\Events\WeatherChanged;

class NotifyAboutWeatherChange
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WeatherChanged $event): void
    {
        dump('Listener triggered! '.$event->measure.' is over threshold. The new value is '.$event->value);
    }
}
