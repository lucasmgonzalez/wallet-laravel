<?php

namespace App\Exceptions;

use Exception;

class MoneySubtractionOverflow extends Exception
{
    use ApiRenderable;
}
