<?php

namespace App\Livewire\Pulse;

use App\Recorders\CustomEventRecorder;
use Illuminate\Support\Facades\Config;
use Laravel\Pulse\Facades\Pulse;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

#[Lazy]
class CustomEventCard extends Card
{
    public function render()
    {
        [[$total, $totalByUsers], $time, $runAt] = $this->remember(
            function() {
                $total = $this->aggregateTotal('custom_event', 'count');

                $totalByUsers = $this->aggregate('custom_event', 'count');

                $users = Pulse::resolveUsers($totalByUsers->pluck('key'));

                $totalByUsers = $totalByUsers->map(function ($row) use ($users) {
                    return (object) [
                        'key' => $row->key,
                        'user' => $users->find($row->key),
                        'count' => $row->count,
                    ];
                });

                return [$total, $totalByUsers];
            });

        $config = Config::get('pulse.recorders.' . CustomEventRecorder::class);

        return view('livewire.pulse.custom-event-card', [
            'total' => $total,
            'totalByUsers' => $totalByUsers,
            'config' => $config,
            'time' => $time,
            'runAt' => $runAt,
        ]);
    }
}
