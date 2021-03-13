<?php

namespace App\Http\Controllers\API\Me;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {
        return new UserResource($request->user());
    }
}
