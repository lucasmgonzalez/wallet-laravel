<?php

namespace App\Notifications\Contracts;

interface TextNotification
{
    public function toText($notifiable) : string;
}
