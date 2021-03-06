<?php

namespace App\Exceptions;

use Exception;

class UserTypeCannotTransferMoney extends BaseException
{
    public function __construct()
    {
        parent::__construct('This user type cannot transfer money');
    }
}
