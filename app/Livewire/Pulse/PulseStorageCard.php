<?php

namespace App\Livewire\Pulse;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

#[Lazy]
class PulseStorageCard extends Card
{
    public function render()
    {
        [$storageSizeData, $time, $runAt] = $this->remember(
            function() {
                return json_decode($this->values('pulse_storage_size')['tables']->value);
            },
            ttl: 30
        );

        return view('livewire.pulse.pulse-storage-card', [
            'storageSizeData' => $storageSizeData,
            'time' => $time,
            'runAt' => $runAt,
        ]);
    }
}
