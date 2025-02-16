<?php
namespace App\Components\Api\Http\Controllers\v1\Sbux;

use App\Components\Api\Http\Controllers\BaseApiController;

class SbuxController extends BaseApiController
{
    public function hello()
    {
        return $this->respondSuccess(
            ['message' => 'Hello from Sbux System'],
            'Sbux module active'
        );
    }
}
