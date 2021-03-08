<?php

namespace App\Exceptions;

use App\Exceptions\Traits\ApiRenderable;
use Exception;

class MoneySubtractionOverflow extends Exception
{
    use ApiRenderable;
}
