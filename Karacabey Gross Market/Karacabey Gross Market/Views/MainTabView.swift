import SwiftUI

struct MainTabView: View {
    @EnvironmentObject private var cartManager: CartManager
    @State private var selectedTab = 0

    var body: some View {
        TabView(selection: $selectedTab) {
            NavigationStack {
                HomeView()
            }
            .tabItem { Label("Ana Sayfa", systemImage: "house.fill") }
            .tag(0)

            NavigationStack {
                ProductsView()
            }
            .tabItem { Label("Ürünler", systemImage: "square.grid.2x2.fill") }
            .tag(1)

            NavigationStack {
                CartView()
            }
            .tabItem {
                Label {
                    Text("Sepet")
                } icon: {
                    cartBadge
                }
            }
            .tag(2)

            NavigationStack {
                ProfileView()
            }
            .tabItem { Label("Hesabım", systemImage: "person.fill") }
            .tag(3)
        }
        .tint(.kgmOrange)
    }

    private var cartBadge: some View {
        ZStack(alignment: .topTrailing) {
            Image(systemName: "cart.fill")
            if cartManager.totalCount > 0 {
                Text("\(cartManager.totalCount)")
                    .font(.system(size: 9, weight: .bold))
                    .foregroundColor(.white)
                    .padding(3)
                    .background(Color.red)
                    .clipShape(Circle())
                    .offset(x: 6, y: -6)
            }
        }
    }
}
