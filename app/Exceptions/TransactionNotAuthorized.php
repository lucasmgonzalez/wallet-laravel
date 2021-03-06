<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class TransactionNotAuthorized extends BaseException
{
    public function __construct()
    {
        parent::__construct('Transaction not authorized');
    }
}
