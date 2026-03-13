<?php

namespace GTCrais\Native\Mobile\PushNotificationsPermissionWatcher;

use Illuminate\Support\ServiceProvider;
use GTCrais\Native\Mobile\PushNotificationsPermissionWatcher\Commands\CopyAssetsCommand;

class PushNotificationsPermissionWatcherServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PushNotificationsPermissionWatcher::class, function () {
            return new PushNotificationsPermissionWatcher();
        });
    }

    public function boot(): void
    {
        // Register plugin hook commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                CopyAssetsCommand::class,
            ]);
        }
    }
}
