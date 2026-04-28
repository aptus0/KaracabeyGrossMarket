import SwiftUI

struct RootView: View {
    @State private var isSplashActive = true
    
    var body: some View {
        Group {
            if isSplashActive {
                SplashView(isActive: $isSplashActive)
            } else {
                MainTabView()
            }
        }
    }
}

#Preview {
    RootView()
}
