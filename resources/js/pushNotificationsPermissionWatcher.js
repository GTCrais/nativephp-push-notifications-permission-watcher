/**
 * PushNotificationsPermissionWatcher Plugin for NativePHP Mobile
 *
 * @example
 * import { On } from '#nativephp';
 * import { PushNotificationsPermissionWatcher, PushNotificationsPermissionEvents } from '@gtcrais/nativephp-push-notifications-permission-watcher';
 *
 * // Start watching - triggers the native permission dialog
 * await PushNotificationsPermissionWatcher.watch();
 *
 * // Listen for the permission result
 * On(PushNotificationsPermissionEvents.PushNotificationsPermissionChanged, ({ status }) => {
 *     console.log(status); // 'granted' or 'denied'
 * });
 */

const baseUrl = '/_native/api/call';

/**
 * Internal bridge call function
 * @private
 */
async function bridgeCall(method, params = {}) {
    const response = await fetch(baseUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify({ method, params })
    });

    const result = await response.json();

    if (result.status === 'error') {
        throw new Error(result.message || 'Native call failed');
    }

    const nativeResponse = result.data;
    if (nativeResponse && nativeResponse.data !== undefined) {
        return nativeResponse.data;
    }

    return nativeResponse;
}

/**
 * Event constants for use with NativePHP's On() listener.
 */
export const PushNotificationsPermissionEvents = {
    PushNotificationsPermissionChanged: 'GTCrais\\Native\\PushNotificationsPermissionWatcher\\Events\\PushNotificationsPermissionChanged'
};

/**
 * Trigger the native push notification permission dialog.
 * Listen for the result using On(PushNotificationsPermissionEvents.PushNotificationsPermissionChanged, callback).
 * @returns {Promise<{status: string}>} Resolves immediately with { status: 'pending' } while the dialog is shown
 */
export async function watch() {
    return bridgeCall('PushNotificationsPermissionWatcher.Watch');
}

/**
 * Check the current push notification permission status without triggering the dialog.
 * @returns {Promise<{status: string}>} status is 'granted', 'denied', or 'not_determined' (iOS only)
 */
export async function checkPermission() {
    return bridgeCall('PushNotificationsPermissionWatcher.CheckPermission');
}

/**
 * PushNotificationsPermissionWatcher namespace object
 */
export const PushNotificationsPermissionWatcher = {
    watch,
    checkPermission
};

export default PushNotificationsPermissionWatcher;
