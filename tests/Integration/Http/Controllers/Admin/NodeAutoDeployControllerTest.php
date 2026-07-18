<?php

namespace Hexactyl\\Tests\Integration\Http\Controllers\Admin;

use Hexactyl\\Models\Node;
use Hexactyl\\Models\User;
use Hexactyl\\Models\ApiKey;
use Hexactyl\\Models\Location;
use Hexactyl\\Services\Acl\Api\AdminAcl;
use Hexactyl\\Tests\Integration\Http\HttpTestCase;

class NodeAutoDeployControllerTest extends HttpTestCase
{
    public function testGeneratedTokenHasNodeWritePermission(): void
    {
        $node = Node::factory()->for(Location::factory())->create();

        $response = $this->actingAs(User::factory()->admin()->create())
            ->postJson(route('admin.nodes.view.configuration.token', ['node' => $node]));

        $response->assertOk();
        $response->assertJsonPath('node', $node->id);

        $key = ApiKey::query()
            ->where('identifier', substr($response->json('token'), 0, ApiKey::IDENTIFIER_LENGTH))
            ->firstOrFail();

        $this->assertSame(AdminAcl::READ | AdminAcl::WRITE, $key->r_nodes);
    }
}
