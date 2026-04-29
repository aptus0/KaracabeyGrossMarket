import SwiftUI

struct FavoritesView: View {
    @EnvironmentObject private var favManager: FavoritesManager
    @EnvironmentObject private var authManager: AuthManager
    @State private var showLogin = false

    private let columns = [GridItem(.flexible()), GridItem(.flexible())]

    var body: some View {
        Group {
            if !authManager.isLoggedIn {
                VStack(spacing: 20) {
                    Image(systemName: "heart.slash").font(.system(size: 64)).foregroundColor(.gray.opacity(0.4))
                    Text("Favorileri görmek için giriş yapın")
                        .font(.poppins(weight: .medium, size: 15)).foregroundColor(.secondary)
                    Button { showLogin = true } label: {
                        Text("Giriş Yap").font(.poppins(weight: .bold, size: 15))
                            .frame(maxWidth: 220).padding(.vertical, 12)
                            .background(Color.kgmOrange).foregroundColor(.white).cornerRadius(12)
                    }
                }
                .frame(maxWidth: .infinity, maxHeight: .infinity)
            } else if favManager.products.isEmpty {
                VStack(spacing: 16) {
                    Image(systemName: "heart").font(.system(size: 64)).foregroundColor(.gray.opacity(0.4))
                    Text("Henüz favori ürününüz yok")
                        .font(.poppins(weight: .medium, size: 15)).foregroundColor(.secondary)
                }
                .frame(maxWidth: .infinity, maxHeight: .infinity)
            } else {
                ScrollView {
                    LazyVGrid(columns: columns, spacing: 14) {
                        ForEach(favManager.products) { product in
                            NavigationLink(destination: ProductDetailView(slug: product.slug)) {
                                ProductCard(product: product)
                            }
                            .buttonStyle(.plain)
                        }
                    }
                    .padding()
                }
            }
        }
        .navigationTitle("Favorilerim")
        .sheet(isPresented: $showLogin) { LoginView() }
        .task { await favManager.fetchFavorites() }
    }
}
