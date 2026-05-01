import SwiftUI
import UIKit

@main
struct Karacabey_Gross_MarketApp: App {
    @UIApplicationDelegateAdaptor(AppDelegate.self) private var appDelegate

    @StateObject private var authManager = AuthManager.shared
    @StateObject private var cartManager = CartManager.shared
    @StateObject private var favManager  = FavoritesManager.shared
    @StateObject private var pushManager = PushNotificationManager.shared

    var body: some Scene {
        WindowGroup {
            RootView()
                .environmentObject(authManager)
                .environmentObject(cartManager)
                .environmentObject(favManager)
                .environmentObject(pushManager)
        }
    }

    init() {
        Task {
            _ = await PushNotificationManager.shared.requestAuthorization()
            await PushNotificationManager.shared.registerForRemoteNotifications()
        }
    }
}
