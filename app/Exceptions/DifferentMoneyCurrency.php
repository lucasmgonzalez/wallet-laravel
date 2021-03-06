<?php

namespace App\Exceptions;

use Exception;

class DifferentMoneyCurrency extends Exception
{
    use ApiRenderable;
}
