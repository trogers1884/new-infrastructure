<?php
namespace App\Components\Api\Http\Controllers\v1\Loyalty;

use App\Components\Api\Http\Controllers\BaseApiController;

class LoyaltyController extends BaseApiController
{
    public function hello()
    {
        return $this->respondSuccess(
            ['message' => 'Hello from Loyalty System'],
            'Loyalty module active'
        );
    }
}
