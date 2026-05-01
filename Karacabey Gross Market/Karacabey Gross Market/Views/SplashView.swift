import SwiftUI

struct SplashView: View {
    @Binding var isActive: Bool
    @State private var iconScale: CGFloat = 0.5
    @State private var iconOpacity: Double = 0
    @State private var textOpacity: Double = 0
    @State private var pulse: Bool = false

    var body: some View {
        ZStack {
            // Premium Gradient Background
            LinearGradient(
                gradient: Gradient(colors: [Color.kgmOrange, Color.kgmOrange.opacity(0.8)]),
                startPoint: .topLeading,
                endPoint: .bottomTrailing
            )
            .ignoresSafeArea()

            VStack(spacing: 24) {
                // Animated Icon
                ZStack {
                    Circle()
                        .fill(Color.white.opacity(0.2))
                        .frame(width: 140, height: 140)
                        .scaleEffect(pulse ? 1.2 : 1.0)
                        .opacity(pulse ? 0 : 1)
                    
                    Circle()
                        .fill(Color.white)
                        .frame(width: 100, height: 100)
                        .shadow(color: Color.black.opacity(0.15), radius: 20, x: 0, y: 10)
                    
                    Image("AppLogo")
                        .resizable()
                        .scaledToFit()
                        .frame(width: 70, height: 70)
                }
                .scaleEffect(iconScale)
                .opacity(iconOpacity)

                // Brand Text
                VStack(spacing: 8) {
                    Text("Karacabey")
                        .font(.system(size: 32, weight: .black, design: .rounded))
                        .foregroundColor(.white)
                    
                    Text("GROSS MARKET")
                        .font(.system(size: 20, weight: .bold, design: .rounded))
                        .foregroundColor(.white.opacity(0.9))
                        .tracking(2)
                }
                .opacity(textOpacity)
                .offset(y: textOpacity == 1 ? 0 : 20)

                Spacer().frame(height: 40)

                // Subtitle
                Text("Toptan fiyatına, güvenle alışveriş")
                    .font(.system(size: 15, weight: .medium, design: .rounded))
                    .foregroundColor(.white.opacity(0.8))
                    .opacity(textOpacity)
            }
        }
        .onAppear {
            // Entrance Animations
            withAnimation(.spring(response: 0.6, dampingFraction: 0.6)) {
                iconScale = 1.0
                iconOpacity = 1.0
            }
            
            withAnimation(.easeOut(duration: 0.6).delay(0.3)) {
                textOpacity = 1.0
            }
            
            // Pulse Animation
            withAnimation(.easeInOut(duration: 1.5).repeatForever(autoreverses: false).delay(0.6)) {
                pulse = true
            }
            
            // Transition to Main App
            DispatchQueue.main.asyncAfter(deadline: .now() + 2.5) {
                withAnimation(.easeInOut(duration: 0.4)) {
                    isActive = true
                }
            }
        }
    }
}
