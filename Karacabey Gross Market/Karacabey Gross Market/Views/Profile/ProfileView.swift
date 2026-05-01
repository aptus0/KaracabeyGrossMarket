import SwiftUI

struct ProfileView: View {
    @EnvironmentObject private var authManager: AuthManager
    @StateObject private var authVM = AuthViewModel()
    @State private var showLogin = false
    @Environment(\.openURL) var openURL

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
            ZStack {
                Circle().fill(Color(UIColor.systemGray6)).frame(width: 120, height: 120)
                Image(systemName: "person.circle.fill")
                    .font(.system(size: 80))
                    .foregroundColor(.gray.opacity(0.4))
            }
            VStack(spacing: 8) {
                Text("Henüz giriş yapmadınız")
                    .font(.system(size: 20, weight: .bold, design: .rounded))
                Text("Siparişlerinizi takip etmek ve size özel kampanyalardan faydalanmak için giriş yapın.")
                    .font(.system(size: 15))
                    .foregroundColor(.secondary)
                    .multilineTextAlignment(.center)
                    .padding(.horizontal, 32)
            }
            
            Button { showLogin = true } label: {
                Text("Giriş Yap / Üye Ol")
                    .font(.system(size: 16, weight: .bold, design: .rounded))
                    .frame(maxWidth: .infinity)
                    .padding(.vertical, 16)
                    .background(Color.kgmOrange)
                    .foregroundColor(.white)
                    .cornerRadius(16)
                    .shadow(color: Color.kgmOrange.opacity(0.3), radius: 10, y: 4)
            }
            .padding(.horizontal, 32)
            .padding(.top, 16)
            
            Spacer()
        }
    }

    private var loggedInView: some View {
        List {
            // Avatar section
            Section {
                HStack(spacing: 16) {
                    Circle()
                        .fill(LinearGradient(colors: [.kgmOrange, .kgmOrange.opacity(0.7)], startPoint: .topLeading, endPoint: .bottomTrailing))
                        .frame(width: 64, height: 64)
                        .overlay(
                            Text(String(authManager.currentUser?.name.prefix(1) ?? "?").uppercased())
                                .font(.system(size: 26, weight: .bold, design: .rounded))
                                .foregroundColor(.white)
                        )
                    VStack(alignment: .leading, spacing: 4) {
                        Text(authManager.currentUser?.name ?? "Kullanıcı")
                            .font(.system(size: 18, weight: .bold, design: .rounded))
                        Text(authManager.currentUser?.email ?? "")
                            .font(.system(size: 14))
                            .foregroundColor(.secondary)
                    }
                }
                .padding(.vertical, 8)
            }

            Section(header: Text("Alışveriş İşlemleri").font(.system(size: 13, weight: .semibold))) {
                NavigationLink(destination: OrdersView()) {
                    Label {
                        Text("Siparişlerim").font(.system(size: 16))
                    } icon: {
                        Image(systemName: "shippingbox.fill").foregroundColor(.blue)
                    }
                }
                NavigationLink(destination: FavoritesView()) {
                    Label {
                        Text("Favorilerim").font(.system(size: 16))
                    } icon: {
                        Image(systemName: "heart.fill").foregroundColor(.red)
                    }
                }
            }

            Section(header: Text("Hesap Ayarları").font(.system(size: 13, weight: .semibold))) {
                NavigationLink(destination: AddressesView()) {
                    Label {
                        Text("Adreslerim").font(.system(size: 16))
                    } icon: {
                        Image(systemName: "map.fill").foregroundColor(.green)
                    }
                }
                NavigationLink(destination: Text("Kullanıcı Bilgilerim")) {
                    Label {
                        Text("Kullanıcı Bilgilerim").font(.system(size: 16))
                    } icon: {
                        Image(systemName: "person.text.rectangle.fill").foregroundColor(.purple)
                    }
                }
            }
            
            Section(header: Text("Destek").font(.system(size: 13, weight: .semibold))) {
                Button {
                    let whatsappURL = URL(string: "https://wa.me/905000000000?text=Merhaba,%20destek%20almak%20istiyorum.")!
                    if UIApplication.shared.canOpenURL(whatsappURL) {
                        openURL(whatsappURL)
                    }
                } label: {
                    Label {
                        Text("WhatsApp Destek Hattı").font(.system(size: 16)).foregroundColor(.primary)
                    } icon: {
                        Image(systemName: "message.fill").foregroundColor(.green)
                    }
                }
            }

            Section {
                Button(role: .destructive) {
                    UIImpactFeedbackGenerator(style: .medium).impactOccurred()
                    Task { await authVM.logout() }
                } label: {
                    HStack {
                        Spacer()
                        Text("Çıkış Yap")
                            .font(.system(size: 16, weight: .bold))
                        Spacer()
                    }
                }
            }
        }
        .listStyle(.insetGrouped)
    }
}

// Dummy views for navigation links
struct AddressesView: View {
    var body: some View {
        Text("Adreslerim").navigationTitle("Adreslerim")
    }
}
