<?php

namespace App\Recorders;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Pulse\Concerns\ConfiguresAfterResolving;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns\LivewireRoutes;
use Symfony\Component\HttpFoundation\Response;

class RequestTrackerRecorder
{
    use ConfiguresAfterResolving;
    use LivewireRoutes;

    public function __construct(protected Pulse $pulse, protected Repository $config)
    {

    }

    public function register(callable $record, Application $app)
    {
        $this->afterResolving(
            $app,
            Kernel::class,
            fn (Kernel $kernel) => $kernel->whenRequestLifecycleIsLongerThan(-1, $record) // @phpstan-ignore method.notFound
        );
    }

    public function record(Carbon $startedAt, Request $request, Response $response)
    {
        [$path, $via] = $this->resolveRoutePath($request);
        logger()->info('New request at ' . $path . ' via ' . $via);
    }
}
