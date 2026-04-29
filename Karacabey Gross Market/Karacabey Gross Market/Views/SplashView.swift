import SwiftUI

struct SplashView: View {
    @Binding var isActive: Bool
    @State private var scale: CGFloat = 0.7
    @State private var opacity: Double = 0

    var body: some View {
        ZStack {
            Color.kgmOrange.ignoresSafeArea()
            VStack(spacing: 12) {
                Image(systemName: "cart.fill")
                    .font(.system(size: 72, weight: .bold))
                    .foregroundColor(.white)
                    .scaleEffect(scale)
                    .opacity(opacity)

                Text("Karacabey\nGross Market")
                    .font(.poppins(weight: .bold, size: 26))
                    .foregroundColor(.white)
                    .multilineTextAlignment(.center)
                    .opacity(opacity)

                Text("Toptan fiyatına, güvenle alışveriş")
                    .font(.poppins(weight: .medium, size: 14))
                    .foregroundColor(.white.opacity(0.8))
                    .opacity(opacity)
            }
        }
        .onAppear {
            withAnimation(.spring(duration: 0.8)) { scale = 1.0; opacity = 1.0 }
            DispatchQueue.main.asyncAfter(deadline: .now() + 2.2) {
                withAnimation { isActive = true }
            }
        }
    }
}
