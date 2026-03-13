<?php

namespace GTCrais\Native\PushNotificationsPermissionWatcher\Commands;

use Native\Mobile\Plugins\Commands\NativePluginHookCommand;

/**
 * Copy assets hook command for PushNotificationsPermissionWatcher plugin.
 *
 * This hook runs during the copy_assets phase of the build process.
 * Use it to copy ML models, binary files, or other assets that need
 * to be in specific locations in the native project.
 *
 * @see \Native\Mobile\Plugins\Commands\NativePluginHookCommand
 */
class CopyAssetsCommand extends NativePluginHookCommand
{
    protected $signature = 'nativephp:push-notifications-permission-watcher:copy-assets';

    protected $description = 'Copy assets for PushNotificationsPermissionWatcher plugin';

    public function handle(): int
    {
        // Example: Copy different files based on platform
        if ($this->isAndroid()) {
            $this->copyAndroidAssets();
        }

        if ($this->isIos()) {
            $this->copyIosAssets();
        }

        return self::SUCCESS;
    }

    /**
     * Copy assets for Android build
     */
    protected function copyAndroidAssets(): void
    {
        $this->info('Android assets copied for PushNotificationsPermissionWatcher');
    }

    /**
     * Copy assets for iOS build
     */
    protected function copyIosAssets(): void
    {
        $this->info('iOS assets copied for PushNotificationsPermissionWatcher');
    }
}
