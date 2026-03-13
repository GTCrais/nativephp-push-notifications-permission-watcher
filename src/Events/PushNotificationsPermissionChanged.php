<?php

namespace GTCrais\Native\Mobile\PushNotificationsPermissionWatcher\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PushNotificationsPermissionChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $status,
    ) {}
}
