<?php

namespace Hexactyl\\Http\Controllers\Api\Application\Servers;

use Hexactyl\\Models\User;
use Hexactyl\\Models\Server;
use Hexactyl\\Services\Servers\StartupModificationService;
use Hexactyl\\Transformers\Api\Application\ServerTransformer;
use Hexactyl\\Http\Controllers\Api\Application\ApplicationApiController;
use Hexactyl\\Http\Requests\Api\Application\Servers\UpdateServerStartupRequest;

class StartupController extends ApplicationApiController
{
    /**
     * StartupController constructor.
     */
    public function __construct(private StartupModificationService $modificationService)
    {
        parent::__construct();
    }

    /**
     * Update the startup and environment settings for a specific server.
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Hexactyl\\Exceptions\Http\Connection\DaemonConnectionException
     * @throws \Hexactyl\\Exceptions\Model\DataValidationException
     * @throws \Hexactyl\\Exceptions\Repository\RecordNotFoundException
     */
    public function index(UpdateServerStartupRequest $request, Server $server): array
    {
        $server = $this->modificationService
            ->setUserLevel(User::USER_LEVEL_ADMIN)
            ->handle($server, $request->validated());

        return $this->fractal->item($server)
            ->transformWith($this->getTransformer(ServerTransformer::class))
            ->toArray();
    }
}
