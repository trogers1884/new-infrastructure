<?php
namespace App\Components\Api\Http\Controllers\v1\Cafe;

use App\Components\Api\Http\Controllers\BaseApiController;

class MenuController extends BaseApiController
{
    public function hello()
    {
        return $this->respondSuccess(
            ['message' => 'Hello from Cafe Menu System'],
            'Cafe module active'
        );
    }
}
