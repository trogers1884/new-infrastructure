<?php
namespace App\Components\Api\Http\Controllers\v1\Trading;

use App\Components\Api\Http\Controllers\BaseApiController;

class DeskController extends BaseApiController
{
    public function hello()
    {
        return $this->respondSuccess(
            ['message' => 'Hello from Trading Desk'],
            'Trading module active'
        );
    }
}
