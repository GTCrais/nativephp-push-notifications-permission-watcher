# PushNotificationsPermissionWatcher Plugin for NativePHP Mobile

NativePHP Mobile plugin which emits an event when the push notifications permission changes.

## Installation

```bash
composer require gtcrais/nativephp-push-notifications-permission-watcher
```

## Usage

### PHP (Livewire/Blade)

```php
use GTCrais\Native\Mobile\PushNotificationsPermissionWatcher\Facades\PushNotificationsPermissionWatcher;

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
use GTCrais\Native\Mobile\PushNotificationsPermissionWatcher\Events\PushNotificationsPermissionChanged;

#[OnNative(PushNotificationsPermissionChanged::class)]
public function handlePushNotificationsPermissionChanged(string $status)
{
    // $status is 'granted' or 'denied'
}
```

### Laravel Event Listener

```php
use GTCrais\Native\Mobile\PushNotificationsPermissionWatcher\Events\PushNotificationsPermissionChanged;

Event::listen(PushNotificationsPermissionChanged::class, function (PushNotificationsPermissionChanged $event) {
    // $event->status is 'granted' or 'denied'
});
```

## Full Code Example

`BaseLayout.vue`
```js
mounted() {
	PushNotificationService.registerTokenGeneratedListener();
	PushNotificationService.registerPushNotificationsPermissionChangeListener();
}
```

`PushNotificationService.js`
```js
import { PushNotifications, On, Events } from '#nativephp';
import { PushNotificationsPermissionWatcher, PushNotificationsPermissionEvents } from '@gtcrais/nativephp-push-notifications-permission-watcher';
import { useAuthStore } from "@/stores/auth-store.js";
import axios from "axios";
import { useAppDataStore } from "@/stores/app-data-store.js";

export default class PushNotificationService
{
	static async enroll()
	{
		await PushNotificationsPermissionWatcher.watch();
	}

	static async refreshPermissionStatus()
	{
		const status = await PushNotifications.checkPermission();
		useAuthStore().setPushNotificationsPermissionStatus(status);
	}

	static async getTokenAndStore()
	{
		const token = await PushNotifications.getToken();

		if (token) {
			await this.storeToken(token);
		}
	}

	static async storeToken(token)
	{
		await axios.post('/push-notifications-token', { token })
			.catch((error) => {
			    console.log('[LC]', JSON.stringify(error));
			});
	}

	static async deleteToken()
	{
		await axios.delete('/push-notifications-token');
	}

	static registerPushNotificationsPermissionChangeListener()
	{
		On(PushNotificationsPermissionEvents.PushNotificationsPermissionChanged, async ({ status }) => {
			setTimeout(async () => {
				await this.refreshPermissionStatus();

				if (this.isGranted) {
					// Now that the permission is granted, this will just go and fetch 
                    // the token, then fire the TokenGenerated event which we listen to
					await PushNotifications.enroll();
				}
			}, 200);
		});
	}

	static registerTokenGeneratedListener()
	{
		On(Events.PushNotification.TokenGenerated, async ({ token }) => {
			await this.handleTokenGenerated(token);
		});
	}

	static async handleTokenGenerated(token)
	{
		if (token) {
			await this.storeToken(token);
		}
	}

	static get permissionStatus()
	{
		return useAuthStore().pushNotificationsPermissionStatus;
	}

	static get isNotDetermined()
	{
		return this.permissionStatus === 'not_determined';
	}

	static get isDetermined()
	{
		return !this.isNotDetermined;
	}

	static get isDenied()
	{
		return this.permissionStatus === 'denied';
	}

	static get isGranted()
	{
		return (this.isDetermined && !this.isDenied);
	}
}
```

`auth-store.js`
```js
export const useAuthStore = defineStore('auth', {
	state: () => ({
		_pushNotificationsPermissionStatus: null
	}),

	getters: {
		pushNotificationsPermissionStatus: (state) => state._pushNotificationsPermissionStatus,
	},

	actions: {
		setPushNotificationsPermissionStatus(status) {
			this._pushNotificationsPermissionStatus = status;
		}
	}
});
```

## License

MIT
