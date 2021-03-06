<?php

namespace App\Exceptions;

use Exception;

class UserTypeCannotTransferMoney extends Exception
{
    use ApiRenderable;

    public function __construct()
    {
        parent::__construct('This user type cannot transfer money');
    }
}
