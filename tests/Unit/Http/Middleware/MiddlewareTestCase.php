<?php

namespace Hexactyl\\Tests\Unit\Http\Middleware;

use Hexactyl\\Tests\TestCase;
use Hexactyl\\Tests\Traits\Http\RequestMockHelpers;
use Hexactyl\\Tests\Traits\Http\MocksMiddlewareClosure;
use Hexactyl\\Tests\Assertions\MiddlewareAttributeAssertionsTrait;

abstract class MiddlewareTestCase extends TestCase
{
    use MiddlewareAttributeAssertionsTrait;
    use MocksMiddlewareClosure;
    use RequestMockHelpers;

    /**
     * Setup tests with a mocked request object and normal attributes.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->buildRequestMock();
    }
}
