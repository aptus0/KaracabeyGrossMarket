import SwiftUI

struct ProfileView: View {
    @EnvironmentObject private var authManager: AuthManager
    @StateObject private var authVM = AuthViewModel()
    @State private var showLogin = false

    var body: some View {
        Group {
            if authManager.isLoggedIn {
                loggedInView
            } else {
                guestView
            }
        }
        .navigationTitle("Hesabım")
        .navigationBarTitleDisplayMode(.inline)
        .sheet(isPresented: $showLogin) { LoginView() }
    }

    private var guestView: some View {
        VStack(spacing: 24) {
            Spacer()
            Image(systemName: "person.circle").font(.system(size: 80)).foregroundColor(.gray.opacity(0.4))
            Text("Henüz giriş yapmadınız")
                .font(.poppins(weight: .bold, size: 18))
            Text("Siparişlerinizi takip etmek ve favori ürünlerinizi\nkaydetmek için giriş yapın.")
                .font(.poppins(weight: .regular, size: 14))
                .foregroundColor(.kgmGray)
                .multilineTextAlignment(.center)
                .padding(.horizontal)
            Button { showLogin = true } label: {
                Text("Giriş Yap / Kayıt Ol")
                    .font(.poppins(weight: .bold, size: 16))
                    .frame(maxWidth: 280).padding(.vertical, 14)
                    .background(Color.kgmOrange).foregroundColor(.white)
                    .cornerRadius(14)
            }
            Spacer()
        }
    }

    private var loggedInView: some View {
        List {
            // Avatar section
            Section {
                HStack(spacing: 16) {
                    Circle()
                        .fill(Color.kgmOrange.opacity(0.15))
                        .frame(width: 60, height: 60)
                        .overlay(
                            Text(String(authManager.currentUser?.name.prefix(1) ?? "?"))
                                .font(.poppins(weight: .bold, size: 24))
                                .foregroundColor(.kgmOrange)
                        )
                    VStack(alignment: .leading, spacing: 2) {
                        Text(authManager.currentUser?.name ?? "")
                            .font(.poppins(weight: .bold, size: 16))
                        Text(authManager.currentUser?.email ?? "")
                            .font(.poppins(weight: .regular, size: 13))
                            .foregroundColor(.kgmGray)
                    }
                }
                .padding(.vertical, 6)
            }

            Section("Alışverişlerim") {
                NavigationLink(destination: OrdersView()) {
                    Label("Siparişlerim", systemImage: "shippingbox.fill")
                }
                NavigationLink(destination: FavoritesView()) {
                    Label("Favorilerim", systemImage: "heart.fill")
                }
            }

            Section {
                Button(role: .destructive) {
                    Task { await authVM.logout() }
                } label: {
                    Label("Çıkış Yap", systemImage: "rectangle.portrait.and.arrow.right")
                }
            }
        }
        .listStyle(.insetGrouped)
    }
}
