<?php

namespace App\NotificationChannels;

use App\Exceptions\MockNotifierError;
use App\Notifications\Contracts\TextNotification;
use App\Services\MockNotifierService;
use Illuminate\Notifications\Notification;

class MockChannel
{
    protected MockNotifierService $service;

    public function __construct(MockNotifierService $service) {
        $this->service = $service;
    }

    public function send($notifiable, TextNotification $notification)
    {
        // Retrieve message from notification
        $message = $notification->toText($notifiable);

        // Make Request to Service
        try {
            $this->service->send($message);
        } catch (MockNotifierError $e) {
            // Create Job to retry sending notification
        }

        return true;
    }
}
