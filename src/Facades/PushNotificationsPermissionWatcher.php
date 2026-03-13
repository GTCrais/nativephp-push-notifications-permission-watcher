<?php

namespace GTCrais\Native\PushNotificationsPermissionWatcher\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void watch()
 * @method static string|null checkPermission()
 *
 * @see \GTCrais\Native\PushNotificationsPermissionWatcher\PushNotificationsPermissionWatcher
 */
class PushNotificationsPermissionWatcher extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \GTCrais\Native\PushNotificationsPermissionWatcher\PushNotificationsPermissionWatcher::class;
    }
}
