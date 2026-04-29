import SwiftUI

@main
struct Karacabey_Gross_MarketApp: App {
    @StateObject private var authManager = AuthManager.shared
    @StateObject private var cartManager = CartManager.shared
    @StateObject private var favManager  = FavoritesManager.shared

    var body: some Scene {
        WindowGroup {
            RootView()
                .environmentObject(authManager)
                .environmentObject(cartManager)
                .environmentObject(favManager)
        }
    }
}
