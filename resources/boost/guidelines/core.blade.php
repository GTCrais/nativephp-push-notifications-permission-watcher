## gtcrais/nativephp-push-notifications-permission-watcher

NativePHP Mobile plugin which emits an event when the push notifications permission changes.

### Installation

```bash
composer require gtcrais/nativephp-push-notifications-permission-watcher
```

### PHP Usage (Livewire/Blade)

Use the `PushNotificationsPermissionWatcher` facade:

@verbatim
<code-snippet name="Using PushNotificationsPermissionWatcher Facade" lang="php">
use GTCrais\Native\Mobile\PushNotificationsPermissionWatcher\Facades\PushNotificationsPermissionWatcher;

// Request permission (shows native dialog if not yet determined)
PushNotificationsPermissionWatcher::watch();

// Check current permission status without requesting
$status = PushNotificationsPermissionWatcher::checkPermission(); // 'granted', 'denied', or 'not_determined' (iOS only)
</code-snippet>
@endverbatim

### Available Methods

- `PushNotificationsPermissionWatcher::watch()`: Request push notification permission and return the result
- `PushNotificationsPermissionWatcher::checkPermission()`: Check current permission status without requesting

### Events

- `PushNotificationsPermissionChanged`: Dispatched after `watch()` completes with the permission result

@verbatim
<code-snippet name="Listening for PushNotificationsPermissionChanged Events" lang="php">
use Native\Mobile\Attributes\OnNative;
use GTCrais\Native\Mobile\PushNotificationsPermissionWatcher\Events\PushNotificationsPermissionChanged;

#[OnNative(PushNotificationsPermissionChanged::class)]
public function handlePushNotificationsPermissionChanged(string $status)
{
    // $status is 'granted' or 'denied'
}
</code-snippet>
@endverbatim

### JavaScript Usage (Vue/React/Inertia)

@verbatim
<code-snippet name="Using PushNotificationsPermissionWatcher in JavaScript" lang="javascript">
import { On } from '#nativephp';
import { PushNotificationsPermissionWatcher, PushNotificationsPermissionEvents } from '@gtcrais/nativephp-push-notifications-permission-watcher';

// Listen for the permission result
On(PushNotificationsPermissionEvents.PushNotificationsPermissionChanged, ({ status }) => {
    console.log(status); // 'granted' or 'denied'
});

// Trigger the permission dialog
await PushNotificationsPermissionWatcher.watch();
</code-snippet>
@endverbatim
