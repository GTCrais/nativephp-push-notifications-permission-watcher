<?php

namespace GTCrais\Native\Mobile\PushNotificationsPermissionWatcher;

class PushNotificationsPermissionWatcher
{
    /**
     * Trigger the native push notification permission dialog.
     * The PushNotificationsPermissionChanged event is dispatched by the native side
     * when the user responds.
     */
    public function watch(): void
    {
        if (function_exists('nativephp_call')) {
            nativephp_call('PushNotificationsPermissionWatcher.Watch', '{}');
        }
    }

    /**
     * Check the current push notification permission status without triggering the dialog.
     */
    public function checkPermission(): ?string
    {
        if (function_exists('nativephp_call')) {
            $result = nativephp_call('PushNotificationsPermissionWatcher.CheckPermission', '{}');

            if ($result) {
                $decoded = json_decode($result);

                return $decoded->data->status ?? null;
            }
        }

        return null;
    }
}
