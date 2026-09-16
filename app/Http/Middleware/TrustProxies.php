<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Se confía en todos los proxies porque el contenedor solo es alcanzable
     * a través del proxy/edge de la plataforma (Railway, etc.), nunca
     * directamente desde internet — sin esto, Laravel no lee X-Forwarded-Proto
     * y genera URLs de assets en http:// aunque el sitio se sirva en https://,
     * lo que el navegador bloquea como contenido mixto (CSS/JS no cargan).
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
