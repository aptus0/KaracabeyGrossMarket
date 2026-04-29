import SwiftUI

struct RootView: View {
    @State private var splashDone = false
    @EnvironmentObject private var authManager: AuthManager

    var body: some View {
        Group {
            if !splashDone {
                SplashView(isActive: $splashDone)
            } else {
                MainTabView()
            }
        }
    }
}
