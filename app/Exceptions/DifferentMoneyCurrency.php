<?php

namespace App\Exceptions;

use App\Exceptions\Traits\ApiRenderable;
use Exception;

class DifferentMoneyCurrency extends Exception
{
    use ApiRenderable;
}
