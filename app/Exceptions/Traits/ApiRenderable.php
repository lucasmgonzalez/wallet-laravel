<?php

namespace App\Exceptions\Traits;

use Illuminate\Http\Request;

trait ApiRenderable
{
    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'type' => static::class,
                'message' => $this->message
            ], 422);
        }

    }
}
