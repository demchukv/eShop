<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        "admin/settings/*",
        "/payments/response*",
        "/cart*",
        "/installer*",
        "/webhook/*",
        "auth/google/callback*",
        "auth/facebook/callback*",
        "payments/stripe-response*",
    ];
}
