import SwiftUI
import Combine

struct CustomAppBar: View {
    let title: String?
    let showBackButton: Bool
    let showCart: Bool
    
    // We'll use a mocked cart count for now.
    // In MVVM, this would be bound to a CartViewModel.
    @State private var cartCount: Int = 3
    @Environment(\.presentationMode) var presentationMode
    
    init(title: String? = nil, showBackButton: Bool = false, showCart: Bool = true) {
        self.title = title
        self.showBackButton = showBackButton
        self.showCart = showCart
    }
    
    var body: some View {
        HStack {
            if showBackButton {
                Button(action: {
                    presentationMode.wrappedValue.dismiss()
                }) {
                    Image(systemName: "chevron.left")
                        .font(.system(size: 20, weight: .semibold))
                        .foregroundColor(.kgmDarkGray)
                }
            } else {
                // Menu Icon for drawer/sidebar
                Button(action: {
                    // Action to open drawer
                }) {
                    Image(systemName: "line.3.horizontal")
                        .font(.system(size: 24, weight: .medium))
                        .foregroundColor(.kgmDarkGray)
                }
            }
            
            Spacer()
            
            if let title = title {
                Text(title)
                    .font(.poppins(weight: .bold, size: 18))
            } else {
                // Logo
                Text("KG") // Placeholder for actual Image("Logo")
                    .font(.poppins(weight: .bold, size: 24))
                    .foregroundColor(.kgmOrange)
            }
            
            Spacer()
            
            if showCart {
                Button(action: {
                    // Navigate to Cart View
                }) {
                    ZStack(alignment: .topTrailing) {
                        Image(systemName: "cart")
                            .font(.system(size: 24))
                            .foregroundColor(.kgmDarkGray)
                            .padding(.top, 4)
                            .padding(.trailing, 4)
                        
                        if cartCount > 0 {
                            Text("\(cartCount)")
                                .font(.system(size: 10, weight: .bold))
                                .foregroundColor(.white)
                                .frame(width: 16, height: 16)
                                .background(Color.red)
                                .clipShape(Circle())
                                .offset(x: 4, y: -4)
                        }
                    }
                }
            } else {
                // Placeholder to balance the HStack if no cart
                Spacer().frame(width: 24)
            }
        }
        .padding(.horizontal)
        .padding(.vertical, 12)
        .background(Color.white)
        .shadow(color: Color.black.opacity(0.05), radius: 5, x: 0, y: 2)
    }
}

#Preview {
    VStack {
        CustomAppBar(title: nil, showBackButton: false, showCart: true)
        Spacer()
    }
}
