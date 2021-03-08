<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\Traits\ApiRenderable;

class MoneyCannotBeNegative extends Exception
{
    use ApiRenderable;
}
