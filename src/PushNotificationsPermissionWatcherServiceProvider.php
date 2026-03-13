<?php

namespace GTCrais\Native\PushNotificationsPermissionWatcher;

use Illuminate\Support\ServiceProvider;
use GTCrais\Native\PushNotificationsPermissionWatcher\Commands\CopyAssetsCommand;

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
