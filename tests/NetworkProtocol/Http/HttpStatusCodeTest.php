<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\Tests\NetworkProtocol\Http;

use Luyiyuan\Toolkits\NetworkProtocol\Http\HttpStatusCode;
use Luyiyuan\Toolkits\Tests\TestCase;

/**
 * Class HttpStatusCodeTest
 * @package Luyiyuan\Toolkits\Tests\NetworkProtocol\Http
 */
class HttpStatusCodeTest extends TestCase
{
    /**
     * @var HttpStatusCode
     */
    private HttpStatusCode $httpStatusCode;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->httpStatusCode = new HttpStatusCode();
    }
}