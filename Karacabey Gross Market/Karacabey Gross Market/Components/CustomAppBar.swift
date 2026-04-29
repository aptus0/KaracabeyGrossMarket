import SwiftUI

// CartBarButton — toolbar'da kullanılmak üzere
struct CartBarButton: View {
    @EnvironmentObject private var cartManager: CartManager

    var body: some View {
        ZStack(alignment: .topTrailing) {
            Image(systemName: "cart")
                .font(.system(size: 20))
                .foregroundColor(.kgmDarkGray)
                .padding(.top, 2).padding(.trailing, 2)

            if cartManager.totalCount > 0 {
                Text("\(cartManager.totalCount)")
                    .font(.system(size: 9, weight: .bold))
                    .foregroundColor(.white)
                    .frame(width: 15, height: 15)
                    .background(Color.red)
                    .clipShape(Circle())
                    .offset(x: 5, y: -5)
            }
        }
    }
}
