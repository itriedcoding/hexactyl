<?php

namespace Hexactyl\\Http\Controllers\Api\Application\Servers;

use Hexactyl\\Models\Server;
use Hexactyl\\Services\Servers\BuildModificationService;
use Hexactyl\\Services\Servers\DetailsModificationService;
use Hexactyl\\Transformers\Api\Application\ServerTransformer;
use Hexactyl\\Http\Controllers\Api\Application\ApplicationApiController;
use Hexactyl\\Http\Requests\Api\Application\Servers\UpdateServerDetailsRequest;
use Hexactyl\\Http\Requests\Api\Application\Servers\UpdateServerBuildConfigurationRequest;

class ServerDetailsController extends ApplicationApiController
{
    /**
     * ServerDetailsController constructor.
     */
    public function __construct(
        private BuildModificationService $buildModificationService,
        private DetailsModificationService $detailsModificationService,
    ) {
        parent::__construct();
    }

    /**
     * Update the details for a specific server.
     *
     * @throws \Hexactyl\\Exceptions\DisplayException
     * @throws \Hexactyl\\Exceptions\Model\DataValidationException
     * @throws \Hexactyl\\Exceptions\Repository\RecordNotFoundException
     */
    public function details(UpdateServerDetailsRequest $request, Server $server): array
    {
        $updated = $this->detailsModificationService->returnUpdatedModel()->handle(
            $server,
            $request->validated()
        );

        return $this->fractal->item($updated)
            ->transformWith($this->getTransformer(ServerTransformer::class))
            ->toArray();
    }

    /**
     * Update the build details for a specific server.
     *
     * @throws \Hexactyl\\Exceptions\DisplayException
     * @throws \Hexactyl\\Exceptions\Model\DataValidationException
     * @throws \Hexactyl\\Exceptions\Repository\RecordNotFoundException
     */
    public function build(UpdateServerBuildConfigurationRequest $request, Server $server): array
    {
        $server = $this->buildModificationService->handle($server, $request->validated());

        return $this->fractal->item($server)
            ->transformWith($this->getTransformer(ServerTransformer::class))
            ->toArray();
    }
}
