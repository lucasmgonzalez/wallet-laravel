<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class TransactionNotAuthorized extends Exception
{
    use ApiRenderable;

    public function __construct()
    {
        parent::__construct('Transaction not authorized by the service');
    }
}
