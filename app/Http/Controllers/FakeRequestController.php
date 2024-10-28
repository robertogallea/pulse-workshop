<?php

namespace App\Http\Controllers;

use App\Events\CustomEvent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Lottery;

class FakeRequestController extends Controller
{
    public function __invoke()
    {
        Auth::onceUsingId($user ?? rand(1, User::count()));

        Event::dispatch(new CustomEvent(Auth::id(), now()->timestamp));

        // EXCEPTIONS
        Lottery::odds(0.1)
            ->winner(fn() => match (rand(1, 3)) {
                1 => throw new \DivisionByZeroError('Division by zero'),
                2 => throw new \InvalidArgumentException('Invalid argument'),
                3 => throw new \RuntimeException('Runtime exception'),
            })
            ->choose();

        // CACHE INTERACTIONS
        Lottery::odds(1, 3)
            ->winner(fn() => Cache::rememberForever('user:'.Auth::id(), fn() => true))
            ->loser(fn() => Cache::remember('x', rand(10, 60), fn() => true))
            ->choose();

        // JOBS
        $delay = rand(500_000, 2000_000);
        dispatch(fn() => usleep($delay))->onQueue(rand(0, 2) < 2 ? 'default' : 'high');

        // SLOW QUERY
        Lottery::odds(1, 10)
            ->winner(fn() => DB::select('WITH RECURSIVE r(i) AS (VALUES(0) UNION ALL SELECT i FROM r LIMIT 10000000) SELECT i FROM r WHERE i = 1;'))
            ->choose();

        // OUTGOING REQUESTS
        Lottery::odds(1, 10)
            ->winner(fn() => Http::get('https://api.github.com/users/robertogallea/repos')->json())
            ->choose();

        // EVENT
        usleep(rand((int)($delay / 10), $delay));
        return response()->json(['delay' => $delay]);
    }
}
