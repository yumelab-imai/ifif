<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // WebhookはPOSTメソッドでリクエストが来るので、 'line/*' のルートをCSRFの対象外に設定
        "line/*",
    ];
}
