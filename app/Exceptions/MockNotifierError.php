<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\Traits\ApiRenderable;

class MockNotifierError extends Exception
{
    use ApiRenderable;
}
