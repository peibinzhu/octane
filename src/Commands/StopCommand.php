<?php

namespace Laravel\Octane\Commands;

use Laravel\Octane\RoadRunner\ServerProcessInspector as RoadRunnerServerProcessInspector;
use Laravel\Octane\RoadRunner\ServerStateFile as RoadRunnerServerStateFile;
use Laravel\Octane\Swoole\ServerProcessInspector as SwooleServerProcessInspector;
use Laravel\Octane\Swoole\ServerStateFile as SwooleServerStateFile;

class StopCommand extends Command
{
    /**
     * The command's signature.
     *
     * @var string
     */
    public $signature = 'octane:stop {--server= : The server that is running the application}';

    /**
     * The command's description.
     *
     * @var string
     */
    public $description = 'Stop the Octane server';

    /**
     * Handle the command.
     *
     * @return int
     */
    public function handle()
    {
        $server = $this->option('server') ?: config('octane.server');

        return $server == 'swoole'
            ? $this->stopSwooleServer()
            : $this->stopRoadRunnerServer();
    }

    /**
     * Stop the Swoole server for Octane.
     *
     * @return int
     */
    protected function stopSwooleServer()
    {
        $inspector = app(SwooleServerProcessInspector::class);

        if (! $inspector->serverIsRunning()) {
            $this->error('Swoole server is not running.');

            return 1;
        }

        $this->info('Stopping server...');

        $inspector->stopServer();

        app(SwooleServerStateFile::class)->delete();

        return 0;
    }

    /**
     * Stop the RoadRunner server for Octane.
     *
     * @return int
     */
    protected function stopRoadRunnerServer()
    {
        $inspector = app(RoadRunnerServerProcessInspector::class);

        if (! $inspector->serverIsRunning()) {
            $this->error('RoadRunner server is not running.');

            return 1;
        }

        $this->info('Stopping server...');

        $inspector->stopServer();

        app(RoadRunnerServerStateFile::class)->delete();

        return 0;
    }
}
