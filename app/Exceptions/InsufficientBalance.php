<?php

namespace App\Exceptions;

use App\Exceptions\Traits\ApiRenderable;
use Exception;

class InsufficientBalance extends Exception
{
    use ApiRenderable;

    public function __construct()
    {
        parent::__construct('Insufficient Balance - Payer does not have enough balance to make this transaction');
    }
}
