<?php

namespace App\Recorders;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\DB;
use Laravel\Pulse\Events\SharedBeat;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns\Throttling;

class PulseStorageRecorder
{
    use Throttling;

    public string $listen = SharedBeat::class;

    public function __construct(protected Pulse $pulse, protected Repository $config)
    {

    }

    public function record(SharedBeat $event)
    {
        $this->throttle(15, $event, function ($event) {
            $sizes = DB::select("SELECT SUM(pgsize) as size, name FROM 'dbstat' WHERE name in('pulse_entries','pulse_values','pulse_aggregates') group by name;");
            $this->pulse->set('pulse_storage_size', 'tables', json_encode($sizes, JSON_THROW_ON_ERROR));
        });
    }
}
