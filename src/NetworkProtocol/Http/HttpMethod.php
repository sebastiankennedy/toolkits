<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\NetworkProtocol\Http;

/**
 * Class HttpMethod
 * @package Luyiyuan\Toolkits\NetworkProtocol\Http
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
 */
final class HttpMethod
{
    public const GET = 'GET';
    public const HEAD = 'HEAD';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const DELETE = 'DELETE';
    public const CONNECT = 'CONNECT';
    public const OPTIONS = 'OPTIONS';
    public const PATCH = 'PATCH';
    public const TRACE = 'TRACE';
}
