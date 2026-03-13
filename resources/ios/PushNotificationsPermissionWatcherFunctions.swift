import Foundation
import UserNotifications

enum PushNotificationsPermissionWatcherFunctions {

    class Watch: BridgeFunction {
        func execute(parameters: [String: Any]) throws -> [String: Any] {
            let eventClass = "GTCrais\\Native\\Mobile\\PushNotificationsPermissionWatcher\\Events\\PushNotificationsPermissionChanged"

            UNUserNotificationCenter.current().requestAuthorization(options: [.alert, .badge, .sound]) { _, _ in
                // Verify actual status before emitting
                UNUserNotificationCenter.current().getNotificationSettings { settings in
                    if settings.authorizationStatus == .notDetermined {
                        return
                    }

                    let status = settings.authorizationStatus == .authorized ? "granted" : "denied"
                    let payload: [String: Any] = ["status": status]
                    LaravelBridge.shared.send?(eventClass, payload)
                }
            }

            return BridgeResponse.success(data: ["status": "pending"])
        }
    }

    class CheckPermission: BridgeFunction {
        func execute(parameters: [String: Any]) throws -> [String: Any] {
            let semaphore = DispatchSemaphore(value: 0)
            var currentStatus = "not_determined"

            UNUserNotificationCenter.current().getNotificationSettings { settings in
                switch settings.authorizationStatus {
                case .authorized:
                    currentStatus = "granted"
                case .denied:
                    currentStatus = "denied"
                case .notDetermined:
                    currentStatus = "not_determined"
                case .provisional:
                    currentStatus = "provisional"
                case .ephemeral:
                    currentStatus = "ephemeral"
                @unknown default:
                    currentStatus = "unknown"
                }
                semaphore.signal()
            }

            semaphore.wait()

            return BridgeResponse.success(data: ["status": currentStatus])
        }
    }
}
