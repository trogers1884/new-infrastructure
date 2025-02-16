<?php
namespace App\Components\Api\Http\Controllers\v1\Management;

use App\Components\Api\Http\Controllers\BaseApiController;

class ReportingController extends BaseApiController
{
    public function hello()
    {
        return $this->respondSuccess(
            ['message' => 'Hello from Management Reporting'],
            'Reporting module active'
        );
    }
}
