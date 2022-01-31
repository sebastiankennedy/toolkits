<?php

declare(strict_types=1);

namespace Luyiyuan\Toolkits\NetworkProtocol\Http;

/**
 * Class HttpHeader
 * @package Luyiyuan\Toolkits\NetworkProtocol\Http
 * @link https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Headers
 */
final class HttpHeader
{
    /**
     * 客户端用来告知服务端：客户端它可以处理的内容类型
     *
     * @usage Accept: $mime_type/$mime_subtype
     *
     * @example Accept: text/html
     * @example Accept: text/html, application/json
     * @example Accept: image/*
     */
    public const ACCEPT = 'Accept';
    public const ACCEPT_TEXT_HTML = 'text/html';
    public const ACCEPT_APPLICATION_XML = 'application/xml';
    public const ACCEPT_APPLICATION_JSON = 'application/json';
    public const ACCEPT_DEFAULT = self::ACCEPT_APPLICATION_JSON;

    /**
     * 客户端用来告知服务端：客户端它可以处理的字符集类型
     *
     * @usage Accept-Charset: $charset
     *
     * @example Accept-Charset: utf-8
     * @example Accept-Charset: utf-8, iso-8859-1;q=0.5
     */
    public const ACCEPT_CHARSET = 'Accept-Charset';
    public const ACCEPT_CHARSET_UTF8 = 'utf-8';
    public const ACCEPT_CHARSET_DEFAULT = self::ACCEPT_CHARSET_UTF8;

    /**
     * 客户端用来告知服务端：客户端它可以理解的压缩算法
     *
     * @example Accept-Encoding: gzip
     * @example Accept-Encoding: gzip, compress, deflate
     * @example Accept-Encoding: br;q=1.0, gzip;q=0.8, *;q=0.1
     */
    public const ACCEPT_ENCODING = 'Accept-Encoding';
    public const ACCEPT_ENCODING_GZIP = 'gzip';
    public const ACCEPT_ENCODING_COMPRESS = 'compress';
    public const ACCEPT_ENCODING_DEFLATE = 'deflate';
    public const ACCEPT_ENCODING_BR = 'br';
    public const ACCEPT_ENCODING_DEFAULT = self::ACCEPT_ENCODING_GZIP;


    /**
     * 客户端声明它可以理解的自然语言，以及优先选择的区域方言。
     *
     * @usage Accept-Language: $language
     *
     * @example Accept-Language: *
     * @example Accept-Language: zh-CN
     * @example Accept-Language: zh-CN, en-US;q=0.8
     */
    public const ACCEPT_LANGUAGE = 'Accept-Language';
    public const ACCEPT_LANGUAGE_ZH_CN = 'zh-CN';
    public const ACCEPT_LANGUAGE_EN_US = 'en-US';

    /**
     * 客户端在此头部携带服务器用于验证用户身份的凭证
     *
     * @usage AUTHORIZATION $type $credentials
     *
     * @example AUTHORIZATION Basic $token
     * @example AUTHORIZATION Bearer $token
     */
    public const AUTHORIZATION = 'Authorization';
    public const AUTHORIZATION_BASIC = 'Basic';
    public const AUTHORIZATION_BEARER = 'Bearer';
}
