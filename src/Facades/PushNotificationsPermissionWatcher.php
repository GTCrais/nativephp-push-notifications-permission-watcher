<?php

namespace GTCrais\Native\Mobile\PushNotificationsPermissionWatcher\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void watch()
 * @method static string|null checkPermission()
 *
 * @see \GTCrais\Native\Mobile\PushNotificationsPermissionWatcher\PushNotificationsPermissionWatcher
 */
class PushNotificationsPermissionWatcher extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \GTCrais\Native\Mobile\PushNotificationsPermissionWatcher\PushNotificationsPermissionWatcher::class;
    }
}
