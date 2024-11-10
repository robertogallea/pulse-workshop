<?php

namespace App\Recorders;

use App\Events\CustomEvent;
use Illuminate\Config\Repository;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns\Sampling;

class CustomEventRecorder
{
    use Sampling;

    public string $listen = CustomEvent::class;

    public function __construct(protected Pulse $pulse, protected Repository $config)
    {

    }

    public function record(CustomEvent $event)
    {
        if (! $this->shouldSample()) {
            return;
        }

        $this->pulse->record('custom_event', $event->userId, timestamp: $event->timestamp)
            ->count();
    }
}
