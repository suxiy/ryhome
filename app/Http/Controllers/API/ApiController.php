<?php

namespace App\Http\Controllers\API;

use App\Http\Helpers\Api\ApiResponse;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    use ApiResponse, AuthorizesRequests, DispatchesJobs, ValidatesRequests;

}
