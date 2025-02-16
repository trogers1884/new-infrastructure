<?php

namespace App\Components\Admin\Http\Middleware;

use App\Http\Middleware\BaseAuthentication;

class AdminAuthentication extends BaseAuthentication
{
    protected function getLoginRoute(): string
    {
        return 'login';
    }
}

