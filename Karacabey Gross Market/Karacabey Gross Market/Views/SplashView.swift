import SwiftUI

struct SplashView: View {
    @Binding var isActive: Bool
    @State private var scale: CGFloat = 0.8
    @State private var opacity: Double = 0.0
    
    var body: some View {
        ZStack {
            // Primary Brand Color
            Color.kgmOrange
                .ignoresSafeArea()
            
            VStack {
                // Placeholder for Logo
                Text("KG")
                    .font(.poppins(weight: .bold, size: 64))
                    .foregroundColor(.white)
                    .scaleEffect(scale)
                    .opacity(opacity)
                
                Text("Karacabey Gross Market")
                    .font(.poppins(weight: .bold, size: 18))
                    .foregroundColor(.white.opacity(0.9))
                    .padding(.top, 8)
                    .opacity(opacity)
            }
        }
        .onAppear {
            withAnimation(.easeIn(duration: 1.0)) {
                self.scale = 1.0
                self.opacity = 1.0
            }
            
            // Simulate network initialization or artificial delay
            DispatchQueue.main.asyncAfter(deadline: .now() + 2.5) {
                withAnimation {
                    self.isActive = true
                }
            }
        }
    }
}

#Preview {
    SplashView(isActive: .constant(false))
}
