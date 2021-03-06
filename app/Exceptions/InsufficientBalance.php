<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InsufficientBalance extends Exception
{
    use ApiRenderable;

    public function __construct()
    {
        parent::__construct('Insufficient Balance - Payer does not have enough balance to make this transaction');
    }
}
