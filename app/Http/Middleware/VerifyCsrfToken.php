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
        //
        'http://127.0.0.1:8000/webhook/*',
        'http://127.0.0.1:8000/fetch_contact_grmax/',
        'http://127.0.0.1:8000/fetch_contact_grmax_reverse/',
        'http://127.0.0.1:8000/fetch_gr_max_pro/'

    ];
}
