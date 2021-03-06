<?php

namespace App\Exceptions;

use Exception;

class MoneyCannotBeNegative extends Exception
{
    use ApiRenderable;
}
