<?php

namespace App\Exceptions;

use Exception;

class MockNotifierError extends Exception
{
    use ApiRenderable;
}
