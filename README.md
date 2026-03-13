# PushNotificationsPermissionWatcher Plugin for NativePHP Mobile

NativePHP Mobile plugin which emits an event when the push notifications permission changes.

## Installation

```bash
composer require gtcrais/nativephp-push-notifications-permission-watcher
```

## Usage

### PHP (Livewire/Blade)

```php
use GTCrais\Native\PushNotificationsPermissionWatcher\Facades\PushNotificationsPermissionWatcher;

// Request permission (shows native dialog if not yet determined)
PushNotificationsPermissionWatcher::watch();

// Check current permission status without requesting
$status = PushNotificationsPermissionWatcher::checkPermission(); // 'granted', 'denied', or 'not_determined' (iOS only)
```

### JavaScript (Vue/React/Inertia)

```javascript
import { On } from '#nativephp';
import { PushNotificationsPermissionWatcher, PushNotificationsPermissionEvents } from '@gtcrais/nativephp-push-notifications-permission-watcher';

// Listen for the permission result
On(PushNotificationsPermissionEvents.PushNotificationsPermissionChanged, ({ status }) => {
    console.log(status); // 'granted' or 'denied'
});

// Trigger the permission dialog
await PushNotificationsPermissionWatcher.watch();
```

## Listening for Events

### Livewire

```php
use Native\Mobile\Attributes\OnNative;
use GTCrais\Native\PushNotificationsPermissionWatcher\Events\PushNotificationsPermissionChanged;

#[OnNative(PushNotificationsPermissionChanged::class)]
public function handlePushNotificationsPermissionChanged(string $status)
{
    // $status is 'granted' or 'denied'
}
```

### Laravel Event Listener

```php
use GTCrais\Native\PushNotificationsPermissionWatcher\Events\PushNotificationsPermissionChanged;

Event::listen(PushNotificationsPermissionChanged::class, function (PushNotificationsPermissionChanged $event) {
    // $event->status is 'granted' or 'denied'
});
```

## License

MIT
