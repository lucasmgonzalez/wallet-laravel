<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

trait ApiRenderable
{
    public function render(Request $request)
    {
        $content = $request->expectsJson()
            ? json_encode([
                'type' => static::class,
                'message' => $this->message
            ]) : $this->message;

        return response($content, 422);
    }
}
