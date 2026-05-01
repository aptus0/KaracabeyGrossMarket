import Foundation
import UserNotifications
import Combine

@MainActor
class PushNotificationManager: NSObject, ObservableObject, UNUserNotificationCenterDelegate {
    static let shared = PushNotificationManager()

    @Published var isAuthorized = false
    @Published var lastNotification: UNNotificationResponse?

    override private init() {
        super.init()
        UNUserNotificationCenter.current().delegate = self
    }

    func requestAuthorization() async -> Bool {
        do {
            let granted = try await UNUserNotificationCenter.current()
                .requestAuthorization(options: [.alert, .sound, .badge])
            await MainActor.run {
                self.isAuthorized = granted
            }
            return granted
        } catch {
            return false
        }
    }

    func registerForRemoteNotifications() {
        DispatchQueue.main.async {
            UIApplication.shared.registerForRemoteNotifications()
        }
    }

    func handleRemoteNotification(_ userInfo: [AnyHashable: Any]) {
        if let orderStatus = userInfo["order_status"] as? String,
           let orderId = userInfo["order_id"] as? String {
            handleOrderNotification(orderId: orderId, status: orderStatus)
        }
    }

    private func handleOrderNotification(orderId: String, status: String) {
        Task {
            // Burada order detail'ı yükleme logic'i olacak
            print("Order \(orderId) status: \(status)")
        }
    }

    func userNotificationCenter(
        _ center: UNUserNotificationCenter,
        willPresent notification: UNNotification,
        withCompletionHandler completionHandler: @escaping (UNNotificationPresentationOptions) -> Void
    ) {
        completionHandler([.banner, .sound, .badge])
    }

    func userNotificationCenter(
        _ center: UNUserNotificationCenter,
        didReceive response: UNNotificationResponse,
        withCompletionHandler completionHandler: @escaping () -> Void
    ) {
        lastNotification = response
        handleRemoteNotification(response.notification.request.content.userInfo)
        completionHandler()
    }
}
