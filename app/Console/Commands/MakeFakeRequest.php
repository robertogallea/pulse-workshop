<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class MakeFakeRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:make-fake-request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make requests to fake endpoint';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        while (true) {
            try {
                $this->info('Make fake request to ' . route('fake-request'));
                $response = Http::asJson()->get(route('fake-request'));
                $this->info($response->body());
                $delay = $response->json('delay');
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }
            usleep($delay ?? 0);
        }
    }
}
